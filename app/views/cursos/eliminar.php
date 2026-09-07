<?php
session_start();

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

$idCurso = (int) ($_POST["id_curso"] ?? 0);

if ($idCurso > 0) {
    $cursoController = new CursoController();
    try {
        $cursoController->eliminar($idCurso);
    } catch (PDOException $e) {
        die("No se pudo eliminar: probablemente tiene estudiantes o sesiones "
            . "ligados a este curso. Elimínalos primero, o dime y hacemos "
            . "un borrado en cascada. <a href='index.php'>Volver</a>");
    }
}

header("Location: index.php");
exit();