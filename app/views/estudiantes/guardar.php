<?php
require __DIR__ . "/../../bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: nuevo.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/CursoController.php";

$idEstudiante = (int) ($_POST["id_estudiante"] ?? 0); // 0 = es nuevo
$nombres = trim($_POST["nombres"] ?? "");
$apellidos = trim($_POST["apellidos"] ?? "");
$idCurso = (int) ($_POST["id_curso"] ?? 0);

$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
$volverEstudiantes = $esAdmin
    ? ($idCurso > 0 ? "curso.php?id_curso=$idCurso" : "index.php")
    : "../dashboard/visualizador.php";

if ($nombres === "" || $apellidos === "" || $idCurso === 0) {
    die("Faltan datos. <a href='" . htmlspecialchars($volverEstudiantes) . "'>Volver</a>");
}

$estudianteController = new EstudianteController();
$cursoController = new CursoController();

// Un docente solo puede guardar estudiantes en un curso que tiene a
// cargo, y solo puede editar estudiantes que ya pertenecían a uno de
// sus cursos. Esto se valida aquí (no solo en el formulario) porque
// guardar.php se puede invocar directo por POST sin pasar por él.
if (!$esAdmin) {
    $cursoDestino = $cursoController->obtenerPorId($idCurso);
    if (!$cursoDestino || (int) $cursoDestino["id_docente"] !== (int) $_SESSION["id_docente"]) {
        die("No tienes permiso para usar ese curso. <a href='" . htmlspecialchars($volverEstudiantes) . "'>Volver</a>");
    }

    if ($idEstudiante > 0) {
        $estudianteExistente = $estudianteController->obtenerPorId($idEstudiante);
        $cursoActual = $estudianteExistente ? $cursoController->obtenerPorId((int) $estudianteExistente["id_curso"]) : null;
        if (!$cursoActual || (int) $cursoActual["id_docente"] !== (int) $_SESSION["id_docente"]) {
            die("No tienes permiso para editar este estudiante. <a href='" . htmlspecialchars($volverEstudiantes) . "'>Volver</a>");
        }
    }
}

if ($idEstudiante === 0) {
    $estudianteController->crear($nombres, $apellidos, $idCurso);
} else {
    $estudianteController->actualizar($idEstudiante, $nombres, $apellidos, $idCurso);
}

header("Location: $volverEstudiantes");
exit();
