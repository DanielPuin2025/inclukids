<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/ProgresoController.php";

$idSesion = (int) ($_GET["id_sesion"] ?? 0);

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

    <a href="../dashboard/home.php" class="btn btn-secondary w-100">Volver al dashboard</a>

</div>
</body>
</html>