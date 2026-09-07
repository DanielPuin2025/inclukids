<?php
session_start();

require_once __DIR__ . "/../../helpers/sesion.php";
sesion_verificar_timeout();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../helpers/csrf.php";
require_once __DIR__ . "/../../controllers/DocenteController.php";

$docenteController = new DocenteController();
$docentes = $docenteController->listar();

$curso = null; // modo "nuevo": no hay datos previos que precargar
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo curso</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <a href="index.php" class="text-decoration-none">&larr; Volver a Cursos</a>

    <h2 class="mt-3 mb-4">Nuevo curso</h2>

    <?php include __DIR__ . "/_formulario.php"; ?>

</div>
</body>
</html>