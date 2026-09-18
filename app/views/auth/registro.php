<?php
require_once __DIR__ . "/../../helpers/session_boot.php";
sesion_iniciar();

// El registro público se cerró: las cuentas de docente las crea el
// administrador desde el panel (app/views/docentes/nuevo.php). Este
// archivo se deja en pie (en vez de borrarlo) para que un link viejo o
// guardado en favoritos no caiga en un 404 confuso, pero ya no ofrece
// ningún formulario.
if (isset($_SESSION["id_docente"])) {
    header("Location: ../dashboard/home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IncluKids - Registro cerrado</title>
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
                    <h3>Registro cerrado</h3>
                </div>
                <div class="card-body text-center">
                    <p>
                        Las cuentas de docente ya no se crean por aquí:
                        pídele a la persona que administra IncluKids en tu
                        colegio que te dé de alta.
                    </p>
                    <p class="mb-0">
                        <a href="../../../index.php">Volver a iniciar sesión</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
