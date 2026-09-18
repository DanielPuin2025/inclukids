<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/SesionClaseController.php";

$idDocente = $_SESSION["id_docente"];

$sesionController = new SesionClaseController();
$sesiones = $sesionController->listarPorDocente($idDocente);
?>
<?php render_head("Mis sesiones de clase - IncluKids"); ?>
<body>
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <h2 class="mb-0">📅 Mis sesiones de clase</h2>
        <a href="paso1_curso.php" class="btn btn-primary">▶️ Iniciar sesión de clase</a>
    </div>

<?= flash_render() ?>

    <div class="mb-3">
        <input type="search" class="form-control" placeholder="🔎 Buscar…" data-filtro-tabla="tablaSesiones" aria-label="Buscar en la tabla">
        <small class="text-muted" data-filtro-cuenta="tablaSesiones"></small>
    </div>

        <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="tablaSesiones">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Curso</th>
                            <th>DBA trabajado</th>
                            <th>Progreso</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sesiones)): ?>
                        <tr>
                            <td colspan="5" class="text-center">
                                No hay sesiones registradas todavía. Usa
                                "▶️ Iniciar sesión de clase".
                            </td>
                        </tr>
                        <?php else: foreach ($sesiones as $sesion): ?>
                        <tr>
                            <td><?= htmlspecialchars($sesion["fecha"]) ?></td>
                            <td><?= $sesion["grado"] !== null ? (int) $sesion["grado"] . "°" . htmlspecialchars($sesion["grupo"]) : "-" ?></td>
                            <td><?= htmlspecialchars($sesion["dba_codigo"] ?? "-") ?></td>
                            <td>
                                <?php if ((int) ($sesion["n_progreso"] ?? 0) === 0): ?>
                                    <span class="badge bg-warning">Sin progreso</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= (int) $sesion["n_progreso"] ?> registrado<?= (int) $sesion["n_progreso"] === 1 ? "" : "s" ?></span>
                                <?php endif; ?>
                                <a href="registrar_progreso.php?id_sesion=<?= (int) $sesion['id_sesion'] ?>"
                                   class="btn btn-sm btn-primary"><?= (int) ($sesion["n_progreso"] ?? 0) === 0 ? "Registrar progreso" : "Editar progreso" ?></a>
                            </td>
                            <td class="text-nowrap">
                                <a href="duplicar.php?id_sesion=<?= (int) $sesion['id_sesion'] ?>"
                                   class="btn btn-sm btn-outline-secondary">🔁 Duplicar</a>
                                <form method="POST" action="eliminar.php" class="d-inline"
                                      data-confirmar="¿Eliminar esta sesión? También se eliminará el progreso y las actividades registradas en ella.">
                                    <?= csrf_campo() ?>
                                    <input type="hidden" name="id_sesion" value="<?= (int) $sesion['id_sesion'] ?>">
                                    <input type="hidden" name="volver" value="mis_sesiones">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                        <tr data-filtro-vacio hidden><td colspan="5" class="text-center text-muted">Sin resultados para esa búsqueda.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</body>
</html>
