<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/DocenteController.php";

$docenteController = new DocenteController();
$docentes = $docenteController->listar();

$idAdmin = (int) $_SESSION["id_docente"];

$solicitudesPendientes = count(array_filter($docentes, fn($d) => $d["solicitud_password_en"] !== null));
?>
<?php render_head("Docentes - IncluKids"); ?>
<body>
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-1">
        <h2 class="mb-0">🧑‍🏫 Docentes</h2>
        <a href="nuevo.php" class="btn btn-success">+ Nuevo docente</a>
    </div>
    <p class="text-muted mb-4">
        El registro público está cerrado: las cuentas de docente se crean
        desde aquí. También puedes editarlas, generar una contraseña
        temporal para quien perdió la suya, o eliminarlas.
    </p>

    <?php if ($solicitudesPendientes > 0): ?>
        <div class="alert alert-warning">
            ⏳ <?= $solicitudesPendientes ?>
            <?= $solicitudesPendientes === 1 ? "docente pidió" : "docentes pidieron" ?>
            que le restablezcas la contraseña (marcados abajo).
        </div>
    <?php endif; ?>

    <?= flash_render() ?>

    <div class="mb-3">
        <input type="search" class="form-control" placeholder="🔎 Buscar…" data-filtro-tabla="tablaDocentes" aria-label="Buscar en la tabla">
        <small class="text-muted" data-filtro-cuenta="tablaDocentes"></small>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="tablaDocentes">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Solicitud</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($docentes)): ?>
                        <tr><td colspan="5" class="text-center">No hay docentes registrados.</td></tr>
                        <?php else: foreach ($docentes as $d): ?>
                        <tr<?= $d["solicitud_password_en"] !== null ? ' class="table-warning"' : '' ?>>
                            <td><?= htmlspecialchars($d["nombres"] . " " . $d["apellidos"]) ?></td>
                            <td><?= htmlspecialchars($d["correo"]) ?></td>
                            <td><?= htmlspecialchars($d["rol"]) ?></td>
                            <td>
                                <?php if ($d["solicitud_password_en"] !== null): ?>
                                    <span class="badge text-bg-warning">
                                        ⏳ <?= date("d/m/Y H:i", strtotime($d["solicitud_password_en"])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($d["rol"] === "administrador"): ?>
                                    <span class="text-muted small">—</span>
                                <?php else: ?>
                                    <a href="editar.php?id_docente=<?= (int) $d["id_docente"] ?>" class="btn btn-sm btn-outline-primary">Editar</a>

                                    <form method="POST" action="restablecer_password.php" class="d-inline"
                                          data-confirmar="¿Generar una contraseña temporal para <?= htmlspecialchars($d["nombres"] . " " . $d["apellidos"], ENT_QUOTES) ?>? La contraseña actual dejará de servir."
                                          data-confirmar-boton="Restablecer"
                                          data-confirmar-titulo="Restablecer contraseña">
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="id_docente" value="<?= (int) $d["id_docente"] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🔑 Restablecer contraseña</button>
                                    </form>

                                    <form method="POST" action="eliminar.php" class="d-inline"
                                          data-confirmar="¿Eliminar la cuenta de <?= htmlspecialchars($d["nombres"] . " " . $d["apellidos"], ENT_QUOTES) ?>? No podrá volver a iniciar sesión."
                                          data-confirmar-boton="Eliminar"
                                          data-confirmar-titulo="Eliminar docente">
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="id_docente" value="<?= (int) $d["id_docente"] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Eliminar</button>
                                    </form>
                                <?php endif; ?>
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
