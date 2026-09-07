<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/CursoController.php";

$cursoController = new CursoController();
$cursos = $cursoController->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Estudiantes - IncluKids</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
<link href="/IncluKids/public/css/a11y.css?v=2" rel="stylesheet">
<script src="/IncluKids/public/js/a11y.js?v=2" defer></script>
<link href="/IncluKids/public/css/panel.css?v=2" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

    <a href="../dashboard/home.php" class="text-decoration-none">&larr; Volver al panel</a>

    <h2 class="mt-3 mb-1">👨‍🎓 Estudiantes</h2>
    <p class="text-muted mb-4">
        Elige un curso para ver sus estudiantes y el progreso registrado en sus sesiones.
    </p>

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
