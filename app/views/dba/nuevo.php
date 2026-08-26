<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../controllers/MateriaController.php";

$materiaController = new MateriaController();
$materias = $materiaController->listar();
$dba = null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo DBA</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
    <a href="index.php" class="text-decoration-none">&larr; Volver a DBA</a>
    <h2 class="mt-3 mb-4">Nuevo DBA</h2>
    <?php include __DIR__ . "/_formulario.php"; ?>
</div>
</body>
</html>