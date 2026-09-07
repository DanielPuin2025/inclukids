<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/DbaController.php";

$dbaController = new DbaController();
$dbaLista = $dbaController->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>DBA</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
    <link href="/IncluKids/public/css/a11y.css?v=2" rel="stylesheet">
    <script src="/IncluKids/public/js/a11y.js?v=2" defer></script>
</head>
<body>
<div class="container mt-5">

    <a href="../dashboard/home.php" class="text-decoration-none">&larr; Volver al inicio</a>

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <h2 class="mb-0">📚 DBA</h2>
        <a href="nuevo.php" class="btn btn-success">+ Nuevo DBA</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Grado</th>
                        <th>Periodo</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dbaLista)): ?>
                    <tr><td colspan="6" class="text-center">No hay DBA registrados.</td></tr>
                    <?php else: foreach ($dbaLista as $dba): ?>
                    <tr>
                        <td><?= htmlspecialchars($dba['materia'] ?? '-') ?></td>
                        <td><?= (int) $dba['grado'] ?>°</td>
                        <td><?= (int) $dba['periodo'] ?></td>
                        <td><?= htmlspecialchars($dba['codigo']) ?></td>
                        <td><?= htmlspecialchars($dba['descripcion']) ?></td>
                        <td>
                            <a href="editar.php?id_dba=<?= (int) $dba['id_dba'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form method="POST" action="eliminar.php" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este DBA? También se eliminarán sus actividades y el contenido jugable de cada una. Si el DBA o alguna de sus actividades ya se usó en una sesión de clase real, no se podrá eliminar.');">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id_dba" value="<?= (int) $dba['id_dba'] ?>">
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