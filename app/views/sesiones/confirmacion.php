<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/SesionClaseController.php";
require_once __DIR__ . "/../../controllers/SesionActividadController.php";

$idSesion = (int) ($_GET["id_sesion"] ?? 0);

$sesionController = new SesionClaseController();
$sesion = $sesionController->obtenerPorId($idSesion);

if (!$sesion) {
    header("Location: paso1_curso.php");
    exit();
}

// Un docente solo puede ver la confirmación de sus propias sesiones.
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
if (!$esAdmin && (int) $sesion["id_docente"] !== (int) $_SESSION["id_docente"]) {
    die("No tienes permiso para ver esta sesión. <a href='../dashboard/visualizador.php'>Volver</a>");
}

$sesionActividadController = new SesionActividadController();
$actividades = $sesionActividadController->listarPorSesion($idSesion);
?>
<?php render_head("Sesión iniciada"); ?>
<body>
<div class="container mt-5" style="max-width: 600px;">

    <div class="alert alert-success">
        <h4 class="alert-heading">✅ Sesión de clase iniciada</h4>
        <p class="mb-0">Fecha: <?= htmlspecialchars($sesion['fecha']) ?></p>
    </div>

    <h5>Actividades para desarrollar en esta sesión:</h5>
    <ul class="list-group mb-4">
        <?php foreach ($actividades as $actividad): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($actividad['nombre']) ?>
                <a href="../juegos/jugar.php?id_actividad=<?= (int) $actividad['id_actividad'] ?>&id_sesion=<?= $idSesion ?>"
                   class="btn btn-sm btn-success">▶️ Jugar</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <p class="text-muted">
        Desarrolla la actividad con la clase. Cuando termines, registra
        el progreso individual de cada estudiante.
    </p>

    <a href="../dashboard/visualizador.php" class="btn btn-secondary">Volver a mi panel</a>
    <a href="registrar_progreso.php?id_sesion=<?= $idSesion ?>" class="btn btn-primary">Registrar progreso</a>

</div>
</body>
</html>