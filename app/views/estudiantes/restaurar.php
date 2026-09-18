<?php
require __DIR__ . "/../../bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: papelera.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/CursoController.php";

$idEstudiante = (int) ($_POST["id_estudiante"] ?? 0);
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";

$estudianteController = new EstudianteController();
$estudiante = $idEstudiante > 0 ? $estudianteController->obtenerPorId($idEstudiante) : null;

if (!$estudiante) {
    flash_set("error", "No se encontró ese estudiante.");
    header("Location: papelera.php");
    exit();
}

// Un docente solo puede restaurar estudiantes de un curso a su cargo.
if (!$esAdmin) {
    $cursoController = new CursoController();
    $curso = $cursoController->obtenerPorId((int) $estudiante["id_curso"]);
    if (!$curso || (int) $curso["id_docente"] !== (int) $_SESSION["id_docente"]) {
        flash_set("error", "No tienes permiso para restaurar este estudiante.");
        header("Location: papelera.php");
        exit();
    }
}

try {
    $estudianteController->restaurar($idEstudiante);
    auditar("restaurar", "estudiante", $idEstudiante,
        $estudiante["nombres"] . " " . $estudiante["apellidos"]);
    flash_set("exito", $estudiante["nombres"] . " " . $estudiante["apellidos"] . " volvió a la lista de estudiantes.");
} catch (PDOException $e) {
    error_log("IncluKids - fallo al restaurar estudiante $idEstudiante: " . $e->getMessage());
    flash_set("error", "No se pudo restaurar. Inténtalo de nuevo.");
}

header("Location: papelera.php");
exit();
