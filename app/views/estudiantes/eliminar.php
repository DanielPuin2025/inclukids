<?php
require __DIR__ . "/../../bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/CursoController.php";

$idEstudiante = (int) ($_POST["id_estudiante"] ?? 0);
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";

$estudianteController = new EstudianteController();
$cursoController = new CursoController();

// Hay que saber a qué curso pertenece para poder devolver al
// administrador a la vista de ese mismo curso.
$idCursoDelEstudiante = 0;
$estudianteExistente = $idEstudiante > 0 ? $estudianteController->obtenerPorId($idEstudiante) : null;
if ($estudianteExistente) {
    $idCursoDelEstudiante = (int) $estudianteExistente["id_curso"];
}

$volver = $esAdmin
    ? ($idCursoDelEstudiante > 0 ? "curso.php?id_curso=$idCursoDelEstudiante" : "index.php")
    : "mis_estudiantes.php";

// Un docente solo puede retirar estudiantes de un curso a su cargo (si
// no, cualquier docente podría retirar estudiantes ajenos con solo
// cambiar el id en la URL).
if ($idEstudiante > 0 && !$esAdmin) {
    $cursoDelEstudiante = $idCursoDelEstudiante > 0 ? $cursoController->obtenerPorId($idCursoDelEstudiante) : null;
    if (!$cursoDelEstudiante || (int) $cursoDelEstudiante["id_docente"] !== (int) $_SESSION["id_docente"]) {
        flash_set("error", "No tienes permiso para retirar este estudiante.");
        header("Location: $volver");
        exit();
    }
}

if (!$estudianteExistente) {
    flash_set("error", "No se encontró ese estudiante.");
    header("Location: $volver");
    exit();
}

try {
    // Borrado suave: el estudiante se marca como retirado y deja de
    // aparecer, pero su progreso se conserva y se puede restaurar
    // desde la papelera (papelera.php).
    $estudianteController->eliminar($idEstudiante);
    auditar("retirar", "estudiante", $idEstudiante,
        $estudianteExistente["nombres"] . " " . $estudianteExistente["apellidos"]);
    flash_set(
        "exito",
        $estudianteExistente["nombres"] . " " . $estudianteExistente["apellidos"]
            . " quedó retirado. Puedes restaurarlo desde la papelera."
    );
} catch (PDOException $e) {
    error_log("IncluKids - fallo al retirar estudiante $idEstudiante: " . $e->getMessage());
    flash_set("error", "No se pudo retirar al estudiante. Inténtalo de nuevo.");
}

header("Location: $volver");
exit();
