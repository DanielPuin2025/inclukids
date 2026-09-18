<?php
require __DIR__ . "/../../bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../dashboard/visualizador.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/SesionClaseController.php";

$idSesion = (int) ($_POST["id_sesion"] ?? 0);
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";

// A dónde volver después de borrar. Por defecto cada rol va a su
// panel, pero si se borra desde una vista más específica (el curso o
// la vista global de sesiones) es mejor volver ahí mismo en vez de
// mandar siempre al panel principal. Se valida contra una lista fija
// de destinos conocidos, nunca una URL libre.
$volverParam = $_POST["volver"] ?? "";
if ($esAdmin && $volverParam === "global") {
    $volver = "../progreso/global.php";
} elseif ($esAdmin && preg_match('/^curso:(\d+)$/', $volverParam, $coincidencia)) {
    $volver = "../estudiantes/curso.php?id_curso=" . (int) $coincidencia[1];
} elseif (!$esAdmin && $volverParam === "mis_sesiones") {
    $volver = "mis_sesiones.php";
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
    flash_set("error", "No tienes permiso para eliminar esta sesión.");
    header("Location: $volver");
    exit();
}

try {
    // Borra también el progreso y las actividades registradas de
    // esta sesión — no tienen ningún significado sin ella.
    $sesionController->eliminar($idSesion);
    auditar("eliminar", "sesion", $idSesion,
        ($sesion["fecha"] ?? "") . " · curso " . ($sesion["id_curso"] ?? "?"));
    flash_set("exito", "Sesión eliminada.");
} catch (PDOException $e) {
    error_log("IncluKids - no se pudo eliminar sesión $idSesion: " . $e->getMessage());
    flash_set("error", "No se pudo eliminar la sesión. Inténtalo de nuevo.");
}

header("Location: $volver");
exit();
