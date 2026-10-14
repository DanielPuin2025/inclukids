<?php
require_once __DIR__ . "/../../helpers/session_boot.php";
sesion_iniciar();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../helpers/juegos.php";
require_once __DIR__ . "/../../controllers/ActividadController.php";

$idActividad = (int) ($_GET["id_actividad"] ?? 0);
$idSesion = (int) ($_GET["id_sesion"] ?? 0);

// Sin id_sesion: "vista previa" del juego desde el catálogo, sin
// sesión de clase detrás.
$urlVolver = juego_url_volver($idSesion);
$textoVolver = $idSesion > 0 ? "Volver a la sesión" : "Salir de la prueba";

$actividadController = new ActividadController();
$actividad = $actividadController->obtenerPorId($idActividad);

if (!$actividad) {
    die("Actividad no encontrada.");
}

// A qué motor de juego (archivo) le corresponde cada id_tipo de
// tipoactividad. Emparejar usa unir.php (tocar una tarjeta y luego su
// pareja) — mismo contenido (zonas/piezas) que arrastrar.php, pero con
// una mecánica distinta a propósito, para que no todos los juegos se
// sientan igual. Completar sí reusa arrastrar.php porque es lo mismo:
// arrastrar una pieza hasta el espacio en blanco correcto. Ordenar usa
// secuencia.php (tocar las piezas en el orden correcto, sin arrastrar)
// — mismo contenido (orden_correcto + piezas) que ordenar.php.
// Rompecabezas SÍ se queda en ordenar.php a propósito: ahí arrastrar
// tiene sentido, se siente como encajar las piezas de un rompecabezas.
$motorPorTipo = [
    1 => "memoria.php",     // Memoria
    2 => "unir.php",        // Emparejar
    3 => "ordenar.php",     // Rompecabezas
    4 => "seleccionar.php", // Seleccionar (opción múltiple)
    5 => "secuencia.php",   // Ordenar
    6 => "arrastrar.php",   // Completar (dictado con banco de palabras)
    7 => "arrastrar.php",   // Arrastrar y soltar
];

$archivo = $motorPorTipo[(int) $actividad["id_tipo"]] ?? null;

if ($archivo !== null && file_exists(__DIR__ . "/$archivo")) {
    $parametrosRetorno = "";
    if ($idSesion <= 0 && ($_GET["origen"] ?? "") === "paso5") {
        $idCurso = (int) ($_GET["id_curso"] ?? 0);
        $idDba = (int) ($_GET["id_dba"] ?? 0);
        if ($idCurso > 0 && $idDba > 0) {
            $parametrosRetorno = "&origen=paso5&id_curso=$idCurso&id_dba=$idDba";
        }
    }
    header("Location: $archivo?id_actividad=$idActividad&id_sesion=$idSesion$parametrosRetorno");
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
<link href="/IncluKids/public/css/a11y.css?v=10" rel="stylesheet">
<script src="/IncluKids/public/js/a11y.js?v=10" defer></script>
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
    <a href="<?= htmlspecialchars($urlVolver, ENT_QUOTES, "UTF-8") ?>" class="btn btn-secondary">&larr; <?= htmlspecialchars($textoVolver, ENT_QUOTES, "UTF-8") ?></a>
</div>
</body>
</html>
