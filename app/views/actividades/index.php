<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/ActividadController.php";

$actividadController = new ActividadController();
$actividades = $actividadController->listar();
?>
<?php render_head("Actividades"); ?>
<body>
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <h2 class="mb-0">🎮 Actividades</h2>
        <a href="nuevo.php" class="btn btn-success">+ Nueva actividad</a>
    </div>

<?= flash_render() ?>

<div class="mb-3">
        <input type="search" class="form-control" placeholder="🔎 Buscar…" data-filtro-tabla="tablaActividades" aria-label="Buscar en la tabla">
        <small class="text-muted" data-filtro-cuenta="tablaActividades"></small>
    </div>

        <div class="card">
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-bordered mb-0" id="tablaActividades">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>DBA</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($actividades)): ?>
                    <tr><td colspan="4" class="text-center">No hay actividades registradas.</td></tr>
                    <?php else: foreach ($actividades as $actividad): ?>
                    <tr>
                        <td><?= htmlspecialchars($actividad['nombre']) ?></td>
                        <td><?= htmlspecialchars($actividad['dba_codigo'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($actividad['descripcion'] ?? '') ?></td>
                        <td>
                            <a href="../juegos/jugar.php?id_actividad=<?= (int) $actividad['id_actividad'] ?>" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener">▶️ Probar</a>
                            <a href="editar.php?id_actividad=<?= (int) $actividad['id_actividad'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            <a href="contenido.php?id_actividad=<?= (int) $actividad['id_actividad'] ?>" class="btn btn-sm btn-outline-secondary">Preguntas</a>
                            <form method="POST" action="eliminar.php" class="d-inline"
                                  data-confirmar="¿Eliminar esta actividad?">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id_actividad" value="<?= (int) $actividad['id_actividad'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                    <tr data-filtro-vacio hidden><td colspan="4" class="text-center text-muted">Sin resultados para esa búsqueda.</td></tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>
</body>
</html>