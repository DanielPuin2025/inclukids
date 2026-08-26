<?php
$registrado = isset($_GET["registrado"]);
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>IncluKids - Iniciar Sesión</title>

    <link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container">

    <div class="row justify-content-center mt-5">

        <div class="col-md-4">

            <div class="card shadow">

                <div class="card-header text-center bg-primary text-white">

                    <h3>IncluKids</h3>

                </div>

                <div class="card-body">

                    <?php if ($registrado): ?>
                        <div class="alert alert-success">
                            ¡Cuenta creada! Ya puedes iniciar sesión.
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">

                        <div class="mb-3">

                            <label class="form-label">Correo electrónico</label>

                            <input type="email" name="correo" class="form-control" required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">Contraseña</label>

                            <input type="password" name="password" class="form-control" required>

                        </div>

                        <button type="submit" name="login" class="btn btn-success w-100">

                            Iniciar sesión

                        </button>

                    </form>

                    <p class="text-center mt-3 mb-0">
                        ¿No tienes cuenta? <a href="app/views/auth/registro.php">Regístrate aquí</a>
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>