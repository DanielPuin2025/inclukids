<?php
require __DIR__ . "/../../bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: paso1_curso.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/SesionClaseController.php";
require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/ProgresoController.php";

$idSesion = (int) ($_POST["id_sesion"] ?? 0);

$sesionController = new SesionClaseController();
$sesion = $sesionController->obtenerPorId($idSesion);

if (!$sesion) {
    die("Sesión no encontrada.");
}

// Un docente solo puede guardar progreso en sus propias sesiones; sin
// esto, cualquier docente autenticado podría enviar este POST con el
// id_sesion de otro y sobrescribir el progreso de estudiantes ajenos.
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
if (!$esAdmin && (int) $sesion["id_docente"] !== (int) $_SESSION["id_docente"]) {
    die("No tienes permiso para registrar progreso en esta sesión.");
}

$estadosValidos = ["Logrado", "En proceso", "Requiere apoyo"];

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listarPorCurso($sesion["id_curso"]);

$progresoController = new ProgresoController();

foreach ($estudiantes as $estudiante) {
    $idEst = (int) $estudiante["id_estudiante"];
    $estado = $_POST["estado_" . $idEst] ?? null;
    $observaciones = trim($_POST["observaciones_" . $idEst] ?? "");

    // Solo guarda si el docente sí marcó un estado para este estudiante,
    // y valida que sea uno de los 3 valores permitidos por el ENUM.
    if ($estado !== null && in_array($estado, $estadosValidos, true)) {
        $progresoController->registrar($idSesion, $idEst, $estado, $observaciones);
    }
}

header("Location: progreso_guardado.php?id_sesion=" . $idSesion);
exit();