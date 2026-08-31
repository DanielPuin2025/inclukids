<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/SesionClaseController.php";

$idSesion = (int) ($_GET["id_sesion"] ?? 0);
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";

$volver = $esAdmin ? "../dashboard/home.php" : "../dashboard/visualizador.php";

$sesionController = new SesionClaseController();
$sesion = $sesionController->obtenerPorId($idSesion);

if (!$sesion) {
    header("Location: $volver");
    exit();
}

// Un docente solo puede borrar sus propias sesiones; el
// administrador puede borrar cualquiera (por ejemplo, para limpiar
// sesiones de prueba).
if (!$esAdmin && (int) $sesion["id_docente"] !== (int) $_SESSION["id_docente"]) {
    die("No tienes permiso para eliminar esta sesión. <a href='" . htmlspecialchars($volver) . "'>Volver</a>");
}

try {
    // Borra también el progreso y las actividades registradas de
    // esta sesión — no tienen ningún significado sin ella.
    $sesionController->eliminar($idSesion);
} catch (PDOException $e) {
    die("No se pudo eliminar: " . htmlspecialchars($e->getMessage())
        . " <a href='" . htmlspecialchars($volver) . "'>Volver</a>");
}

header("Location: $volver");
exit();
