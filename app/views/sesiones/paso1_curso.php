<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/CursoController.php";

$cursoController = new CursoController();
$cursos = $cursoController->listarPorDocente($_SESSION["id_docente"]);
?>
<?php render_head("Iniciar sesión de clase - Paso 1"); ?>
<body>
<div class="container mt-5" style="max-width: 600px;">

    <h4 class="text-muted mt-3">Paso 1 de 5</h4>
    <h2 class="mb-4">¿Con qué curso vas a trabajar?</h2>

    <?= flash_render() ?>

    <?php if (empty($cursos)): ?>
        <div class="alert alert-warning">
            No tienes cursos asignados. Pide a un administrador que
            te asigne uno en la tabla "cursos".
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($cursos as $curso): ?>
                <a href="paso2_materia.php?id_curso=<?= (int) $curso['id_curso'] ?>"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span><strong><?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?></strong></span>
                    <span class="text-muted">Año <?= (int) $curso['anio'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</body>
</html>