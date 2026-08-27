<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/CursoController.php";

$cursoController = new CursoController();
$cursos = $cursoController->listarPorDocente($_SESSION["id_docente"]);

$volverEstudiantes = (($_SESSION["rol"] ?? "docente") === "administrador")
    ? "../estudiantes/index.php"
    : "../dashboard/visualizador.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo estudiante</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <a href="<?= $volverEstudiantes ?>" class="text-decoration-none">&larr; Volver a Estudiantes</a>

    <h2 class="mt-3 mb-4">Nuevo estudiante</h2>

    <?php if (empty($cursos)): ?>
        <div class="alert alert-warning">
            No tienes cursos asignados todavía. Necesitas al menos un
            curso antes de poder registrar estudiantes.
        </div>
    <?php else: ?>
        <form method="POST" action="guardar.php">
            <div class="mb-3">
                <label for="nombres" class="form-label">Nombres</label>
                <input type="text" class="form-control" id="nombres" name="nombres" required>
            </div>

            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
            </div>

            <div class="mb-3">
                <label for="id_curso" class="form-label">Curso</label>
                <select class="form-select" id="id_curso" name="id_curso" required>
                    <option value="">-- Selecciona --</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?= (int) $curso['id_curso'] ?>">
                            <?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success w-100">Guardar estudiante</button>
        </form>
    <?php endif; ?>

</div>
</body>
</html>