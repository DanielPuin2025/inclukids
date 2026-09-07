<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/MateriaController.php";

$idCurso = (int) ($_GET["id_curso"] ?? 0);

$cursoController = new CursoController();
$curso = $cursoController->obtenerPorId($idCurso);

// Seguridad básica: que el curso exista y sea de este docente
if (!$curso || (int) $curso["id_docente"] !== (int) $_SESSION["id_docente"]) {
    header("Location: paso1_curso.php");
    exit();
}

$materiaController = new MateriaController();
$materias = $materiaController->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar sesión de clase - Paso 2</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 600px;">

    <a href="paso1_curso.php" class="text-decoration-none">&larr; Cambiar curso</a>

    <h4 class="text-muted mt-3">Paso 2 de 5</h4>
    <h2 class="mb-1">¿Qué materia van a trabajar?</h2>
    <p class="text-muted mb-4">Curso: <?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?></p>

    <?php if (empty($materias)): ?>
        <div class="alert alert-warning">
            No hay materias registradas en la tabla "materias".
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($materias as $materia): ?>
                <a href="paso3_periodo.php?id_curso=<?= $idCurso ?>&id_materia=<?= (int) $materia['id_materia'] ?>"
                   class="list-group-item list-group-item-action">
                    <?= htmlspecialchars($materia['nombre']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</body>
</html>