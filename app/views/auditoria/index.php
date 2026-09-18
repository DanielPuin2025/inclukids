<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

$entradas = auditoria_recientes(300);
?>
<?php render_head("Bitácora - IncluKids", ["css" => ["panel.css"]]); ?>
<body>
<div class="container mt-5">

    <h2 class="mt-3 mb-1">🗒️ Bitácora</h2>
    <p class="text-muted mb-4">
        Últimas acciones registradas (borrados, retiros y restauraciones).
        Las más recientes primero.
    </p>

    <div class="mb-3">
        <input type="search" class="form-control" placeholder="🔎 Buscar…"
               data-filtro-tabla="tablaAuditoria" aria-label="Buscar en la bitácora">
        <small class="text-muted" data-filtro-cuenta="tablaAuditoria"></small>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" id="tablaAuditoria">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Quién</th>
                            <th>Acción</th>
                            <th>Sobre</th>
                            <th>Detalle</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($entradas)): ?>
                        <tr><td colspan="6" class="text-center">
                            No hay entradas todavía (o la tabla <code>auditoria</code> aún no está creada:
                            aplica <code>database/migrations/2026_09_09_crear_auditoria.sql</code>).
                        </td></tr>
                        <?php else: foreach ($entradas as $e): ?>
                        <tr>
                            <td class="text-nowrap"><?= htmlspecialchars($e["fecha"]) ?></td>
                            <td><?= htmlspecialchars($e["docente_nombre"] ?: "—") ?>
                                <?php if ($e["rol"]): ?><span class="text-muted small">(<?= htmlspecialchars($e["rol"]) ?>)</span><?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($e["accion"]) ?></span></td>
                            <td><?= htmlspecialchars($e["entidad"]) ?><?= $e["id_entidad"] !== null ? " #" . (int) $e["id_entidad"] : "" ?></td>
                            <td><?= htmlspecialchars($e["detalle"] ?: "") ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($e["ip"] ?: "") ?></td>
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
