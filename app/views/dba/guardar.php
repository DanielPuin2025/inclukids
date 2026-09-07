<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit(); }

require_once __DIR__ . "/../../helpers/csrf.php";
csrf_verificar();

require_once __DIR__ . "/../../controllers/DbaController.php";

$idDba = (int) ($_POST["id_dba"] ?? 0);
$idMateria = (int) ($_POST["id_materia"] ?? 0);
$grado = (int) ($_POST["grado"] ?? 0);
$periodo = (int) ($_POST["periodo"] ?? 0);
$codigo = trim($_POST["codigo"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($idMateria === 0 || $grado === 0 || $periodo === 0 || $codigo === "" || $descripcion === "") {
    die("Faltan datos. <a href='index.php'>Volver</a>");
}

$dbaController = new DbaController();

if ($idDba === 0) {
    $dbaController->crear($idMateria, $grado, $periodo, $codigo, $descripcion);
} else {
    $dbaController->actualizar($idDba, $idMateria, $grado, $periodo, $codigo, $descripcion);
}

header("Location: index.php");
exit();