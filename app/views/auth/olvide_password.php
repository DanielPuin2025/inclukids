<?php
require_once __DIR__ . "/../../helpers/session_boot.php";
sesion_iniciar();

require_once __DIR__ . "/../../helpers/csrf.php";

if (isset($_SESSION["id_docente"])) {
    header("Location: ../dashboard/home.php");
    exit();
}

$error = $_SESSION["error_olvide"] ?? "";
unset($_SESSION["error_olvide"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IncluKids - Olvidé mi contraseña</title>
    <link rel="icon" href="/IncluKids/public/img/logo-inclukids.jpg">
    <link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="text-center mb-3">
                <img src="/IncluKids/public/img/logo-inclukids.jpg" alt="IncluKids"
                     style="width: 80px; height: 80px; border-radius: 50%; box-shadow: 0 4px 12px rgba(36,48,58,0.15);">
            </div>
            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h3>¿Olvidaste tu contraseña?</h3>
                </div>
                <div class="card-body">

                    <p class="text-muted">
                        Escribe el correo con el que inicias sesión. Le
                        avisamos al administrador para que te genere una
                        contraseña temporal.
                    </p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="olvide_enviar.php" method="POST">
                        <?= csrf_campo() ?>
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="correo" class="form-control" required autofocus>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Enviar solicitud</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        <a href="../../../index.php">Volver a iniciar sesión</a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
