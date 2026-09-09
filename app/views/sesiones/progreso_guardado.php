<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/ProgresoController.php";
require_once __DIR__ . "/../../controllers/SesionClaseController.php";

$idSesion = (int) ($_GET["id_sesion"] ?? 0);

$sesionController = new SesionClaseController();
$sesion = $sesionController->obtenerPorId($idSesion);

if (!$sesion) {
    header("Location: ../dashboard/visualizador.php");
    exit();
}

// Un docente solo puede ver el progreso guardado de sus propias
// sesiones.
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
if (!$esAdmin && (int) $sesion["id_docente"] !== (int) $_SESSION["id_docente"]) {
    die("No tienes permiso para ver esta sesión. <a href='../dashboard/visualizador.php'>Volver</a>");
}

$progresoController = new ProgresoController();
$registros = $progresoController->listarPorSesion($idSesion);

$colores = ["Logrado" => "success", "En proceso" => "warning", "Requiere apoyo" => "danger"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Progreso guardado</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
    <link href="/IncluKids/public/css/a11y.css?v=4" rel="stylesheet">
    <script src="/IncluKids/public/js/a11y.js?v=4" defer></script>
</head>
<body>
<div class="container mt-5" style="max-width: 600px;">

    <div class="alert alert-success">
        <h4 class="alert-heading">✅ Progreso guardado</h4>
        <p class="mb-0">Se registró el avance de <?= count($registros) ?> estudiante(s).</p>
    </div>

    <ul class="list-group mb-4">
        <?php foreach ($registros as $registro): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($registro['nombres'] . " " . $registro['apellidos']) ?>
                <span class="badge bg-<?= $colores[$registro['estado']] ?? 'secondary' ?>">
                    <?= htmlspecialchars($registro['estado']) ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="../dashboard/visualizador.php" class="btn btn-secondary w-100">Volver a mi panel</a>

</div>
</body>
</html>