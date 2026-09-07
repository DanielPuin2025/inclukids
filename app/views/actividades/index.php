<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../helpers/csrf.php";
require_once __DIR__ . "/../../controllers/ActividadController.php";

$actividadController = new ActividadController();
$actividades = $actividadController->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Actividades</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

    <a href="../dashboard/home.php" class="text-decoration-none">&larr; Volver al inicio</a>

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <h2 class="mb-0">🎮 Actividades</h2>
        <a href="nuevo.php" class="btn btn-success">+ Nueva actividad</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>DBA</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($actividades)): ?>
                    <tr><td colspan="4" class="text-center">No hay actividades registradas.</td></tr>
                    <?php else: foreach ($actividades as $actividad): ?>
                    <tr>
                        <td><?= htmlspecialchars($actividad['nombre']) ?></td>
                        <td><?= htmlspecialchars($actividad['dba_codigo'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($actividad['descripcion'] ?? '') ?></td>
                        <td>
                            <a href="editar.php?id_actividad=<?= (int) $actividad['id_actividad'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            <a href="contenido.php?id_actividad=<?= (int) $actividad['id_actividad'] ?>" class="btn btn-sm btn-outline-secondary">Preguntas</a>
                            <form method="POST" action="eliminar.php" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar esta actividad?');">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id_actividad" value="<?= (int) $actividad['id_actividad'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>