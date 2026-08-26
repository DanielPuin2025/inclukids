<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/EstudianteController.php";

$idEstudiante = (int) ($_GET["id_estudiante"] ?? 0);

if ($idEstudiante > 0) {
    $estudianteController = new EstudianteController();
    try {
        $estudianteController->eliminar($idEstudiante);
    } catch (PDOException $e) {
        die("No se pudo eliminar: probablemente tiene registros de progreso ligados. "
            . "<a href='../dashboard/home.php?modulo=estudiantes'>Volver</a>");
    }
}

header("Location: ../dashboard/home.php?modulo=estudiantes");
exit();