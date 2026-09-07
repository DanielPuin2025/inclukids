<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/SesionClaseController.php";
require_once __DIR__ . "/../../controllers/ProgresoController.php";
require_once __DIR__ . "/../../controllers/DocenteController.php";

$idDocenteFiltro = (int) ($_GET["id_docente"] ?? 0);
$filtro = $idDocenteFiltro > 0 ? $idDocenteFiltro : null;

$docenteController = new DocenteController();
$docentes = $docenteController->listar();

$sesionController = new SesionClaseController();
$sesiones = $sesionController->listarTodo($filtro);

$progresoController = new ProgresoController();
$registrosProgreso = $progresoController->listarTodo($filtro);

// El progreso se agrupa por sesión (como en el resto de la app). No
// todas las sesiones tienen progreso registrado todavía, así que se
// arma a partir de TODAS las sesiones (no solo las que tienen
// progreso) para que ninguna quede oculta.
$progresoPorSesion = [];
foreach ($registrosProgreso as $r) {
    $idSesionProgreso = (int) $r["id_sesion"];
    if (!isset($progresoPorSesion[$idSesionProgreso])) {
        $progresoPorSesion[$idSesionProgreso] = [];
    }
    $progresoPorSesion[$idSesionProgreso][] = $r;
}

$docentesActivos = [];
$sesionesSinProgreso = 0;
foreach ($sesiones as $s) {
    if ($s["id_docente"]) {
        $docentesActivos[(int) $s["id_docente"]] = true;
    }
    if (empty($progresoPorSesion[(int) $s["id_sesion"]])) {
        $sesionesSinProgreso++;
    }
}

$colores = ["Logrado" => "success", "En proceso" => "warning", "Requiere apoyo" => "danger"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sesiones y progreso - IncluKids</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
<link href="/IncluKids/public/css/a11y.css?v=2" rel="stylesheet">
<script src="/IncluKids/public/js/a11y.js?v=2" defer></script>
<link href="/IncluKids/public/css/panel.css?v=2" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 1000px;">

    <a href="../dashboard/home.php" class="text-decoration-none">&larr; Volver al panel</a>

    <h2 class="mt-3 mb-1">📊 Sesiones y progreso</h2>
    <p class="text-muted mb-4">Vista global de todas las sesiones de clase registradas, de todos los docentes.</p>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card" style="cursor: default;">
                <div class="stat-card__etiqueta">Sesiones</div>
                <div class="stat-card__numero"><?= count($sesiones) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="cursor: default;">
                <div class="stat-card__etiqueta">Docentes activos</div>
                <div class="stat-card__numero"><?= count($docentesActivos) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="cursor: default;">
                <div class="stat-card__etiqueta">Sin progreso</div>
                <div class="stat-card__numero"><?= $sesionesSinProgreso ?></div>
            </div>
        </div>
    </div>

    <form method="GET" class="mb-4 d-flex align-items-end gap-2 flex-wrap">
        <div>
            <label class="form-label mb-1" for="id_docente">Filtrar por docente</label>
            <select class="form-select" id="id_docente" name="id_docente" onchange="this.form.submit()">
                <option value="0">Todos los docentes</option>
                <?php foreach ($docentes as $d): ?>
                    <option value="<?= (int) $d['id_docente'] ?>" <?= ($idDocenteFiltro === (int) $d['id_docente']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['nombres'] . ' ' . $d['apellidos']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($idDocenteFiltro > 0): ?>
            <a href="global.php" class="btn btn-outline-secondary">Quitar filtro</a>
        <?php endif; ?>
    </form>

    <?php if (empty($sesiones)): ?>

        <div class="alert alert-warning">
            <?= $idDocenteFiltro > 0
                ? 'Ese docente todavía no ha registrado ninguna sesión de clase.'
                : 'Todavía no se ha registrado ninguna sesión de clase en el sistema.' ?>
        </div>

    <?php else: ?>

        <?php foreach ($sesiones as $sesion): ?>
        <?php $idSesionCard = (int) $sesion["id_sesion"]; ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h6 class="mb-1">
                            📅 <?= htmlspecialchars($sesion["fecha"]) ?>
                            <?php if ($sesion["grado"] !== null): ?>
                                · <?= (int) $sesion["grado"] ?>°<?= htmlspecialchars($sesion["grupo"]) ?>
                            <?php endif; ?>
                        </h6>
                        <p class="text-muted small mb-0">
                            👤 <?= $sesion["docente_nombres"]
                                ? htmlspecialchars($sesion["docente_nombres"] . ' ' . $sesion["docente_apellidos"])
                                : '(sin docente)' ?>
                        </p>
                        <?php if ($sesion["dba_codigo"]): ?>
                        <p class="text-muted small mb-0">
                            <strong><?= htmlspecialchars($sesion["dba_codigo"]) ?></strong>
                            — <?= htmlspecialchars($sesion["dba_descripcion"] ?? "") ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="../sesiones/eliminar.php" class="d-inline"
                          onsubmit="return confirm('¿Eliminar esta sesión? También se eliminará el progreso y las actividades registradas en ella.');">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id_sesion" value="<?= (int) $idSesionCard ?>">
                        <input type="hidden" name="volver" value="global">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar sesión</button>
                    </form>
                </div>

                <?php if (empty($progresoPorSesion[$idSesionCard])): ?>
                    <p class="text-muted small mt-3 mb-0">Todavía no se ha registrado progreso en esta sesión.</p>
                <?php else: ?>
                <table class="table table-sm table-bordered mb-0 mt-3">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($progresoPorSesion[$idSesionCard] as $r): ?>
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
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>
</body>
</html>
