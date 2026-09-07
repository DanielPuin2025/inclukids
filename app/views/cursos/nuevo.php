<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

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
    <link href="/IncluKids/public/css/a11y.css?v=2" rel="stylesheet">
    <script src="/IncluKids/public/js/a11y.js?v=2" defer></script>
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <a href="index.php" class="text-decoration-none">&larr; Volver a Cursos</a>

    <h2 class="mt-3 mb-4">Nuevo curso</h2>

    <?php include __DIR__ . "/_formulario.php"; ?>

</div>
</body>
</html>