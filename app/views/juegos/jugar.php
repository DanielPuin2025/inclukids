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
// tipoactividad. Emparejar y Completar reusan arrastrar.php porque
// mecánicamente son lo mismo: arrastrar una pieza hasta la zona (o el
// espacio en blanco) correcta. Rompecabezas reusa ordenar.php porque
// las 3 actividades de ese tipo son, en el fondo, "acomoda las piezas
// en el orden correcto" con look de rompecabezas.
$motorPorTipo = [
    1 => "memoria.php",     // Memoria
    2 => "arrastrar.php",   // Emparejar
    3 => "ordenar.php",     // Rompecabezas
    4 => "seleccionar.php", // Seleccionar (opción múltiple)
    5 => "ordenar.php",     // Ordenar
    6 => "arrastrar.php",   // Completar (dictado con banco de palabras)
    7 => "arrastrar.php",   // Arrastrar y soltar
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
