<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/DbaController.php";
require_once __DIR__ . "/../../controllers/TipoActividadController.php";

$dbaController = new DbaController();
$dbaLista = $dbaController->listar();

$tipoController = new TipoActividadController();
$tipos = $tipoController->listar();

$actividad = null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nueva actividad</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 600px;">
    <a href="index.php" class="text-decoration-none">&larr; Volver a Actividades</a>
    <h2 class="mt-3 mb-4">Nueva actividad</h2>
    <?php include __DIR__ . "/_formulario.php"; ?>
</div>
</body>
</html>