<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/ActividadController.php";
require_once __DIR__ . "/../../controllers/ContenidoActividadController.php";

$idActividad = (int) ($_GET["id_actividad"] ?? 0);

$actividadController = new ActividadController();
$actividad = $actividadController->obtenerPorId($idActividad);

if (!$actividad) { header("Location: index.php"); exit(); }

$contenidoController = new ContenidoActividadController();
$preguntas = $contenidoController->listarPorActividad($idActividad);
$siguienteOrden = count($preguntas) + 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Preguntas de <?= htmlspecialchars($actividad['nombre']) ?></title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
    <link href="/IncluKids/public/css/a11y.css?v=4" rel="stylesheet">
    <script src="/IncluKids/public/js/a11y.js?v=4" defer></script>
</head>
<body>
<div class="container mt-5" style="max-width: 700px;">

    <a href="index.php" class="text-decoration-none">&larr; Volver a Actividades</a>

    <h2 class="mt-3 mb-1">Preguntas: <?= htmlspecialchars($actividad['nombre']) ?></h2>
    <p class="text-muted mb-4">
        Estas son las preguntas que se muestran al jugar esta actividad.
    </p>

    <?php if (!empty($preguntas)): ?>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr><th>#</th><th>Pregunta</th><th>Correcta</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($preguntas as $p): ?>
                    <tr>
                        <td><?= (int) $p['orden'] ?></td>
                        <td><?= htmlspecialchars($p['pregunta']) ?></td>
                        <td><span class="badge bg-success"><?= htmlspecialchars($p['respuesta_correcta']) ?></span></td>
                        <td>
                            <form method="POST" action="eliminar_contenido.php" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar esta pregunta?');">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id_contenido" value="<?= (int) $p['id_contenido'] ?>">
                                <input type="hidden" name="id_actividad" value="<?= (int) $idActividad ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Agregar pregunta nueva</h5>

            <form method="POST" action="guardar_contenido.php">
                <?= csrf_campo() ?>
                <input type="hidden" name="id_actividad" value="<?= (int) $idActividad ?>">

                <div class="mb-3">
                    <label class="form-label">Pregunta</label>
                    <input type="text" class="form-control" name="pregunta" required>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Opción A</label>
                        <input type="text" class="form-control" name="opcion_a" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Opción B</label>
                        <input type="text" class="form-control" name="opcion_b" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Opción C (opcional)</label>
                        <input type="text" class="form-control" name="opcion_c">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Opción D (opcional)</label>
                        <input type="text" class="form-control" name="opcion_d">
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Respuesta correcta</label>
                        <select class="form-select" name="respuesta_correcta" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Orden</label>
                        <input type="number" class="form-control" name="orden" min="1" value="<?= $siguienteOrden ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100">+ Agregar pregunta</button>
            </form>
        </div>
    </div>

</div>
</body>
</html>