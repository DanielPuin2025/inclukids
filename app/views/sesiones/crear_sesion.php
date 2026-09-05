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
require_once __DIR__ . "/../../controllers/CursoController.php";

$idCurso = (int) ($_POST["id_curso"] ?? 0);
$idDba = (int) ($_POST["id_dba"] ?? 0);
$observaciones = trim($_POST["observaciones"] ?? "");
$actividades = $_POST["actividades"] ?? [];

if ($idCurso === 0 || $idDba === 0 || empty($actividades)) {
    die("Faltan datos para crear la sesión. Vuelve atrás e inténtalo de nuevo.");
}

// Un docente solo puede crear sesiones para un curso que tiene a
// cargo; sin esto, podría enviar el POST final directo (sin pasar por
// los pasos 1-5) con el id_curso de otro docente y quedar con una
// sesión —y luego progreso registrado— sobre estudiantes ajenos.
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
if (!$esAdmin) {
    $cursoController = new CursoController();
    $curso = $cursoController->obtenerPorId($idCurso);
    if (!$curso || (int) $curso["id_docente"] !== (int) $_SESSION["id_docente"]) {
        die("No tienes permiso para crear una sesión en ese curso.");
    }
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