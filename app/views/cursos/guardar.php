<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/CursoController.php";

$idCurso = (int) ($_POST["id_curso"] ?? 0); // 0 significa "es nuevo"
$grado = (int) ($_POST["grado"] ?? 0);
$grupo = strtoupper(trim($_POST["grupo"] ?? ""));
$anio = (int) ($_POST["anio"] ?? 0);
$idDocente = (int) ($_POST["id_docente"] ?? 0);

$volver = $idCurso > 0 ? "editar.php?id_curso=$idCurso" : "nuevo.php";
if ($grado === 0 || $grupo === "" || $anio === 0 || $idDocente === 0) {
    flash_set("error", "Faltan datos: revisa grado, grupo, año y docente.");
    header("Location: $volver");
    exit();
}

$cursoController = new CursoController();

if ($idCurso === 0) {
    $cursoController->crear($grado, $grupo, $anio, $idDocente);
    flash_set("exito", "Curso {$grado}°{$grupo} creado.");
} else {
    $cursoController->actualizar($idCurso, $grado, $grupo, $anio, $idDocente);
    flash_set("exito", "Curso {$grado}°{$grupo} actualizado.");
}

header("Location: index.php");
exit();