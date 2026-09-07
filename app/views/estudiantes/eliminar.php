<?php
session_start();

require_once __DIR__ . "/../../helpers/sesion.php";
sesion_verificar_timeout();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . "/../../helpers/csrf.php";
csrf_verificar();

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/CursoController.php";

$idEstudiante = (int) ($_POST["id_estudiante"] ?? 0);
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";

$estudianteController = new EstudianteController();
$cursoController = new CursoController();

// Hay que guardar a qué curso pertenecía ANTES de borrarlo, para
// poder devolver al administrador a la vista de ese mismo curso.
$idCursoDelEstudiante = 0;
if ($idEstudiante > 0) {
    $estudianteExistente = $estudianteController->obtenerPorId($idEstudiante);
    if ($estudianteExistente) {
        $idCursoDelEstudiante = (int) $estudianteExistente["id_curso"];
    }
}

$volverEstudiantes = $esAdmin
    ? ($idCursoDelEstudiante > 0 ? "curso.php?id_curso=$idCursoDelEstudiante" : "index.php")
    : "../dashboard/visualizador.php";

// Un docente solo puede eliminar estudiantes de un curso que tiene a
// cargo (si no, cualquier docente podría borrar estudiantes ajenos
// con solo cambiar el id en la URL).
if ($idEstudiante > 0 && !$esAdmin) {
    $cursoDelEstudiante = $idCursoDelEstudiante > 0 ? $cursoController->obtenerPorId($idCursoDelEstudiante) : null;
    if (!$cursoDelEstudiante || (int) $cursoDelEstudiante["id_docente"] !== (int) $_SESSION["id_docente"]) {
        die("No tienes permiso para eliminar este estudiante. <a href='../dashboard/visualizador.php'>Volver</a>");
    }
}

if ($idEstudiante > 0) {
    try {
        // Elimina también el progreso registrado del estudiante (ya
        // no tiene sentido guardarlo sin el estudiante al que pertenece).
        $estudianteController->eliminar($idEstudiante);
    } catch (PDOException $e) {
        die("No se pudo eliminar: " . htmlspecialchars($e->getMessage())
            . " <a href='" . htmlspecialchars($volverEstudiantes) . "'>Volver</a>");
    }
}

header("Location: $volverEstudiantes");
exit();
