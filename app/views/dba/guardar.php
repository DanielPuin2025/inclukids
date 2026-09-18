<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit(); }

csrf_verificar();

require_once __DIR__ . "/../../controllers/DbaController.php";

$idDba = (int) ($_POST["id_dba"] ?? 0);
$idMateria = (int) ($_POST["id_materia"] ?? 0);
$grado = (int) ($_POST["grado"] ?? 0);
$periodo = (int) ($_POST["periodo"] ?? 0);
$codigo = trim($_POST["codigo"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

$volver = $idDba > 0 ? "editar.php?id_dba=$idDba" : "nuevo.php";
if ($idMateria === 0 || $grado === 0 || $periodo === 0 || $codigo === "" || $descripcion === "") {
    flash_set("error", "Faltan datos: revisa materia, grado, periodo, código y descripción.");
    header("Location: $volver");
    exit();
}

$dbaController = new DbaController();

if ($idDba === 0) {
    $dbaController->crear($idMateria, $grado, $periodo, $codigo, $descripcion);
    flash_set("exito", "DBA $codigo creado.");
} else {
    $dbaController->actualizar($idDba, $idMateria, $grado, $periodo, $codigo, $descripcion);
    flash_set("exito", "DBA $codigo actualizado.");
}

header("Location: index.php");
exit();