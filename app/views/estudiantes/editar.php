<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/CursoController.php";

$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
$idEstudiante = (int) ($_GET["id_estudiante"] ?? 0);

$estudianteController = new EstudianteController();
$estudiante = $estudianteController->obtenerPorId($idEstudiante);

$volverEstudiantes = $esAdmin
    ? (($estudiante && $estudiante['id_curso']) ? "curso.php?id_curso=" . (int) $estudiante['id_curso'] : "index.php")
    : "../dashboard/visualizador.php";

if (!$estudiante) {
    header("Location: $volverEstudiantes");
    exit();
}

$cursoController = new CursoController();

// Un docente solo puede editar estudiantes de un curso que tiene a
// cargo; si no, podría editar (o incluso "robar" a su propio curso)
// estudiantes de otros docentes con solo cambiar el id en la URL.
if (!$esAdmin) {
    $cursoDelEstudiante = $cursoController->obtenerPorId((int) $estudiante["id_curso"]);
    if (!$cursoDelEstudiante || (int) $cursoDelEstudiante["id_docente"] !== (int) $_SESSION["id_docente"]) {
        die("No tienes permiso para editar este estudiante. <a href='../dashboard/visualizador.php'>Volver</a>");
    }
}

// El administrador puede mover al estudiante a cualquier curso; el
// docente solo entre los cursos que tiene a cargo.
$cursos = $esAdmin ? $cursoController->listar() : $cursoController->listarPorDocente($_SESSION["id_docente"]);
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

    <a href="<?= htmlspecialchars($volverEstudiantes) ?>" class="text-decoration-none">&larr; Volver a Estudiantes</a>

    <h2 class="mt-3 mb-4">Editar estudiante</h2>

    <form method="POST" action="guardar.php">
        <?= csrf_campo() ?>
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
