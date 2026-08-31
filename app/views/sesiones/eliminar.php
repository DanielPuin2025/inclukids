<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/SesionClaseController.php";

$idSesion = (int) ($_GET["id_sesion"] ?? 0);
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";

// A dónde volver después de borrar. Por defecto cada rol va a su
// panel, pero si se borra desde una vista más específica (el curso o
// la vista global de sesiones) es mejor volver ahí mismo en vez de
// mandar siempre al panel principal. Se valida contra una lista fija
// de destinos conocidos, nunca una URL libre.
$volverParam = $_GET["volver"] ?? "";
if ($esAdmin && $volverParam === "global") {
    $volver = "../progreso/global.php";
} elseif ($esAdmin && preg_match('/^curso:(\d+)$/', $volverParam, $coincidencia)) {
    $volver = "../estudiantes/curso.php?id_curso=" . (int) $coincidencia[1];
} else {
    $volver = $esAdmin ? "../dashboard/home.php" : "../dashboard/visualizador.php";
}

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
