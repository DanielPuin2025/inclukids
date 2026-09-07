<?php
session_start();

require_once __DIR__ . "/../../helpers/sesion.php";
sesion_verificar_timeout();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . "/../../helpers/csrf.php";
csrf_verificar();

require_once __DIR__ . "/../../controllers/CursoController.php";

$idCurso = (int) ($_POST["id_curso"] ?? 0); // 0 significa "es nuevo"
$grado = (int) ($_POST["grado"] ?? 0);
$grupo = strtoupper(trim($_POST["grupo"] ?? ""));
$anio = (int) ($_POST["anio"] ?? 0);
$idDocente = (int) ($_POST["id_docente"] ?? 0);

if ($grado === 0 || $grupo === "" || $anio === 0 || $idDocente === 0) {
    die("Faltan datos. <a href='index.php'>Volver</a>");
}

$cursoController = new CursoController();

if ($idCurso === 0) {
    $cursoController->crear($grado, $grupo, $anio, $idDocente);
} else {
    $cursoController->actualizar($idCurso, $grado, $grupo, $anio, $idDocente);
}

header("Location: index.php");
exit();