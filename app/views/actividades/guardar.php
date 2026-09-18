<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit(); }

csrf_verificar();

require_once __DIR__ . "/../../controllers/ActividadController.php";

$idActividad = (int) ($_POST["id_actividad"] ?? 0);
$idDba = (int) ($_POST["id_dba"] ?? 0);
$idTipo = (int) ($_POST["id_tipo"] ?? 0);
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$instrucciones = trim($_POST["instrucciones"] ?? "");

$volver = $idActividad > 0 ? "editar.php?id_actividad=$idActividad" : "nuevo.php";
if ($idDba === 0 || $idTipo === 0 || $nombre === "") {
    flash_set("error", "Faltan datos: la actividad necesita DBA, tipo y nombre.");
    header("Location: $volver");
    exit();
}

$actividadController = new ActividadController();

if ($idActividad === 0) {
    $actividadController->crear($idDba, $idTipo, $nombre, $descripcion, $instrucciones);
    flash_set("exito", "Actividad «$nombre» creada.");
} else {
    $actividadController->actualizar($idActividad, $idDba, $idTipo, $nombre, $descripcion, $instrucciones);
    flash_set("exito", "Actividad «$nombre» actualizada.");
}

header("Location: index.php");
exit();