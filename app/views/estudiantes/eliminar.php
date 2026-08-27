<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/EstudianteController.php";

$idEstudiante = (int) ($_GET["id_estudiante"] ?? 0);
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";

$estudianteController = new EstudianteController();

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
