<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/ProgresoController.php";
require_once __DIR__ . "/../../controllers/DocenteController.php";

$idEstudiante = (int) ($_GET["id_estudiante"] ?? 0);
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";

$estudianteController = new EstudianteController();
$estudiante = $estudianteController->obtenerPorId($idEstudiante);

if (!$estudiante) {
    header("Location: " . ($esAdmin ? "../estudiantes/index.php" : "../estudiantes/mis_estudiantes.php"));
    exit();
}

$cursoController = new CursoController();
$curso = $cursoController->obtenerPorId((int) $estudiante["id_curso"]);

// Un docente solo puede ver el reporte de estudiantes de un curso que
// tiene a cargo; el administrador puede ver cualquiera.
if (!$esAdmin) {
    if (!$curso || (int) $curso["id_docente"] !== (int) $_SESSION["id_docente"]) {
        die("No tienes permiso para ver este reporte. <a href='../dashboard/visualizador.php'>Volver</a>");
    }
}

$docente = null;
if ($curso && $curso["id_docente"]) {
    $docenteController = new DocenteController();
    $docente = $docenteController->obtenerPorId($curso["id_docente"]);
}

$progresoController = new ProgresoController();
$registros = $progresoController->listarPorEstudiante($idEstudiante);

$conteo = ["Logrado" => 0, "En proceso" => 0, "Requiere apoyo" => 0];
foreach ($registros as $r) {
    if (isset($conteo[$r["estado"]])) {
        $conteo[$r["estado"]]++;
    }
}
$totalRegistros = count($registros);

$colores = ["Logrado" => "success", "En proceso" => "warning", "Requiere apoyo" => "danger"];

$volver = $esAdmin
    ? (($curso) ? "../estudiantes/curso.php?id_curso=" . (int) $curso["id_curso"] : "../estudiantes/index.php")
    : "../estudiantes/mis_estudiantes.php";

$fechaGeneracion = date("d/m/Y");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reporte de <?= htmlspecialchars($estudiante["nombres"] . " " . $estudiante["apellidos"]) ?> - IncluKids</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
<link href="/IncluKids/public/css/a11y.css?v=3" rel="stylesheet">
<script src="/IncluKids/public/js/a11y.js?v=3" defer></script>
<style>
    .reporte-acciones { margin: 24px 0; }
    .reporte-encabezado {
        display: flex;
        align-items: center;
        gap: 16px;
        border-bottom: 3px solid var(--petroleo, #2B5F63);
        padding-bottom: 16px;
        margin-bottom: 20px;
    }
    .reporte-encabezado img { width: 56px; height: 56px; object-fit: contain; border-radius: 12px; }
    .reporte-encabezado h1 { font-size: 22px; margin: 0; }
    .reporte-encabezado p { margin: 0; color: #5B6B66; font-size: 14px; }
    .reporte-resumen {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
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
</head>
<body>
<div class="container mt-5" style="max-width: 800px;">

    <div class="no-imprimir reporte-acciones d-flex justify-content-between align-items-center">
        <a href="<?= htmlspecialchars($volver) ?>" class="text-decoration-none">&larr; Volver</a>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir / Guardar como PDF</button>
    </div>

    <div class="reporte-encabezado">
        <img src="/IncluKids/public/img/logo-inclukids.jpg" alt="IncluKids">
        <div>
            <h1>Reporte de progreso</h1>
            <p>Megacolegio El Progreso · Generado el <?= $fechaGeneracion ?></p>
        </div>
    </div>

    <table class="table table-borderless mb-4" style="max-width: 500px;">
        <tbody>
            <tr>
                <td class="text-muted" style="width: 160px;">Estudiante</td>
                <td><strong><?= htmlspecialchars($estudiante["nombres"] . " " . $estudiante["apellidos"]) ?></strong></td>
            </tr>
            <tr>
                <td class="text-muted">Curso</td>
                <td><?= $curso ? ((int) $curso["grado"] . "°" . htmlspecialchars($curso["grupo"]) . " · año " . (int) $curso["anio"]) : "Sin curso asignado" ?></td>
            </tr>
            <tr>
                <td class="text-muted">Docente a cargo</td>
                <td><?= $docente ? htmlspecialchars($docente["nombres"] . " " . $docente["apellidos"]) : "Sin asignar" ?></td>
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

    <h5 class="mb-3">Historial de sesiones (<?= $totalRegistros ?>)</h5>

    <?php if (empty($registros)): ?>
        <div class="alert alert-warning">
            Todavía no se ha registrado progreso para este estudiante.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered reporte-tabla">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Materia / DBA</th>
                        <th>Actividades</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $r): ?>
                    <tr>
                        <td class="text-nowrap"><?= htmlspecialchars($r["fecha"]) ?></td>
                        <td>
                            <?php if ($r["dba_codigo"]): ?>
                                <div><strong><?= htmlspecialchars($r["materia"] ?? "") ?></strong></div>
                                <div class="small text-muted"><?= htmlspecialchars($r["dba_codigo"]) ?> — <?= htmlspecialchars($r["dba_descripcion"] ?? "") ?></div>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r["actividades"] ?? "—") ?></td>
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
    <?php endif; ?>

    <p class="reporte-pie">
        Reporte generado automáticamente por IncluKids. El estado de cada sesión refleja la observación
        del docente en el momento de la actividad, no una evaluación formal.
    </p>

</div>
</body>
</html>
