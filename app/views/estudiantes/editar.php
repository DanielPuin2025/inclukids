<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/CursoController.php";

$idEstudiante = (int) ($_GET["id_estudiante"] ?? 0);

$estudianteController = new EstudianteController();
$estudiante = $estudianteController->obtenerPorId($idEstudiante);

if (!$estudiante) {
    header("Location: ../dashboard/home.php?modulo=estudiantes");
    exit();
}

$cursoController = new CursoController();
$cursos = $cursoController->listarPorDocente($_SESSION["id_docente"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar estudiante</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <a href="../dashboard/home.php?modulo=estudiantes" class="text-decoration-none">&larr; Volver a Estudiantes</a>

    <h2 class="mt-3 mb-4">Editar estudiante</h2>

    <form method="POST" action="guardar.php">
        <input type="hidden" name="id_estudiante" value="<?= (int) $estudiante['id_estudiante'] ?>">

        <div class="mb-3">
            <label class="form-label">Nombres</label>
            <input type="text" class="form-control" name="nombres"
                   value="<?= htmlspecialchars($estudiante['nombres']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Apellidos</label>
            <input type="text" class="form-control" name="apellidos"
                   value="<?= htmlspecialchars($estudiante['apellidos']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Curso</label>
            <select class="form-select" name="id_curso" required>
                <option value="">-- Selecciona --</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?= (int) $curso['id_curso'] ?>"
                        <?= ((int) $estudiante['id_curso'] === (int) $curso['id_curso']) ? 'selected' : '' ?>>
                        <?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100">Guardar cambios</button>
    </form>

</div>
</body>
</html>