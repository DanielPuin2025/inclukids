<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/CursoController.php";

$idCurso = (int) ($_POST["id_curso"] ?? 0);

if ($idCurso > 0) {
    $cursoController = new CursoController();
    try {
        $cursoController->eliminar($idCurso);
    } catch (PDOException $e) {
        error_log("IncluKids - no se pudo eliminar curso $idCurso: " . $e->getMessage());
        flash_set("error", "No se pudo eliminar el curso: todavía tiene estudiantes "
            . "o sesiones ligados. Muévelos o retíralos primero.");
        header("Location: index.php");
        exit();
    }
    auditar("eliminar", "curso", $idCurso);
    flash_set("exito", "Curso eliminado.");
}

header("Location: index.php");
exit();