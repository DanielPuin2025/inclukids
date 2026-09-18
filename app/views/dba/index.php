<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/DbaController.php";

$dbaController = new DbaController();
$dbaLista = $dbaController->listar();
?>
<?php render_head("DBA"); ?>
<body>
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <h2 class="mb-0">📚 DBA</h2>
        <a href="nuevo.php" class="btn btn-success">+ Nuevo DBA</a>
    </div>

<?= flash_render() ?>

<div class="mb-3">
        <input type="search" class="form-control" placeholder="🔎 Buscar…" data-filtro-tabla="tablaDba" aria-label="Buscar en la tabla">
        <small class="text-muted" data-filtro-cuenta="tablaDba"></small>
    </div>

        <div class="card">
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-bordered mb-0" id="tablaDba">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Grado</th>
                        <th>Periodo</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dbaLista)): ?>
                    <tr><td colspan="6" class="text-center">No hay DBA registrados.</td></tr>
                    <?php else: foreach ($dbaLista as $dba): ?>
                    <tr>
                        <td><?= htmlspecialchars($dba['materia'] ?? '-') ?></td>
                        <td><?= (int) $dba['grado'] ?>°</td>
                        <td><?= (int) $dba['periodo'] ?></td>
                        <td><?= htmlspecialchars($dba['codigo']) ?></td>
                        <td><?= htmlspecialchars($dba['descripcion']) ?></td>
                        <td>
                            <a href="editar.php?id_dba=<?= (int) $dba['id_dba'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form method="POST" action="eliminar.php" class="d-inline"
                                  data-confirmar="¿Eliminar este DBA? También se eliminarán sus actividades y el contenido jugable de cada una. Si el DBA o alguna de sus actividades ya se usó en una sesión de clase real, no se podrá eliminar.">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id_dba" value="<?= (int) $dba['id_dba'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                    <tr data-filtro-vacio hidden><td colspan="6" class="text-center text-muted">Sin resultados para esa búsqueda.</td></tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>
</body>
</html>