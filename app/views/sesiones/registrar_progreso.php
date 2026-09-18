<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/SesionClaseController.php";
require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/ProgresoController.php";

$idSesion = (int) ($_GET["id_sesion"] ?? 0);

$sesionController = new SesionClaseController();
$sesion = $sesionController->obtenerPorId($idSesion);

if (!$sesion) {
    header("Location: paso1_curso.php");
    exit();
}

// Un docente solo puede registrar progreso en sus propias sesiones;
// si no, podría ver y calificar estudiantes de otro docente con solo
// cambiar el id_sesion en la URL.
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
if (!$esAdmin && (int) $sesion["id_docente"] !== (int) $_SESSION["id_docente"]) {
    die("No tienes permiso para registrar progreso en esta sesión. <a href='../dashboard/visualizador.php'>Volver</a>");
}

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listarPorCurso($sesion["id_curso"]);

// Progreso que YA se registró en esta sesión, por estudiante: sirve
// para dejar el formulario pre-marcado y que la misma pantalla valga
// para corregir, no solo para registrar por primera vez.
$progresoPrevio = [];
foreach ((new ProgresoController())->listarPorSesion($idSesion) as $p) {
    $progresoPrevio[(int) $p["id_estudiante"]] = $p;
}
$hayProgresoPrevio = !empty($progresoPrevio);
?>
<?php render_head("Registrar progreso"); ?>
<body>
<div class="container mt-5" style="max-width: 700px;">

    <h2 class="mt-3 mb-1">Registrar progreso</h2>
    <p class="text-muted mb-4">Marca el avance de cada estudiante en esta sesión (fecha: <?= htmlspecialchars($sesion['fecha']) ?>)</p>

    <?php if ($hayProgresoPrevio): ?>
        <div class="alert alert-success">
            Esta sesión ya tiene progreso registrado. Los cambios que guardes
            aquí <strong>reemplazan</strong> lo anterior (no se duplica).
        </div>
    <?php endif; ?>

    <?php if (empty($estudiantes)): ?>
        <div class="alert alert-warning">
            Este curso no tiene estudiantes registrados. Ve a
            "Estudiantes" para agregar algunos primero.
        </div>
    <?php else: ?>
        <form method="POST" action="guardar_progreso.php">
            <?= csrf_campo() ?>
            <input type="hidden" name="id_sesion" value="<?= $idSesion ?>">

            <?php foreach ($estudiantes as $estudiante): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($estudiante['nombres'] . " " . $estudiante['apellidos']) ?></h5>

                        <?php
                        $idEst = (int) $estudiante['id_estudiante'];
                        $previo = $progresoPrevio[$idEst] ?? null;
                        $estadoPrevio = $previo["estado"] ?? null;
                        ?>
                        <div class="btn-group w-100 mb-2" role="group">
                            <?php
                            $estados = ["Logrado" => "success", "En proceso" => "warning", "Requiere apoyo" => "danger"];
                            foreach ($estados as $estado => $color):
                            ?>
                                <input type="radio" class="btn-check"
                                       name="estado_<?= $idEst ?>"
                                       id="estado_<?= $idEst ?>_<?= md5($estado) ?>"
                                       value="<?= $estado ?>" required
                                       <?= $estadoPrevio === $estado ? 'checked' : '' ?>>
                                <label class="btn btn-outline-<?= $color ?>"
                                       for="estado_<?= $idEst ?>_<?= md5($estado) ?>">
                                    <?= $estado ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <input type="text" class="form-control form-control-sm"
                               name="observaciones_<?= $idEst ?>"
                               placeholder="Observaciones (opcional)"
                               value="<?= htmlspecialchars($previo["observaciones"] ?? '') ?>">
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <?= $hayProgresoPrevio ? "Guardar cambios" : "Guardar progreso de todos" ?>
            </button>
        </form>
    <?php endif; ?>

</div>
</body>
</html>