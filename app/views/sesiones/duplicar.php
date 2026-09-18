<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/SesionClaseController.php";
require_once __DIR__ . "/../../controllers/SesionActividadController.php";

/**
 * Duplicar una sesión de clase: misma sesión (curso + DBA), otra
 * fecha, sin repetir el asistente de 5 pasos. Copia las actividades
 * marcadas, no el progreso.
 *
 * GET  -> resumen de lo que se va a copiar + selector de fecha.
 * POST -> crea la nueva sesión y va a su pantalla de confirmación.
 */
$idSesion = (int) ($_GET["id_sesion"] ?? $_POST["id_sesion"] ?? 0);

$sesionController = new SesionClaseController();
$sesion = $sesionController->obtenerPorId($idSesion);

$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";

if (!$sesion || (!$esAdmin && (int) $sesion["id_docente"] !== (int) $_SESSION["id_docente"])) {
    flash_set("error", "No tienes acceso a esa sesión.");
    header("Location: mis_sesiones.php");
    exit();
}

// Datos de contexto (curso + DBA + actividades) para el resumen.
require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/DbaController.php";
$curso = (new CursoController())->obtenerPorId((int) $sesion["id_curso"]);
$dba = (new DbaController())->obtenerPorId((int) $sesion["id_dba"]);
$actividades = (new SesionActividadController())->listarPorSesion($idSesion);

$errores = [];
$fecha = $_POST["fecha"] ?? date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    csrf_verificar();

    $d = DateTime::createFromFormat("Y-m-d", $fecha);
    if (!$d || $d->format("Y-m-d") !== $fecha) {
        $errores[] = "Elige una fecha válida.";
    }

    if (!$errores) {
        $nuevoId = $sesionController->duplicar($idSesion, $fecha);
        if (!$nuevoId) {
            $errores[] = "No se pudo duplicar la sesión.";
        } else {
            flash_set("exito", "Sesión duplicada para el $fecha. Registra el progreso cuando termines la clase.");
            header("Location: confirmacion.php?id_sesion=" . (int) $nuevoId);
            exit();
        }
    }
}
?>
<?php render_head("Duplicar sesión de clase - IncluKids"); ?>
<body>
<div class="container mt-5" style="max-width: 560px;">

    <h2 class="mt-3 mb-1">🔁 Duplicar sesión de clase</h2>
    <p class="text-muted mb-4">
        Se crea una sesión nueva con el mismo curso y DBA, en la fecha
        que elijas. Se copian las actividades marcadas; el progreso
        empieza en blanco.
    </p>

    <?php if ($errores): ?>
        <div class="alert alert-danger">
            <?php foreach ($errores as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <p class="mb-1"><strong>Curso:</strong>
                <?= $curso ? (int) $curso["grado"] . "°" . htmlspecialchars($curso["grupo"]) : "-" ?></p>
            <p class="mb-1"><strong>DBA:</strong>
                <?= $dba ? htmlspecialchars($dba["codigo"] . " — " . $dba["descripcion"]) : "-" ?></p>
            <p class="mb-1"><strong>Sesión original:</strong> <?= htmlspecialchars($sesion["fecha"]) ?></p>
            <p class="mb-0"><strong>Actividades que se copian:</strong>
                <?= count($actividades) ?>
                <?php if ($actividades): ?>
                    (<?= htmlspecialchars(implode(", ", array_column($actividades, "nombre"))) ?>)
                <?php endif; ?>
            </p>
        </div>
    </div>

    <form method="POST" action="duplicar.php">
        <?= csrf_campo() ?>
        <input type="hidden" name="id_sesion" value="<?= (int) $idSesion ?>">

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha de la sesión nueva</label>
            <input type="date" class="form-control" id="fecha" name="fecha"
                   value="<?= htmlspecialchars($fecha) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Duplicar sesión</button>
    </form>

</div>
</body>
</html>
