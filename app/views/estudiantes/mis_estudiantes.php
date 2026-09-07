<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/EstudianteController.php";

$idDocente = $_SESSION["id_docente"];

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listarPorDocente($idDocente);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mis estudiantes - IncluKids</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
<link href="/IncluKids/public/css/a11y.css?v=2" rel="stylesheet">
<script src="/IncluKids/public/js/a11y.js?v=2" defer></script>
</head>
<body>
<div class="container mt-5">

    <a href="../dashboard/visualizador.php" class="text-decoration-none">&larr; Volver a mi panel</a>

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <h2 class="mb-0">👨‍🎓 Mis estudiantes</h2>
        <a href="nuevo.php" class="btn btn-success">+ Nuevo estudiante</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Curso</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estudiantes)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No tienes estudiantes a cargo todavía.</td>
                        </tr>
                        <?php else: foreach ($estudiantes as $estudiante): ?>
                        <tr>
                            <td><?= htmlspecialchars($estudiante["nombres"]) ?></td>
                            <td><?= htmlspecialchars($estudiante["apellidos"]) ?></td>
                            <td><?= (int) $estudiante["grado"] ?>°<?= htmlspecialchars($estudiante["grupo"]) ?></td>
                            <td>
                                <a href="editar.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="../progreso/reporte_estudiante.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-secondary">📄 Reporte</a>
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
