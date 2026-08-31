<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/ActividadController.php";

$idActividad = (int) ($_GET["id_actividad"] ?? 0);

if ($idActividad > 0) {
    $actividadController = new ActividadController();
    try {
        $actividadController->eliminar($idActividad);
    } catch (PDOException $e) {
        die("No se pudo eliminar: probablemente tiene preguntas o sesiones ligadas. "
            . "<a href='index.php'>Volver</a>");
    }
}

header("Location: index.php");
exit();