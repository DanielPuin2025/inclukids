<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/MateriaController.php";

$idCurso = (int) ($_GET["id_curso"] ?? 0);
$idMateria = (int) ($_GET["id_materia"] ?? 0);

$cursoController = new CursoController();
$curso = $cursoController->obtenerPorId($idCurso);

$materiaController = new MateriaController();
$materia = $materiaController->obtenerPorId($idMateria);

if (!$curso || !$materia || (int) $curso["id_docente"] !== (int) $_SESSION["id_docente"]) {
    header("Location: paso1_curso.php");
    exit();
}
?>
<?php render_head("Iniciar sesión de clase - Paso 3"); ?>
<body>
<div class="container mt-5" style="max-width: 600px;">

    <h4 class="text-muted mt-3">Paso 3 de 5</h4>
    <h2 class="mb-1">¿Qué periodo académico?</h2>
    <p class="text-muted mb-4">
        <?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?> ·
        <?= htmlspecialchars($materia['nombre']) ?>
    </p>

    <div class="row g-3">
        <?php for ($p = 1; $p <= 4; $p++): ?>
        <div class="col-6">
            <a href="paso4_dba.php?id_curso=<?= $idCurso ?>&id_materia=<?= $idMateria ?>&periodo=<?= $p ?>"
               class="btn btn-outline-primary w-100 py-4 fs-4">
                Periodo <?= $p ?>
            </a>
        </div>
        <?php endfor; ?>
    </div>

</div>
</body>
</html>