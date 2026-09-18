<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit(); }

csrf_verificar();

require_once __DIR__ . "/../../controllers/DbaController.php";

$idDba = (int) ($_POST["id_dba"] ?? 0);

if ($idDba > 0) {
    $dbaController = new DbaController();

    if ($dbaController->tieneUsoReal($idDba)) {
        flash_set("error", "No se puede eliminar: este DBA (o una actividad suya) ya se "
            . "usó en una sesión de clase real y se perdería ese historial.");
        header("Location: index.php");
        exit();
    }

    try {
        // Cascada segura: solo llega aquí si nada de esto está ligado
        // a una sesión de clase real (ya se validó arriba).
        $dbaController->eliminarConActividades($idDba);
    } catch (PDOException $e) {
        error_log("IncluKids - no se pudo eliminar DBA $idDba: " . $e->getMessage());
        flash_set("error", "No se pudo eliminar el DBA. Inténtalo de nuevo.");
        header("Location: index.php");
        exit();
    }
    auditar("eliminar", "dba", $idDba);
    flash_set("exito", "DBA eliminado, junto con sus actividades.");
}

header("Location: index.php");
exit();
