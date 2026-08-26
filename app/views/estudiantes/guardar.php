<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: nuevo.php");
    exit();
}

require_once __DIR__ . "/../../controllers/EstudianteController.php";

$idEstudiante = (int) ($_POST["id_estudiante"] ?? 0); // 0 = es nuevo
$nombres = trim($_POST["nombres"] ?? "");
$apellidos = trim($_POST["apellidos"] ?? "");
$idCurso = (int) ($_POST["id_curso"] ?? 0);

if ($nombres === "" || $apellidos === "" || $idCurso === 0) {
    die("Faltan datos. <a href='../dashboard/home.php?modulo=estudiantes'>Volver</a>");
}

$estudianteController = new EstudianteController();

if ($idEstudiante === 0) {
    $estudianteController->crear($nombres, $apellidos, $idCurso);
} else {
    $estudianteController->actualizar($idEstudiante, $nombres, $apellidos, $idCurso);
}

header("Location: ../dashboard/home.php?modulo=estudiantes");
exit();