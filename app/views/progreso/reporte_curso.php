<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/ProgresoController.php";
require_once __DIR__ . "/../../controllers/DocenteController.php";

/**
 * Reporte imprimible de un curso: estudiantes, resumen de progreso por
 * DBA (estado más reciente de cada estudiante) e historial de sesiones.
 * Pensado para "Imprimir / Guardar como PDF".
 *
 * Role-aware: el admin elige cualquier curso; el docente solo los
 * suyos (chequeo de permiso en servidor).
 */
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
$idDocente = (int) $_SESSION["id_docente"];
$idCurso = (int) ($_GET["id_curso"] ?? 0);

$cursoController = new CursoController();
$cursos = $esAdmin
    ? $cursoController->listar()
    : $cursoController->listarPorDocente($idDocente);

$curso = null;
$estudiantes = [];
$registrosProgreso = [];
$docenteCargo = null;

if ($idCurso > 0) {
    $curso = $cursoController->obtenerPorId($idCurso);

    if (!$curso || (!$esAdmin && (int) $curso["id_docente"] !== $idDocente)) {
        flash_set("error", "No tienes acceso a ese curso.");
        header("Location: reporte_curso.php");
        exit();
    }

    $estudiantes = (new EstudianteController())->listarPorCurso($idCurso);
    $registrosProgreso = (new ProgresoController())->listarPorCurso($idCurso);

    if ($curso["id_docente"]) {
        $docenteCargo = (new DocenteController())->obtenerPorId((int) $curso["id_docente"]);
    }
}

// Resumen por DBA: para cada DBA, el estado MÁS RECIENTE de cada
// estudiante (la consulta ya viene ordenada por fecha desc).
$resumenDba = [];
$vistoEstDba = [];
$conteo = ["Logrado" => 0, "En proceso" => 0, "Requiere apoyo" => 0];
foreach ($registrosProgreso as $r) {
    $dbaCod = $r["dba_codigo"] ?: "(sin DBA)";
    $clave = $dbaCod . "|" . $r["id_estudiante"];
    if (isset($vistoEstDba[$clave])) {
        continue;
    }
    $vistoEstDba[$clave] = true;

    if (!isset($resumenDba[$dbaCod])) {
        $resumenDba[$dbaCod] = [
            "desc" => $r["dba_descripcion"] ?? "",
            "Logrado" => 0, "En proceso" => 0, "Requiere apoyo" => 0,
        ];
    }
    if (isset($resumenDba[$dbaCod][$r["estado"]])) {
        $resumenDba[$dbaCod][$r["estado"]]++;
        $conteo[$r["estado"]]++;
    }
}

$fechaGeneracion = date("d/m/Y");

$estiloReporte = <<<'HTML'
<style>
    .reporte-acciones { margin: 24px 0; }
    .reporte-encabezado {
        display: flex; align-items: center; gap: 16px;
        border-bottom: 3px solid var(--petroleo, #2B5F63);
        padding-bottom: 16px; margin-bottom: 20px;
    }
    .reporte-encabezado img { width: 56px; height: 56px; object-fit: contain; border-radius: 12px; }
    .reporte-encabezado h1 { font-size: 22px; margin: 0; }
    .reporte-encabezado p { margin: 0; color: #5B6B66; font-size: 14px; }
    .reporte-resumen {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 12px; margin-bottom: 24px;
    }
    .reporte-resumen .card { text-align: center; padding: 14px 8px; }
    .reporte-resumen .card .num { font-size: 28px; font-weight: 700; line-height: 1; }
    .reporte-resumen .card .label { font-size: 13px; color: #5B6B66; margin-top: 4px; }
    .reporte-tabla th { background: #EEF2ED; }
    .reporte-pie { margin-top: 24px; font-size: 12px; color: #5B6B66; }

    @media print {
        .no-imprimir { display: none !important; }
        body { padding: 0; }
        .container { max-width: 100% !important; }
        .card { border: 1px solid #ccc !important; }
    }
</style>
HTML;

render_head(
    ($curso ? (int) $curso["grado"] . "°" . $curso["grupo"] . " - " : "")
        . "Reporte de curso - IncluKids",
    ["css" => ["panel.css"], "head_extra" => $estiloReporte]
);
?>
<body>
<div class="container mt-5" style="max-width: 900px;">

    <?php if (!$curso): ?>

        <h2 class="mt-3 mb-1">🖨️ Reporte de curso</h2>
        <p class="text-muted mb-4">Elige un curso para ver su reporte imprimible.</p>
        <?= flash_render() ?>

        <?php if (empty($cursos)): ?>
            <div class="alert alert-warning">
                <?= $esAdmin ? 'No hay cursos registrados todavía.' : 'No tienes cursos a tu cargo todavía.' ?>
            </div>
        <?php else: ?>
            <div class="panel-grid">
                <?php foreach ($cursos as $c): ?>
                <a href="reporte_curso.php?id_curso=<?= (int) $c['id_curso'] ?>"
                   class="panel-tile panel-tile--progreso panel-tile--enlace">
                    <span class="panel-tile__header">
                        <span class="panel-tile__icono">🏫</span>
                        <span class="panel-tile__texto">
                            <span class="panel-tile__titulo"><?= (int) $c['grado'] ?>°<?= htmlspecialchars($c['grupo']) ?></span>
                            <span class="panel-tile__subtitulo">Ver reporte imprimible</span>
                        </span>
                        <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>

        <div class="no-imprimir reporte-acciones d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="reporte_curso.php" class="btn btn-outline-secondary btn-sm">← Cambiar de curso</a>
            <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir / Guardar como PDF</button>
        </div>

        <div class="reporte-encabezado">
            <img src="/IncluKids/public/img/logo-inclukids.jpg" alt="IncluKids">
            <div>
                <h1>Reporte de curso</h1>
                <p>Megacolegio El Progreso · Generado el <?= $fechaGeneracion ?></p>
            </div>
        </div>

        <table class="table table-borderless mb-4" style="max-width: 520px;">
            <tbody>
                <tr>
                    <td class="text-muted" style="width: 170px;">Curso</td>
                    <td><strong><?= (int) $curso["grado"] ?>°<?= htmlspecialchars($curso["grupo"]) ?></strong>
                        · año <?= (int) $curso["anio"] ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Docente a cargo</td>
                    <td><?= $docenteCargo ? htmlspecialchars($docenteCargo["nombres"] . " " . $docenteCargo["apellidos"]) : "Sin asignar" ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Estudiantes</td>
                    <td><?= count($estudiantes) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="reporte-resumen">
            <div class="card ficha-lenguaje">
                <div class="num">✅ <?= $conteo["Logrado"] ?></div>
                <div class="label">Logrado</div>
            </div>
            <div class="card">
                <div class="num">🟡 <?= $conteo["En proceso"] ?></div>
                <div class="label">En proceso</div>
            </div>
            <div class="card ficha-ciencias">
                <div class="num">🆘 <?= $conteo["Requiere apoyo"] ?></div>
                <div class="label">Requiere apoyo</div>
            </div>
        </div>
        <p class="text-muted small mb-4">Conteo del estado más reciente de cada estudiante en cada DBA.</p>

        <h5 class="mb-2">Estudiantes (<?= count($estudiantes) ?>)</h5>
        <?php if (empty($estudiantes)): ?>
            <p class="text-muted">Este curso no tiene estudiantes registrados.</p>
        <?php else: ?>
            <div class="table-responsive mb-4">
                <table class="table table-bordered reporte-tabla mb-0">
                    <thead><tr><th>Nombres</th><th>Apellidos</th></tr></thead>
                    <tbody>
                        <?php foreach ($estudiantes as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e["nombres"]) ?></td>
                            <td><?= htmlspecialchars($e["apellidos"]) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <h5 class="mb-2">Resumen por DBA</h5>
        <?php if (empty($resumenDba)): ?>
            <p class="text-muted">Todavía no se ha registrado progreso en este curso.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered reporte-tabla">
                    <thead>
                        <tr>
                            <th>DBA</th>
                            <th class="text-center">✅</th>
                            <th class="text-center">🟡</th>
                            <th class="text-center">🆘</th>
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
        <?php endif; ?>

        <p class="reporte-pie">
            Reporte generado automáticamente por IncluKids. El estado de cada DBA
            refleja la observación del docente en la última sesión, no una
            evaluación formal.
        </p>

    <?php endif; ?>

</div>
</body>
</html>
