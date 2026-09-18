<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/EstudianteController.php";

$idDocente = $_SESSION["id_docente"];

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listarPorDocente($idDocente);
$nRetirados = $estudianteController->contarEliminados($idDocente);
?>
<?php render_head("Mis estudiantes - IncluKids"); ?>
<body>
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4 flex-wrap gap-2">
        <h2 class="mb-0">👨‍🎓 Mis estudiantes</h2>
        <span class="d-flex gap-2 flex-wrap">
            <a href="papelera.php" class="btn btn-outline-secondary">
                🗑️ Retirados<?= $nRetirados > 0 ? " (" . $nRetirados . ")" : "" ?>
            </a>
            <a href="importar.php" class="btn btn-outline-secondary">📋 Importar</a>
            <a href="nuevo.php" class="btn btn-success">+ Nuevo estudiante</a>
        </span>
    </div>

    <?= flash_render() ?>

<div class="mb-3">
        <input type="search" class="form-control" placeholder="🔎 Buscar…" data-filtro-tabla="tablaMisEst" aria-label="Buscar en la tabla">
        <small class="text-muted" data-filtro-cuenta="tablaMisEst"></small>
    </div>

        <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="tablaMisEst">
                    <thead>
                        <tr>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Curso</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estudiantes)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No tienes estudiantes a cargo todavía.</td>
                        </tr>
                        <?php else: foreach ($estudiantes as $estudiante): ?>
                        <tr>
                            <td><?= htmlspecialchars($estudiante["nombres"]) ?></td>
                            <td><?= htmlspecialchars($estudiante["apellidos"]) ?></td>
                            <td><?= (int) $estudiante["grado"] ?>°<?= htmlspecialchars($estudiante["grupo"]) ?></td>
                            <td class="text-nowrap">
                                <a href="editar.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="../progreso/reporte_estudiante.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-secondary">📄 Reporte</a>
                                <form method="POST" action="eliminar.php" class="d-inline"
                                      data-confirmar="¿Retirar a <?= htmlspecialchars($estudiante["nombres"] . " " . $estudiante["apellidos"], ENT_QUOTES) ?>? Deja de aparecer en la lista, pero su progreso se guarda y puedes restaurarlo desde la papelera."
                                      data-confirmar-boton="Retirar"
                                      data-confirmar-titulo="Retirar estudiante">
                                    <?= csrf_campo() ?>
                                    <input type="hidden" name="id_estudiante" value="<?= (int) $estudiante['id_estudiante'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Retirar</button>
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
