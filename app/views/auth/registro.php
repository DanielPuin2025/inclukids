<?php
session_start();

// Si ya hay sesión activa, no tiene sentido registrarse de nuevo
if (isset($_SESSION["id_docente"])) {
    header("Location: ../dashboard/home.php");
    exit();
}

$error = $_SESSION["error_registro"] ?? "";
unset($_SESSION["error_registro"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IncluKids - Registro de docente</title>
    <link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Crear cuenta - IncluKids</h3>
                </div>
                <div class="card-body">

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="guardar_registro.php" method="POST">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Nombres</label>
                                <input type="text" name="nombres" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Apellidos</label>
                                <input type="text" name="apellidos" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" minlength="6" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" name="password_confirmar" class="form-control" minlength="6" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Crear cuenta</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
    ¿Ya tienes cuenta? <a href="../../../index.php">Inicia sesión</a>
</p>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>