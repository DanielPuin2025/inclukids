<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("visualizador.php");

require_once __DIR__ . "/../../controllers/DashboardController.php";

$dashboardController = new DashboardController();
$estadisticas = $dashboardController->obtenerEstadisticas();
?>
<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Panel de administración - IncluKids</title>

<link rel="icon" href="/IncluKids/public/img/logo-inclukids.jpg">
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
<link href="/IncluKids/public/css/panel.css" rel="stylesheet">

</head>
<body>

<div class="avatar-menu" id="avatarMenu">
    <button type="button" class="avatar-menu__boton" id="avatarBoton" aria-expanded="false" aria-haspopup="true" aria-label="Cuenta y accesibilidad">
        <?= htmlspecialchars(strtoupper(substr($_SESSION['nombre'] ?? '?', 0, 1))) ?>
    </button>

    <div class="avatar-menu__panel" id="avatarPanel">
        <div class="avatar-menu__nombre"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></div>

        <a href="../docentes/perfil.php" class="avatar-menu__item" style="text-decoration:none;">
            <span>👤 Mi perfil</span>
        </a>

        <hr>

        <div class="avatar-menu__seccion">
            <div class="avatar-menu__titulo">♿ Accesibilidad</div>

            <div class="avatar-menu__item">
                <span>Tamaño de texto</span>
                <div class="tamano-texto-botones">
                    <button type="button" class="btn-tamano" data-tamano="normal">A</button>
                    <button type="button" class="btn-tamano" data-tamano="grande">A+</button>
                    <button type="button" class="btn-tamano" data-tamano="extra">A++</button>
                </div>
            </div>

            <label class="avatar-menu__item avatar-menu__switch">
                <span>Alto contraste</span>
                <input type="checkbox" id="toggleContraste">
                <span class="switch-visual" aria-hidden="true"></span>
            </label>

            <label class="avatar-menu__item avatar-menu__switch">
                <span>Reducir movimiento</span>
                <input type="checkbox" id="toggleMovimiento">
                <span class="switch-visual" aria-hidden="true"></span>
            </label>
        </div>

        <hr>

        <a href="../../../logout.php" class="avatar-menu__logout">🚪 Cerrar sesión</a>
    </div>
</div>

<div class="container mt-5" style="max-width: 1100px;">

    <div class="panel-encabezado">
        <img src="/IncluKids/public/img/logo-inclukids.jpg" alt="IncluKids" class="panel-encabezado__logo">
        <h2>🛡️ Panel de administración</h2>
    </div>
    <p class="text-muted mb-4">Bienvenido, <?= htmlspecialchars($_SESSION["nombre"] ?? '') ?>. Vista global de IncluKids.</p>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="../estudiantes/index.php" class="stat-card">
                <div class="stat-card__etiqueta">Estudiantes</div>
                <div class="stat-card__numero"><?= (int) $estadisticas["estudiantes"] ?></div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="../actividades/index.php" class="stat-card">
                <div class="stat-card__etiqueta">Actividades</div>
                <div class="stat-card__numero"><?= (int) $estadisticas["actividades"] ?></div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="../dba/index.php" class="stat-card">
                <div class="stat-card__etiqueta">DBA</div>
                <div class="stat-card__numero"><?= (int) $estadisticas["dba"] ?></div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="cursor: default;">
                <div class="stat-card__etiqueta">Sesiones</div>
                <div class="stat-card__numero"><?= (int) $estadisticas["sesiones"] ?></div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Gestión</h5>

    <div class="panel-grid">

        <a href="../estudiantes/index.php" class="panel-tile panel-tile--estudiantes panel-tile--enlace">
            <span class="panel-tile__header">
                <span class="panel-tile__icono">👨‍🎓</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">Estudiantes</span>
                    <span class="panel-tile__subtitulo">Ver y administrar</span>
                </span>
                <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
            </span>
        </a>

        <a href="../cursos/index.php" class="panel-tile panel-tile--cursos panel-tile--enlace">
            <span class="panel-tile__header">
                <span class="panel-tile__icono">🏫</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">Cursos</span>
                    <span class="panel-tile__subtitulo">Grados, grupos y docente a cargo</span>
                </span>
                <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
            </span>
        </a>

        <a href="../dba/index.php" class="panel-tile panel-tile--dba panel-tile--enlace">
            <span class="panel-tile__header">
                <span class="panel-tile__icono">📚</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">DBA</span>
                    <span class="panel-tile__subtitulo">Derechos básicos de aprendizaje</span>
                </span>
                <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
            </span>
        </a>

        <a href="../actividades/index.php" class="panel-tile panel-tile--actividades panel-tile--enlace">
            <span class="panel-tile__header">
                <span class="panel-tile__icono">🎮</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">Actividades</span>
                    <span class="panel-tile__subtitulo">Juegos y su contenido</span>
                </span>
                <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
            </span>
        </a>

        <a href="visualizador.php" class="panel-tile panel-tile--visualizador panel-tile--enlace">
            <span class="panel-tile__header">
                <span class="panel-tile__icono">👁️</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">Panel de un docente</span>
                    <span class="panel-tile__subtitulo">Cómo se ve tu propia cuenta como docente</span>
                </span>
                <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
            </span>
        </a>

    </div>

</div>

<script>
    // ---------- Menú de cuenta + accesibilidad ----------
    (function () {
        var CLAVE_PREFS = 'inclukids_a11y';

        function cargarPrefs() {
            try {
                return JSON.parse(localStorage.getItem(CLAVE_PREFS)) || {};
            } catch (e) {
                return {};
            }
        }
        function guardarPrefs(prefs) {
            try {
                localStorage.setItem(CLAVE_PREFS, JSON.stringify(prefs));
            } catch (e) {
                // Si el navegador bloquea localStorage (modo privado, etc.),
                // la preferencia simplemente no se recuerda entre visitas.
            }
        }

        var prefs = cargarPrefs();

        function aplicarTamano(tamano) {
            document.documentElement.classList.remove('texto-grande', 'texto-extra');
            if (tamano === 'grande') document.documentElement.classList.add('texto-grande');
            if (tamano === 'extra') document.documentElement.classList.add('texto-extra');

            document.querySelectorAll('.btn-tamano').forEach(function (b) {
                b.classList.toggle('activo', b.dataset.tamano === tamano);
            });
        }

        aplicarTamano(prefs.tamano || 'normal');
        document.body.classList.toggle('alto-contraste', !!prefs.contraste);
        document.body.classList.toggle('reducir-movimiento', !!prefs.movimiento);

        var toggleContraste = document.getElementById('toggleContraste');
        var toggleMovimiento = document.getElementById('toggleMovimiento');
        if (toggleContraste) toggleContraste.checked = !!prefs.contraste;
        if (toggleMovimiento) toggleMovimiento.checked = !!prefs.movimiento;

        document.querySelectorAll('.btn-tamano').forEach(function (boton) {
            boton.addEventListener('click', function () {
                prefs.tamano = boton.dataset.tamano;
                guardarPrefs(prefs);
                aplicarTamano(prefs.tamano);
            });
        });

        if (toggleContraste) {
            toggleContraste.addEventListener('change', function () {
                prefs.contraste = toggleContraste.checked;
                guardarPrefs(prefs);
                document.body.classList.toggle('alto-contraste', prefs.contraste);
            });
        }

        if (toggleMovimiento) {
            toggleMovimiento.addEventListener('change', function () {
                prefs.movimiento = toggleMovimiento.checked;
                guardarPrefs(prefs);
                document.body.classList.toggle('reducir-movimiento', prefs.movimiento);
            });
        }

        var avatarMenu = document.getElementById('avatarMenu');
        var avatarBoton = document.getElementById('avatarBoton');

        if (avatarBoton && avatarMenu) {
            avatarBoton.addEventListener('click', function (e) {
                e.stopPropagation();
                var abierto = avatarMenu.classList.toggle('abierto');
                avatarBoton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            });

            document.addEventListener('click', function (e) {
                if (!avatarMenu.contains(e.target)) {
                    avatarMenu.classList.remove('abierto');
                    avatarBoton.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    avatarMenu.classList.remove('abierto');
                    avatarBoton.setAttribute('aria-expanded', 'false');
                }
            });
        }
    })();
</script>

</body>
</html>
