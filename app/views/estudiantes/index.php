<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/EstudianteController.php";

$controller = new EstudianteController();

$estudiantes = $controller->listar();

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<title>Estudiantes</title>

<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Listado de estudiantes</h2>

<table class="table table-bordered">

<thead>

<tr>

<th>ID</th>

<th>Nombres</th>

<th>Apellidos</th>

<th>Curso</th>

</tr>

</thead>

<tbody>

<?php if (empty($estudiantes)): ?>

<tr>
<td colspan="4" class="text-center">No hay estudiantes registrados.</td>
</tr>

<?php else: foreach($estudiantes as $estudiante){ ?>

<tr>

<td><?= (int) $estudiante["id_estudiante"] ?></td>

<td><?= htmlspecialchars($estudiante["nombres"]) ?></td>

<td><?= htmlspecialchars($estudiante["apellidos"]) ?></td>

<td><?= $estudiante["grado"] !== null ? (int) $estudiante["grado"] . "°" . htmlspecialchars($estudiante["grupo"]) : "Sin curso" ?></td>

</tr>

<?php } endif; ?>

</tbody>

</table>

</div>

</body>

</html>