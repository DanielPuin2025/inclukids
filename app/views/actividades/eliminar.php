<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit(); }

csrf_verificar();

require_once __DIR__ . "/../../controllers/ActividadController.php";

$idActividad = (int) ($_POST["id_actividad"] ?? 0);

if ($idActividad > 0) {
    $actividadController = new ActividadController();
    try {
        $actividadController->eliminar($idActividad);
    } catch (PDOException $e) {
        error_log("IncluKids - no se pudo eliminar actividad $idActividad: " . $e->getMessage());
        flash_set("error", "No se pudo eliminar la actividad: probablemente ya se usó "
            . "en una sesión de clase.");
        header("Location: index.php");
        exit();
    }
    auditar("eliminar", "actividad", $idActividad);
    flash_set("exito", "Actividad eliminada.");
}

header("Location: index.php");
exit();