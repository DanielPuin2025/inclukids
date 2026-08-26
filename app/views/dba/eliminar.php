<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../controllers/DbaController.php";

$idDba = (int) ($_GET["id_dba"] ?? 0);

if ($idDba > 0) {
    $dbaController = new DbaController();
    try {
        $dbaController->eliminar($idDba);
    } catch (PDOException $e) {
        die("No se pudo eliminar: probablemente tiene actividades ligadas a este DBA. "
            . "<a href='index.php'>Volver</a>");
    }
}

header("Location: index.php");
exit();