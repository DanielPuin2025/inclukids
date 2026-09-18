<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/DbaController.php";

/**
 * Cobertura de DBA por curso: qué DBA del grado ya se trabajaron en
 * una sesión de clase de ese curso y cuáles siguen pendientes.
 * El administrador puede ver cualquier curso; el docente, solo los
 * que tiene a cargo.
 */
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
$idDocente = (int) $_SESSION["id_docente"];
$idCurso = (int) ($_GET["id_curso"] ?? 0);

$cursoController = new CursoController();
$cursos = $esAdmin
    ? $cursoController->listar()
    : $cursoController->listarPorDocente($idDocente);

$curso = null;
$cobertura = [];

if ($idCurso > 0) {
    $curso = $cursoController->obtenerPorId($idCurso);

    if (!$curso || (!$esAdmin && (int) $curso["id_docente"] !== $idDocente)) {
        flash_set("error", "No tienes acceso a ese curso.");
        header("Location: cobertura.php");
        exit();
    }

    $cobertura = (new DbaController())->coberturaPorCurso($idCurso);
}

// Agrupa: materia -> periodo -> [DBA...], y lleva el total trabajado.
$porMateria = [];
$totalDba = 0;
$totalTrabajados = 0;
foreach ($cobertura as $d) {
    $materia = $d["materia"] ?? "(sin materia)";
    $periodo = (int) $d["periodo"];
    $porMateria[$materia][$periodo][] = $d;
    $totalDba++;
    if ((int) $d["n_sesiones"] > 0) {
        $totalTrabajados++;
    }
}
$totalPendientes = $totalDba - $totalTrabajados;
?>
<?php render_head("Cobertura de DBA - IncluKids", ["css" => ["panel.css"]]); ?>
<body>
<div class="container mt-5" style="max-width: 900px;">

    <h2 class="mt-3 mb-1">🗺️ Cobertura de DBA por curso</h2>
    <p class="text-muted mb-4">
        Qué DBA del grado ya se trabajaron en una sesión de clase de este curso
        y cuáles siguen pendientes.
    </p>

    <?= flash_render() ?>

    <?php if (!$curso): ?>

        <?php if (empty($cursos)): ?>
            <div class="alert alert-warning">
                <?= $esAdmin
                    ? 'No hay cursos registrados todavía.'
                    : 'No tienes cursos a tu cargo todavía.' ?>
            </div>
        <?php else: ?>
            <h5 class="mb-3">Elige un curso</h5>
            <div class="panel-grid">
                <?php foreach ($cursos as $c): ?>
                <a href="cobertura.php?id_curso=<?= (int) $c['id_curso'] ?>"
                   class="panel-tile panel-tile--dba panel-tile--enlace">
                    <span class="panel-tile__header">
                        <span class="panel-tile__icono">🏫</span>
                        <span class="panel-tile__texto">
                            <span class="panel-tile__titulo"><?= (int) $c['grado'] ?>°<?= htmlspecialchars($c['grupo']) ?></span>
                            <span class="panel-tile__subtitulo">Ver cobertura de DBA</span>
                        </span>
                        <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h4 class="mb-0">🏫 <?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?></h4>
            <a href="cobertura.php" class="btn btn-sm btn-outline-secondary">Cambiar de curso</a>
        </div>

        <?php if ($totalDba === 0): ?>
            <div class="alert alert-warning">
                No hay DBA registrados para <?= (int) $curso['grado'] ?>.º grado.
            </div>
        <?php else: ?>

            <p class="mb-4">
                <span class="mini-badge mini-badge--logrado">✅ <?= $totalTrabajados ?> trabajados</span>
                <span class="mini-badge mini-badge--apoyo">⬜ <?= $totalPendientes ?> pendientes</span>
                <span class="text-muted small">de <?= $totalDba ?> DBA del grado</span>
            </p>

            <?php foreach ($porMateria as $materia => $periodos): ?>
                <h5 class="mt-4 mb-2"><?= htmlspecialchars($materia) ?></h5>
                <?php ksort($periodos); ?>
                <?php foreach ($periodos as $periodo => $lista): ?>
                    <div class="progreso-sesion">
                        <div class="progreso-sesion__titulo">Periodo <?= (int) $periodo ?></div>
                        <ul class="progreso-sesion__lista">
                            <?php foreach ($lista as $d): ?>
                            <?php $trabajado = (int) $d["n_sesiones"] > 0; ?>
                            <li>
                                <span>
                                    <?= $trabajado ? "✅" : "⬜" ?>
                                    <strong><?= htmlspecialchars($d["codigo"]) ?></strong>
                                    — <?= htmlspecialchars($d["descripcion"]) ?>
                                </span>
                                <?php if ($trabajado): ?>
                                    <span class="mini-badge mini-badge--logrado">
                                        <?= (int) $d["n_sesiones"] ?> sesi<?= (int) $d["n_sesiones"] === 1 ? "ón" : "ones" ?>
                                    </span>
                                <?php else: ?>
                                    <span class="mini-badge mini-badge--apoyo">pendiente</span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>

        <?php endif; ?>

    <?php endif; ?>

</div>
</body>
</html>
