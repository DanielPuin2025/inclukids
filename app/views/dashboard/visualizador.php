<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/DbaController.php";
require_once __DIR__ . "/../../controllers/SesionClaseController.php";
require_once __DIR__ . "/../../controllers/ProgresoController.php";

$idDocente = $_SESSION["id_docente"];

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listarPorDocente($idDocente);

$dbaController = new DbaController();
$dbaLista = $dbaController->listarPorDocente($idDocente);

$sesionController = new SesionClaseController();
$sesiones = $sesionController->listarPorDocente($idDocente);
$nSinProgreso = $sesionController->contarSinProgreso($idDocente);

$progresoController = new ProgresoController();
$registrosProgreso = $progresoController->listarPorDocente($idDocente);

// Estado más reciente por estudiante, solo para el resumen rápido
// (las insignias del encabezado). La consulta ya viene ordenada por
// fecha descendente, así que el primero que encontramos de cada
// estudiante es su estado más reciente.
$progresoPorEstudiante = [];
foreach ($registrosProgreso as $r) {
    $id = (int) $r["id_estudiante"];
    if (!isset($progresoPorEstudiante[$id])) {
        $progresoPorEstudiante[$id] = $r;
    }
}

$conteoProgreso = ["Logrado" => 0, "En proceso" => 0, "Requiere apoyo" => 0];
foreach ($progresoPorEstudiante as $r) {
    if (isset($conteoProgreso[$r["estado"]])) {
        $conteoProgreso[$r["estado"]]++;
    }
}

// El detalle de sesiones y de progreso ya no se despliega aquí: cada
// uno tiene su propia vista (../sesiones/mis_sesiones.php y
// ../progreso/panel.php). En este panel solo quedan los contadores del
// subtítulo.
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi panel - IncluKids</title>
<link rel="icon" href="/IncluKids/public/img/logo-inclukids.jpg">
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
<link href="<?= htmlspecialchars(asset_url('css/panel.css')) ?>" rel="stylesheet">
</head>
<body>

<div class="avatar-menu" id="avatarMenu">
    <button type="button" class="avatar-menu__boton" id="avatarBoton" aria-expanded="false" aria-haspopup="true" aria-label="Cuenta y accesibilidad">
        <?= htmlspecialchars(strtoupper(substr($_SESSION['nombre'] ?? '?', 0, 1))) ?>
    </button>

    <div class="avatar-menu__panel" id="avatarPanel">
        <div class="avatar-menu__nombre"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></div>

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

<div class="container mt-5">

    <div class="panel-encabezado">
        <img src="/IncluKids/public/img/logo-inclukids.jpg" alt="IncluKids" class="panel-encabezado__logo">
        <h2>👁️ Mi panel</h2>
    </div>
    <p class="text-muted mb-0">Estudiantes, DBA, sesiones y progreso de los cursos a tu cargo.</p>

    <?php if ($nSinProgreso > 0): ?>
        <div class="alert alert-warning mt-3 mb-0">
            Tienes <strong><?= $nSinProgreso ?></strong>
            sesi<?= $nSinProgreso === 1 ? "ón" : "ones" ?> de clase sin progreso registrado.
            <a href="../sesiones/mis_sesiones.php">Ir a mis sesiones →</a>
        </div>
    <?php endif; ?>

    <a href="../sesiones/paso1_curso.php" class="btn btn-primary btn-lg panel-cta">
        ▶️ Iniciar sesión de clase
    </a>

    <div class="panel-tiles">

        <!-- Mis estudiantes: enlace directo a la tabla completa, no despliega aquí -->
        <a href="../estudiantes/mis_estudiantes.php" class="panel-tile panel-tile--estudiantes panel-tile--enlace">
            <span class="panel-tile__header">
                <span class="panel-tile__icono">👨‍🎓</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">Mis estudiantes</span>
                    <span class="panel-tile__subtitulo"><?= count($estudiantes) ?> a cargo</span>
                </span>
                <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
            </span>
        </a>

        <!-- Mis sesiones de clase: enlace a la vista aparte, no despliega aquí -->
        <a href="../sesiones/mis_sesiones.php" class="panel-tile panel-tile--sesiones panel-tile--enlace">
            <span class="panel-tile__header">
                <span class="panel-tile__icono">📅</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">Mis sesiones de clase</span>
                    <span class="panel-tile__subtitulo"><?= count($sesiones) ?> registradas</span>
                </span>
                <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
            </span>
        </a>

        <!-- Progreso de mis estudiantes: enlace al historial completo, no despliega aquí -->
        <a href="../progreso/panel.php" class="panel-tile panel-tile--progreso panel-tile--enlace">
            <span class="panel-tile__header">
                <span class="panel-tile__icono">📊</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">Progreso de mis estudiantes</span>
                    <span class="panel-tile__subtitulo">
                        <?php if (empty($progresoPorEstudiante)): ?>
                            Todavía no hay progreso registrado
                        <?php else: ?>
                            <span class="mini-badge mini-badge--logrado">✅ <?= $conteoProgreso["Logrado"] ?></span>
                            <span class="mini-badge mini-badge--proceso">🟡 <?= $conteoProgreso["En proceso"] ?></span>
                            <span class="mini-badge mini-badge--apoyo">🆘 <?= $conteoProgreso["Requiere apoyo"] ?></span>
                        <?php endif; ?>
                    </span>
                </span>
                <span class="panel-tile__chevron panel-tile__chevron--enlace">→</span>
            </span>
        </a>

        <!-- DBA correspondientes -->
        <div class="panel-tile panel-tile--dba" data-abierto="false" id="tileDba">
            <button type="button" class="panel-tile__header" aria-expanded="false" aria-controls="contenidoDba">
                <span class="panel-tile__icono">📚</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">DBA correspondientes</span>
                    <span class="panel-tile__subtitulo"><?= count($dbaLista) ?> DBA</span>
                </span>
                <span class="panel-tile__chevron">▾</span>
            </button>
            <div class="panel-tile__contenido" id="contenidoDba">
                <div class="panel-tile__contenido-inner">

                    <div class="table-responsive mt-2">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Materia</th>
                                    <th>Grado</th>
                                    <th>Periodo</th>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dbaLista)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay DBA para los cursos a tu cargo.</td>
                                </tr>
                                <?php else: foreach ($dbaLista as $dba): ?>
                                <tr>
                                    <td><?= htmlspecialchars($dba["materia"] ?? "-") ?></td>
                                    <td><?= (int) $dba["grado"] ?>°</td>
                                    <td><?= (int) $dba["periodo"] ?></td>
                                    <td><?= htmlspecialchars($dba["codigo"]) ?></td>
                                    <td><?= htmlspecialchars($dba["descripcion"]) ?></td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<script>
    // Solo las tarjetas que despliegan contenido (las de tipo enlace
    // navegan a su propia vista y no llevan este toggle).
    document.querySelectorAll('.panel-tile:not(.panel-tile--enlace) .panel-tile__header').forEach(function (boton) {
        boton.addEventListener('click', function () {
            var tile = boton.closest('.panel-tile');
            var abierto = tile.getAttribute('data-abierto') === 'true';

            tile.setAttribute('data-abierto', abierto ? 'false' : 'true');
            boton.setAttribute('aria-expanded', abierto ? 'false' : 'true');
        });
    });

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
