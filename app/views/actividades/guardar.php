<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit(); }

require_once __DIR__ . "/../../controllers/ActividadController.php";

$idActividad = (int) ($_POST["id_actividad"] ?? 0);
$idDba = (int) ($_POST["id_dba"] ?? 0);
$idTipo = (int) ($_POST["id_tipo"] ?? 0);
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$instrucciones = trim($_POST["instrucciones"] ?? "");

if ($idDba === 0 || $idTipo === 0 || $nombre === "") {
    die("Faltan datos. <a href='index.php'>Volver</a>");
}

$actividadController = new ActividadController();

if ($idActividad === 0) {
    $actividadController->crear($idDba, $idTipo, $nombre, $descripcion, $instrucciones);
} else {
    $actividadController->actualizar($idActividad, $idDba, $idTipo, $nombre, $descripcion, $instrucciones);
}

header("Location: index.php");
exit();