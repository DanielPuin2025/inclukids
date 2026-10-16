<?php
$registrado = isset($_GET["registrado"]);
$expirado = isset($_GET["expirado"]);
$solicitud = isset($_GET["solicitud"]);

$error = $_SESSION["error_login"] ?? null;
unset($_SESSION["error_login"]);
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>IncluKids - Iniciar Sesión</title>
    <link rel="icon" href="/IncluKids/public/img/logo-inclukids.jpg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
    <link href="/IncluKids/public/css/login.css" rel="stylesheet">
    <link href="/IncluKids/public/css/a11y.css?v=10" rel="stylesheet">
    <script src="/IncluKids/public/js/a11y.js?v=10" defer></script>
    <script src="/IncluKids/public/js/creditos.js?v=10" defer></script>

</head>

<body class="login-body">

<div class="login-shell">

    <div class="login-hero">
        <img src="/IncluKids/public/img/logo-colegio.jpg" alt="" aria-hidden="true" class="login-hero__marca-agua">

        <div class="login-hero__blob login-hero__blob--1"></div>
        <div class="login-hero__blob login-hero__blob--2"></div>
        <div class="login-hero__blob login-hero__blob--3"></div>

        <div class="login-hero__content">
            <img src="/IncluKids/public/img/logo-inclukids.jpg" alt="IncluKids" class="login-sello">
            <p class="login-hero__institucion">Megacolegio El Progreso</p>
            <p class="login-hero__tagline">
                Seguimiento pedagógico inclusivo para cada estudiante,
                a cargo de cada docente.
            </p>
        </div>
    </div>

    <div class="login-panel">

        <div class="login-card">

            <div class="login-card__brand">
                <img src="/IncluKids/public/img/logo-inclukids.jpg" alt="IncluKids" class="login-card__brand-img">
            </div>

            <?php if ($registrado): ?>
                <div class="login-alert login-alert--exito" role="status">
                    ¡Cuenta creada! Ya puedes iniciar sesión.
                </div>
            <?php endif; ?>

            <?php if ($expirado): ?>
                <div class="login-alert login-alert--error" role="status">
                    Tu sesión se cerró por inactividad. Vuelve a iniciar sesión.
                </div>
            <?php endif; ?>

            <?php if ($solicitud): ?>
                <div class="login-alert login-alert--exito" role="status">
                    Si ese correo tiene una cuenta, le avisamos al
                    administrador. Te va a pasar una contraseña temporal
                    por un canal seguro.
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="login-alert login-alert--error" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="role-toggle" data-rol="docente" id="roleToggle">
                <div class="role-toggle__pill" aria-hidden="true"></div>
                <button type="button" class="role-toggle__btn is-active" data-rol="docente" id="btnRolDocente" aria-pressed="true">
                    <span class="role-toggle__icon">🧑‍🏫</span> Docente
                </button>
                <button type="button" class="role-toggle__btn" data-rol="administrador" id="btnRolAdmin" aria-pressed="false">
                    <span class="role-toggle__icon">🛡️</span> Administrador
                </button>
            </div>

            <p class="role-toggle__hint" id="roleHint">
                Ingresa con tu correo institucional de docente.
            </p>

            <form action="" method="POST" id="loginForm" novalidate>

                <?= csrf_campo() ?>
                <input type="hidden" name="rol" id="rolSeleccionado" value="docente">

                <div class="field">
                    <span class="field__icon">✉️</span>
                    <input type="email" name="correo" id="correo" class="field__input" placeholder=" " required autocomplete="email">
                    <label for="correo" class="field__label">Correo electrónico</label>
                </div>

                <div class="field">
                    <span class="field__icon">🔒</span>
                    <input type="password" name="password" id="password" class="field__input" placeholder=" " required autocomplete="current-password">
                    <label for="password" class="field__label">Contraseña</label>
                    <button type="button" class="field__toggle" id="togglePassword" aria-label="Mostrar contraseña" aria-pressed="false">
                        👁️
                    </button>
                </div>

                <p class="text-end mb-3">
                    <a href="app/views/auth/olvide_password.php">¿Olvidaste tu contraseña?</a>
                </p>

                <button type="submit" name="login" class="btn login-submit w-100" id="loginSubmit">
                    <span class="login-submit__label">Iniciar sesión</span>
                    <span class="login-submit__spinner" aria-hidden="true">📖</span>
                </button>

            </form>

        </div>

    </div>

</div>

<script src="/IncluKids/public/js/login.js"></script>

</body>

</html>
