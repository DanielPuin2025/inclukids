<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../controllers/ProgresoController.php";

$idDocente = $_SESSION["id_docente"];

$progresoController = new ProgresoController();
$registros = $progresoController->listarPorDocente($idDocente);

// Agrupa por estudiante. Como la consulta ya viene ordenada por
// nombre y luego por fecha descendente, el primer registro que
// encontramos para cada estudiante es su estado más reciente.
$porEstudiante = [];
foreach ($registros as $r) {
    $id = (int) $r["id_estudiante"];
    if (!isset($porEstudiante[$id])) {
        $porEstudiante[$id] = [
            "nombres" => $r["nombres"],
            "apellidos" => $r["apellidos"],
            "mas_reciente" => $r,
            "historial" => [],
        ];
    }
    $porEstudiante[$id]["historial"][] = $r;
}

$conteo = ["Logrado" => 0, "En proceso" => 0, "Requiere apoyo" => 0];
foreach ($porEstudiante as $est) {
    $estado = $est["mas_reciente"]["estado"];
    if (isset($conteo[$estado])) {
        $conteo[$estado]++;
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
</head>
<body>
<div class="container mt-5" style="max-width: 900px;">

    <a href="../dashboard/visualizador.php" class="text-decoration-none">&larr; Volver a mi panel</a>

    <h2 class="mt-3 mb-1">📊 Progreso de mis estudiantes</h2>
    <p class="text-muted mb-4">Estado más reciente registrado en cada sesión de clase.</p>

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
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>🟡 En proceso</h5>
                        <h2><?= $conteo["En proceso"] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card ficha-ciencias">
                    <div class="card-body text-center">
                        <h5>🆘 Requiere apoyo</h5>
                        <h2><?= $conteo["Requiere apoyo"] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <h4>Estado más reciente por estudiante</h4>
        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>DBA trabajado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($porEstudiante as $est): $r = $est["mas_reciente"]; ?>
                        <tr>
                            <td><?= htmlspecialchars($est["nombres"] . " " . $est["apellidos"]) ?></td>
                            <td>
                                <span class="badge bg-<?= $colores[$r["estado"]] ?? "secondary" ?>">
                                    <?= htmlspecialchars($r["estado"]) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($r["fecha"]) ?></td>
                            <td><?= htmlspecialchars($r["dba_codigo"] ?? "-") ?></td>
                            <td><?= htmlspecialchars($r["observaciones"] ?? "") ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <details>
            <summary class="mb-3" style="cursor: pointer;">
                <strong>Ver historial completo (<?= count($registros) ?> registros)</strong>
            </summary>

            <div class="card mt-2">
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Estudiante</th>
                                <th>Curso</th>
                                <th>DBA</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r["fecha"]) ?></td>
                                <td><?= htmlspecialchars($r["nombres"] . " " . $r["apellidos"]) ?></td>
                                <td><?= $r["grado"] !== null ? (int) $r["grado"] . "°" . htmlspecialchars($r["grupo"]) : "-" ?></td>
                                <td><?= htmlspecialchars($r["dba_codigo"] ?? "-") ?></td>
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
        </details>

    <?php endif; ?>

</div>
</body>
</html>
