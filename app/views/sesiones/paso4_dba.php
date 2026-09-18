<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/DbaController.php";

$idCurso = (int) ($_GET["id_curso"] ?? 0);
$idMateria = (int) ($_GET["id_materia"] ?? 0);
$periodo = (int) ($_GET["periodo"] ?? 0);

$cursoController = new CursoController();
$curso = $cursoController->obtenerPorId($idCurso);

if (!$curso || (int) $curso["id_docente"] !== (int) $_SESSION["id_docente"]) {
    header("Location: paso1_curso.php");
    exit();
}

$dbaController = new DbaController();
// El grado no se vuelve a preguntar: ya viene del curso elegido en el paso 1
$dbaLista = $dbaController->listarPorMateriaGradoPeriodo($idMateria, $curso["grado"], $periodo);
?>
<?php render_head("Iniciar sesión de clase - Paso 4"); ?>
<body>
<div class="container mt-5" style="max-width: 700px;">

    <h4 class="text-muted mt-3">Paso 4 de 5</h4>
    <h2 class="mb-1">¿Qué DBA vas a trabajar?</h2>
    <p class="text-muted mb-4">Periodo <?= $periodo ?> · Grado <?= (int) $curso['grado'] ?>°</p>

    <?php if (empty($dbaLista)): ?>
        <div class="alert alert-warning">
            No hay DBA registrados para esta materia, grado y periodo.
            Verifica la tabla "dba" en tu base de datos.
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($dbaLista as $dba): ?>
                <a href="paso5_actividad.php?id_curso=<?= $idCurso ?>&id_dba=<?= (int) $dba['id_dba'] ?>"
                   class="list-group-item list-group-item-action">
                    <strong><?= htmlspecialchars($dba['codigo']) ?></strong>
                    — <?= htmlspecialchars($dba['descripcion']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</body>
</html>