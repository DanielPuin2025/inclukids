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

// Resumen por DBA: para cada DBA, el estado MÁS RECIENTE de cada
// estudiante (la consulta ya viene ordenada por fecha desc).
$resumenDba = [];
$vistoEstDba = [];
foreach ($registros as $r) {
    $dbaCod = $r["dba_codigo"] ?: "(sin DBA)";
    $clave = $dbaCod . "|" . $r["id_estudiante"];
    if (isset($vistoEstDba[$clave])) continue;
    $vistoEstDba[$clave] = true;
    if (!isset($resumenDba[$dbaCod])) {
        $resumenDba[$dbaCod] = [
            "desc" => $r["dba_descripcion"] ?? "",
            "Logrado" => 0, "En proceso" => 0, "Requiere apoyo" => 0,
        ];
    }
    if (isset($resumenDba[$dbaCod][$r["estado"]])) {
        $resumenDba[$dbaCod][$r["estado"]]++;
    }
}
?>
<?php render_head("Progreso de mis estudiantes - IncluKids"); ?>
<body>
<div class="container mt-5" style="max-width: 900px;">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-1 flex-wrap gap-2">
        <h2 class="mb-0">📊 Progreso de mis estudiantes</h2>
        <span class="d-flex gap-2 flex-wrap">
            <a href="cobertura.php" class="btn btn-sm btn-outline-secondary">🗺️ Cobertura de DBA</a>
            <a href="reporte_curso.php" class="btn btn-sm btn-outline-secondary">🖨️ Reporte de curso</a>
            <a href="exportar.php" class="btn btn-sm btn-outline-secondary">⬇️ Exportar CSV</a>
        </span>
    </div>
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

        <?php $barraTitulo = "Distribución — estado más reciente por estudiante"; ?>
        <?php include __DIR__ . "/_barra_estados.php"; ?>

        <h4 class="mt-4">Resumen por DBA</h4>
        <p class="text-muted small mb-2">Estado más reciente de cada estudiante en cada DBA.</p>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>DBA</th>
                        <th class="text-center">✅ Logrado</th>
                        <th class="text-center">🟡 En proceso</th>
                        <th class="text-center">🆘 Requiere apoyo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resumenDba as $cod => $fila): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($cod) ?></strong>
                            <?php if ($fila["desc"]): ?><br><span class="text-muted small"><?= htmlspecialchars($fila["desc"]) ?></span><?php endif; ?>
                        </td>
                        <td class="text-center"><?= $fila["Logrado"] ?></td>
                        <td class="text-center"><?= $fila["En proceso"] ?></td>
                        <td class="text-center"><?= $fila["Requiere apoyo"] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
