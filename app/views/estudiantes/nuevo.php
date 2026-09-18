<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/CursoController.php";

$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
$idCursoPreseleccionado = (int) ($_GET["id_curso"] ?? 0);

$cursoController = new CursoController();
// El administrador ve todos los cursos (no tiene cursos propios);
// el docente solo los que tiene a cargo.
$cursos = $esAdmin ? $cursoController->listar() : $cursoController->listarPorDocente($_SESSION["id_docente"]);

$volverEstudiantes = $esAdmin
    ? ($idCursoPreseleccionado > 0 ? "curso.php?id_curso=$idCursoPreseleccionado" : "index.php")
    : "../dashboard/visualizador.php";
?>
<?php render_head("Nuevo estudiante"); ?>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <h2 class="mt-3 mb-4">Nuevo estudiante</h2>

    <?php if (empty($cursos)): ?>
        <div class="alert alert-warning">
            <?= $esAdmin
                ? 'No hay cursos registrados todavía. Crea uno desde "Cursos" antes de poder registrar estudiantes.'
                : 'No tienes cursos asignados todavía. Necesitas al menos un curso antes de poder registrar estudiantes.' ?>
        </div>
    <?php else: ?>
        <form method="POST" action="guardar.php">
            <?= csrf_campo() ?>
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
                        <option value="<?= (int) $curso['id_curso'] ?>"
                            <?= ((int) $curso['id_curso'] === $idCursoPreseleccionado) ? 'selected' : '' ?>>
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
