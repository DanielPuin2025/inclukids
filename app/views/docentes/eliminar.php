<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/DocenteController.php";

$idDocente = (int) ($_POST["id_docente"] ?? 0);

$docenteController = new DocenteController();
$docente = $docenteController->obtenerPorId($idDocente);

if (!$docente) {
    flash_set("error", "Ese docente no existe.");
    header("Location: index.php");
    exit();
}

if ($docente["rol"] === "administrador") {
    flash_set("error", "Las cuentas de administrador no se eliminan desde aquí.");
    header("Location: index.php");
    exit();
}

try {
    $docenteController->eliminar($idDocente);
} catch (PDOException $e) {
    error_log("IncluKids - no se pudo eliminar docente $idDocente: " . $e->getMessage());
    flash_set("error", "No se pudo eliminar: el docente todavía tiene cursos o "
        . "sesiones de clase a su cargo. Reasígnalos primero.");
    header("Location: index.php");
    exit();
}

auditar("eliminar", "docente", $idDocente,
    trim(($docente["nombres"] ?? "") . " " . ($docente["apellidos"] ?? "")) . " (" . $docente["correo"] . ")");

flash_set("exito", "Docente eliminado.");
header("Location: index.php");
exit();
