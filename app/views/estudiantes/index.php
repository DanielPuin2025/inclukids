<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/EstudianteController.php";

$cursoController = new CursoController();
$cursos = $cursoController->listar();

$nRetirados = (new EstudianteController())->contarEliminados();
?>
<?php render_head("Estudiantes - IncluKids", ["css" => ["panel.css"]]); ?>
<body>
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-1 flex-wrap gap-2">
        <h2 class="mb-0">👨‍🎓 Estudiantes</h2>
        <a href="papelera.php" class="btn btn-sm btn-outline-secondary">
            🗑️ Retirados<?= $nRetirados > 0 ? " (" . $nRetirados . ")" : "" ?>
        </a>
    </div>
    <p class="text-muted mb-4">
        Elige un curso para ver sus estudiantes y el progreso registrado en sus sesiones.
    </p>

    <?= flash_render() ?>

    <?php if (empty($cursos)): ?>

        <div class="alert alert-warning">
            No hay cursos registrados todavía. Crea uno desde
            <a href="../cursos/nuevo.php">Cursos</a> para poder agrupar estudiantes.
        </div>

    <?php else: ?>

        <div class="panel-grid">
            <?php foreach ($cursos as $curso): ?>
            <a href="curso.php?id_curso=<?= (int) $curso['id_curso'] ?>" class="panel-tile panel-tile--estudiantes panel-tile--enlace">
                <span class="panel-tile__header">
                    <span class="panel-tile__icono">🏫</span>
                    <span class="panel-tile__texto">
                        <span class="panel-tile__titulo"><?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?></span>
                        <span class="panel-tile__subtitulo">
                            <?= (int) $curso['n_estudiantes'] ?> estudiante<?= (int) $curso['n_estudiantes'] === 1 ? '' : 's' ?>
                            <?php if ($curso['docente_nombres']): ?>
                                · <?= htmlspecialchars($curso['docente_nombres'] . ' ' . $curso['docente_apellidos']) ?>
                            <?php else: ?>
                                · sin docente asignado
                            <?php endif; ?>
                        </span>
                    </span>
                    <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
                </span>
            </a>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>
</body>
</html>
