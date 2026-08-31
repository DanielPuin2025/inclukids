<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/DbaController.php";

$idDba = (int) ($_GET["id_dba"] ?? 0);

if ($idDba > 0) {
    $dbaController = new DbaController();

    if ($dbaController->tieneUsoReal($idDba)) {
        die("No se pudo eliminar: este DBA (o una actividad suya) ya se usó en una "
            . "sesión de clase real, así que no se puede borrar sin perder ese historial. "
            . "<a href='index.php'>Volver</a>");
    }

    try {
        // Cascada segura: solo llega aquí si nada de esto está ligado
        // a una sesión de clase real (ya se validó arriba).
        $dbaController->eliminarConActividades($idDba);
    } catch (PDOException $e) {
        die("No se pudo eliminar: " . htmlspecialchars($e->getMessage())
            . " <a href='index.php'>Volver</a>");
    }
}

header("Location: index.php");
exit();
