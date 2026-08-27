<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../controllers/ActividadController.php";

$idActividad = (int) ($_GET["id_actividad"] ?? 0);
$idSesion = (int) ($_GET["id_sesion"] ?? 0);

$actividadController = new ActividadController();
$actividad = $actividadController->obtenerPorId($idActividad);

if (!$actividad) {
    die("Actividad no encontrada.");
}

// A qué motor de juego (archivo) le corresponde cada id_tipo de
// tipoactividad. Los que todavía no tienen archivo construido caen
// al mensaje de "en construcción" de abajo.
$motorPorTipo = [
    1 => "memoria.php",     // Memoria
    7 => "arrastrar.php",   // Arrastrar y soltar
    4 => "seleccionar.php", // Seleccionar (opción múltiple)
];

$archivo = $motorPorTipo[(int) $actividad["id_tipo"]] ?? null;

if ($archivo !== null && file_exists(__DIR__ . "/$archivo")) {
    header("Location: $archivo?id_actividad=$idActividad&id_sesion=$idSesion");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($actividad['nombre']) ?></title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
    <div class="alert alert-warning">
        <h5 class="alert-heading">🚧 Este juego todavía no está listo</h5>
        <p class="mb-0">
            "<?= htmlspecialchars($actividad['nombre']) ?>" está registrado,
            pero su pantalla de juego todavía no se ha construido.
        </p>
    </div>
    <a href="../sesiones/confirmacion.php?id_sesion=<?= $idSesion ?>" class="btn btn-secondary">&larr; Volver a la sesión</a>
</div>
</body>
</html>
