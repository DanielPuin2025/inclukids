<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/SesionClaseController.php";
require_once __DIR__ . "/../../controllers/EstudianteController.php";

$idSesion = (int) ($_GET["id_sesion"] ?? 0);

$sesionController = new SesionClaseController();
$sesion = $sesionController->obtenerPorId($idSesion);

if (!$sesion) {
    header("Location: paso1_curso.php");
    exit();
}

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listarPorCurso($sesion["id_curso"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar progreso</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 700px;">

    <a href="../dashboard/visualizador.php" class="text-decoration-none">&larr; Volver a mi panel</a>

    <h2 class="mt-3 mb-1">Registrar progreso</h2>
    <p class="text-muted mb-4">Marca el avance de cada estudiante en esta sesión (fecha: <?= htmlspecialchars($sesion['fecha']) ?>)</p>

    <?php if (empty($estudiantes)): ?>
        <div class="alert alert-warning">
            Este curso no tiene estudiantes registrados. Ve a
            "Estudiantes" para agregar algunos primero.
        </div>
    <?php else: ?>
        <form method="POST" action="guardar_progreso.php">
            <input type="hidden" name="id_sesion" value="<?= $idSesion ?>">

            <?php foreach ($estudiantes as $estudiante): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($estudiante['nombres'] . " " . $estudiante['apellidos']) ?></h5>

                        <div class="btn-group w-100 mb-2" role="group">
                            <?php
                            $idEst = (int) $estudiante['id_estudiante'];
                            $estados = ["Logrado" => "success", "En proceso" => "warning", "Requiere apoyo" => "danger"];
                            foreach ($estados as $estado => $color):
                            ?>
                                <input type="radio" class="btn-check"
                                       name="estado_<?= $idEst ?>"
                                       id="estado_<?= $idEst ?>_<?= md5($estado) ?>"
                                       value="<?= $estado ?>" required>
                                <label class="btn btn-outline-<?= $color ?>"
                                       for="estado_<?= $idEst ?>_<?= md5($estado) ?>">
                                    <?= $estado ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <input type="text" class="form-control form-control-sm"
                               name="observaciones_<?= $idEst ?>"
                               placeholder="Observaciones (opcional)">
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                Guardar progreso de todos
            </button>
        </form>
    <?php endif; ?>

</div>
</body>
</html>