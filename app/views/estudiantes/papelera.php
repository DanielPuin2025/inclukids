<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/EstudianteController.php";

$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
$idDocente = (int) $_SESSION["id_docente"];

$estudianteController = new EstudianteController();
// El admin ve todos los retirados; el docente solo los de sus cursos.
$retirados = $estudianteController->listarEliminados($esAdmin ? null : $idDocente);

$volver = $esAdmin ? "index.php" : "mis_estudiantes.php";
?>
<?php render_head("Papelera de estudiantes - IncluKids"); ?>
<body>
<div class="container mt-5">

    <h2 class="mt-3 mb-1">🗑️ Estudiantes retirados</h2>
    <p class="text-muted mb-4">
        Al retirar a un estudiante deja de aparecer en los listados, pero su
        progreso se conserva. Aquí puedes <strong>restaurarlo</strong> (vuelve
        tal cual estaba) o <strong>borrarlo definitivamente</strong> (se elimina
        junto con todo su progreso, sin vuelta atrás).
    </p>

    <?= flash_render() ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Curso</th>
                            <th>Retirado el</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($retirados)): ?>
                        <tr>
                            <td colspan="5" class="text-center">La papelera está vacía.</td>
                        </tr>
                        <?php else: foreach ($retirados as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e["nombres"]) ?></td>
                            <td><?= htmlspecialchars($e["apellidos"]) ?></td>
                            <td><?= $e["grado"] !== null ? (int) $e["grado"] . "°" . htmlspecialchars($e["grupo"]) : "-" ?></td>
                            <td><?= htmlspecialchars($e["eliminado_en"]) ?></td>
                            <td class="text-nowrap">
                                <form method="POST" action="restaurar.php" class="d-inline">
                                    <?= csrf_campo() ?>
                                    <input type="hidden" name="id_estudiante" value="<?= (int) $e["id_estudiante"] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">↩️ Restaurar</button>
                                </form>
                                <form method="POST" action="eliminar_definitivo.php" class="d-inline"
                                      data-confirmar="¿Borrar definitivamente a <?= htmlspecialchars($e["nombres"] . " " . $e["apellidos"], ENT_QUOTES) ?>? Se eliminará también todo su progreso. Esto NO se puede deshacer."
                                      data-confirmar-boton="Borrar definitivamente"
                                      data-confirmar-titulo="Borrado definitivo">
                                    <?= csrf_campo() ?>
                                    <input type="hidden" name="id_estudiante" value="<?= (int) $e["id_estudiante"] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Borrar definitivamente</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</body>
</html>
