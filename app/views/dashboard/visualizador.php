<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/DbaController.php";

$idDocente = $_SESSION["id_docente"];

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listarPorDocente($idDocente);

$dbaController = new DbaController();
$dbaLista = $dbaController->listarPorDocente($idDocente);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi visualizador - IncluKids</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

    <a href="home.php" class="text-decoration-none">&larr; Volver al dashboard</a>

    <h2 class="mt-3 mb-1">👁️ Mi visualizador</h2>
    <p class="text-muted">Estudiantes y DBA de los cursos a tu cargo. Vista de solo lectura.</p>

    <h4 class="mt-4">👨‍🎓 Mis estudiantes</h4>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Curso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($estudiantes)): ?>
                    <tr>
                        <td colspan="3" class="text-center">
                            No tienes estudiantes a cargo todavía.
                        </td>
                    </tr>
                    <?php else: foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td><?= htmlspecialchars($estudiante["nombres"]) ?></td>
                        <td><?= htmlspecialchars($estudiante["apellidos"]) ?></td>
                        <td><?= (int) $estudiante["grado"] ?>°<?= htmlspecialchars($estudiante["grupo"]) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mt-4">📚 DBA correspondientes</h4>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Grado</th>
                        <th>Periodo</th>
                        <th>Código</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dbaLista)): ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            No hay DBA para los cursos a tu cargo.
                        </td>
                    </tr>
                    <?php else: foreach ($dbaLista as $dba): ?>
                    <tr>
                        <td><?= htmlspecialchars($dba["materia"] ?? "-") ?></td>
                        <td><?= (int) $dba["grado"] ?>°</td>
                        <td><?= (int) $dba["periodo"] ?></td>
                        <td><?= htmlspecialchars($dba["codigo"]) ?></td>
                        <td><?= htmlspecialchars($dba["descripcion"]) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
