<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../controllers/DocenteController.php";

$docenteController = new DocenteController();
$docente = $docenteController->obtenerPorId($_SESSION["id_docente"]);

$mensaje = $_SESSION["mensaje_perfil"] ?? "";
unset($_SESSION["mensaje_perfil"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi perfil</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <a href="../dashboard/home.php" class="text-decoration-none">&larr; Volver al dashboard</a>

    <h2 class="mt-3 mb-4">👤 Mi perfil</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Mis datos</h5>
            <form method="POST" action="guardar_perfil.php">
                <div class="mb-3">
                    <label class="form-label">Nombres</label>
                    <input type="text" class="form-control" name="nombres"
                           value="<?= htmlspecialchars($docente['nombres']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" class="form-control" name="apellidos"
                           value="<?= htmlspecialchars($docente['apellidos']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo</label>
                    <input type="email" class="form-control" name="correo"
                           value="<?= htmlspecialchars($docente['correo']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Guardar datos</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Cambiar contraseña</h5>
            <form method="POST" action="guardar_password.php">
                <div class="mb-3">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" class="form-control" name="nueva" minlength="6" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar nueva contraseña</label>
                    <input type="password" class="form-control" name="confirmar" minlength="6" required>
                </div>
                <button type="submit" class="btn btn-secondary w-100">Cambiar contraseña</button>
            </form>
        </div>
    </div>

</div>
</body>
</html>