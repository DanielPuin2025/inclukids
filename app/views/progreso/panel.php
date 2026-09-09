<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/ProgresoController.php";

$idDocente = $_SESSION["id_docente"];

$progresoController = new ProgresoController();
$registros = $progresoController->listarPorDocente($idDocente);

// El progreso se lleva por sesión (un registro independiente por
// estudiante en cada una), así que la vista principal se organiza
// por sesión, no como un estado único por estudiante.
$porSesion = [];
foreach ($registros as $r) {
    $idSesion = (int) $r["id_sesion"];
    if (!isset($porSesion[$idSesion])) {
        $porSesion[$idSesion] = [
            "fecha" => $r["fecha"],
            "grado" => $r["grado"],
            "grupo" => $r["grupo"],
            "dba_codigo" => $r["dba_codigo"],
            "dba_descripcion" => $r["dba_descripcion"],
            "estudiantes" => [],
        ];
    }
    $porSesion[$idSesion]["estudiantes"][] = $r;
}

// Estado más reciente por estudiante, solo para las tarjetas de
// resumen (la consulta ya viene ordenada por fecha descendente, así
// que el primer registro de cada estudiante es el más nuevo).
$masRecientePorEstudiante = [];
foreach ($registros as $r) {
    $id = (int) $r["id_estudiante"];
    if (!isset($masRecientePorEstudiante[$id])) {
        $masRecientePorEstudiante[$id] = $r;
    }
}

$conteo = ["Logrado" => 0, "En proceso" => 0, "Requiere apoyo" => 0];
foreach ($masRecientePorEstudiante as $r) {
    if (isset($conteo[$r["estado"]])) {
        $conteo[$r["estado"]]++;
    }
}

$colores = ["Logrado" => "success", "En proceso" => "warning", "Requiere apoyo" => "danger"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Progreso de mis estudiantes - IncluKids</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
<link href="/IncluKids/public/css/a11y.css?v=4" rel="stylesheet">
<script src="/IncluKids/public/js/a11y.js?v=4" defer></script>
</head>
<body>
<div class="container mt-5" style="max-width: 900px;">

    <a href="../dashboard/visualizador.php" class="text-decoration-none">&larr; Volver a mi panel</a>

    <h2 class="mt-3 mb-1">📊 Progreso de mis estudiantes</h2>
    <p class="text-muted mb-4">
        El progreso se lleva sesión por sesión: cada estudiante puede
        tener un registro distinto en cada una.
    </p>

    <?php if (empty($registros)): ?>

        <div class="alert alert-warning">
            Todavía no has registrado progreso de ningún estudiante.
            Usa "Registrar progreso" desde una de tus sesiones de clase
            para empezar.
        </div>

    <?php else: ?>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card ficha-lenguaje">
                    <div class="card-body text-center">
                        <h5>✅ Logrado</h5>
                        <h2><?= $conteo["Logrado"] ?></h2>
                        <p class="text-muted small mb-0">Estado más reciente por estudiante</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>🟡 En proceso</h5>
                        <h2><?= $conteo["En proceso"] ?></h2>
                        <p class="text-muted small mb-0">Estado más reciente por estudiante</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card ficha-ciencias">
                    <div class="card-body text-center">
                        <h5>🆘 Requiere apoyo</h5>
                        <h2><?= $conteo["Requiere apoyo"] ?></h2>
                        <p class="text-muted small mb-0">Estado más reciente por estudiante</p>
                    </div>
                </div>
            </div>
        </div>

        <h4>Progreso por sesión</h4>

        <?php foreach ($porSesion as $sesion): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="mb-1">
                    📅 <?= htmlspecialchars($sesion["fecha"]) ?>
                    <?php if ($sesion["grado"] !== null): ?>
                        · <?= (int) $sesion["grado"] ?>°<?= htmlspecialchars($sesion["grupo"]) ?>
                    <?php endif; ?>
                </h6>
                <?php if ($sesion["dba_codigo"]): ?>
                <p class="text-muted small mb-3">
                    <strong><?= htmlspecialchars($sesion["dba_codigo"]) ?></strong>
                    — <?= htmlspecialchars($sesion["dba_descripcion"] ?? "") ?>
                </p>
                <?php endif; ?>

                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sesion["estudiantes"] as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r["nombres"] . " " . $r["apellidos"]) ?></td>
                            <td>
                                <span class="badge bg-<?= $colores[$r["estado"]] ?? "secondary" ?>">
                                    <?= htmlspecialchars($r["estado"]) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($r["observaciones"] ?? "") ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>
</body>
</html>
