<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/CursoController.php";

$cursoController = new CursoController();
$cursos = $cursoController->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cursos</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
    <link href="/IncluKids/public/css/a11y.css?v=2" rel="stylesheet">
    <script src="/IncluKids/public/js/a11y.js?v=2" defer></script>
</head>
<body>
<div class="container mt-5">

    <a href="../dashboard/home.php" class="text-decoration-none">&larr; Volver al inicio</a>

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <h2 class="mb-0">🏫 Cursos</h2>
        <a href="nuevo.php" class="btn btn-success">+ Nuevo curso</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Curso</th>
                        <th>Año</th>
                        <th>Docente a cargo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cursos)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay cursos registrados.</td>
                    </tr>
                    <?php else: foreach ($cursos as $curso): ?>
                    <tr>
                        <td><?= (int) $curso['id_curso'] ?></td>
                        <td><?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?></td>
                        <td><?= (int) $curso['anio'] ?></td>
                        <td><?= htmlspecialchars(($curso['docente_nombres'] ?? '-') . ' ' . ($curso['docente_apellidos'] ?? '')) ?></td>
                        <td>
                            <a href="editar.php?id_curso=<?= (int) $curso['id_curso'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form method="POST" action="eliminar.php" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este curso? Esto puede afectar estudiantes y sesiones ya ligados a él.');">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id_curso" value="<?= (int) $curso['id_curso'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>