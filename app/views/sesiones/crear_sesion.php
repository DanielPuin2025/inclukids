<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: paso1_curso.php");
    exit();
}

require_once __DIR__ . "/../../controllers/SesionClaseController.php";
require_once __DIR__ . "/../../controllers/SesionActividadController.php";

$idCurso = (int) ($_POST["id_curso"] ?? 0);
$idDba = (int) ($_POST["id_dba"] ?? 0);
$observaciones = trim($_POST["observaciones"] ?? "");
$actividades = $_POST["actividades"] ?? [];

if ($idCurso === 0 || $idDba === 0 || empty($actividades)) {
    die("Faltan datos para crear la sesión. Vuelve atrás e inténtalo de nuevo.");
}

$sesionController = new SesionClaseController();
$idSesion = $sesionController->crear(
    $_SESSION["id_docente"],
    $idCurso,
    $idDba,
    date("Y-m-d"),
    $observaciones
);

$sesionActividadController = new SesionActividadController();
foreach ($actividades as $idActividad) {
    $sesionActividadController->registrar($idSesion, (int) $idActividad);
}

header("Location: confirmacion.php?id_sesion=" . (int) $idSesion);
exit();