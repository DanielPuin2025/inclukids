<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/DbaController.php";
require_once __DIR__ . "/../../controllers/ActividadController.php";
require_once __DIR__ . "/../../controllers/CursoController.php";

$idCurso = (int) ($_GET["id_curso"] ?? 0);
$idDba = (int) ($_GET["id_dba"] ?? 0);

$cursoController = new CursoController();
$curso = $cursoController->obtenerPorId($idCurso);

$dbaController = new DbaController();
$dba = $dbaController->obtenerPorId($idDba);

if (!$curso || !$dba || (int) $curso["id_docente"] !== (int) $_SESSION["id_docente"]) {
    header("Location: paso1_curso.php");
    exit();
}

$actividadController = new ActividadController();
$actividades = $actividadController->listarPorDba($idDba);
?>
<?php render_head("Iniciar sesión de clase - Paso 5"); ?>
<body>
<div class="container mt-5" style="max-width: 700px;">

    <h4 class="text-muted mt-3">Paso 5 de 5</h4>
    <h2 class="mb-1">¿Qué actividad(es) van a hacer?</h2>
    <p class="text-muted mb-4">DBA: <strong><?= htmlspecialchars($dba['codigo']) ?></strong> — <?= htmlspecialchars($dba['descripcion']) ?></p>

    <?php if (empty($actividades)): ?>
        <div class="alert alert-warning">
            Este DBA todavía no tiene actividades asociadas en la
            tabla "actividades". Agrega al menos una para poder continuar.
        </div>
    <?php else: ?>
        <form method="POST" action="crear_sesion.php">
            <?= csrf_campo() ?>
            <input type="hidden" name="id_curso" value="<?= $idCurso ?>">
            <input type="hidden" name="id_dba" value="<?= $idDba ?>">

            <div class="mb-3">
                <?php foreach ($actividades as $actividad): ?>
                    <div class="form-check border rounded p-3 mb-2">
                        <input class="form-check-input" type="checkbox"
                               name="actividades[]"
                               value="<?= (int) $actividad['id_actividad'] ?>"
                               id="act<?= (int) $actividad['id_actividad'] ?>">
                        <label class="form-check-label" for="act<?= (int) $actividad['id_actividad'] ?>">
                            <strong><?= htmlspecialchars($actividad['nombre']) ?></strong><br>
                            <span class="text-muted small"><?= htmlspecialchars($actividad['descripcion'] ?? '') ?></span>
                        </label>
                        <a href="../juegos/jugar.php?id_actividad=<?= (int) $actividad['id_actividad'] ?>"
                           class="btn btn-sm btn-outline-secondary float-end" target="_blank" rel="noopener">▶️ Probar</a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones generales (opcional)</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                Iniciar sesión de clase
            </button>
        </form>
    <?php endif; ?>

</div>
</body>
</html>