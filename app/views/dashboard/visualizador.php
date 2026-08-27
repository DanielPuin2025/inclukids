<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

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

$progresoController = new ProgresoController();
$registrosProgreso = $progresoController->listarPorDocente($idDocente);

// Estado más reciente por estudiante (la consulta ya viene ordenada
// por estudiante y fecha descendente, así que el primero que
// encontramos para cada uno es su estado más reciente).
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

$colores = ["Logrado" => "success", "En proceso" => "warning", "Requiere apoyo" => "danger"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi panel - IncluKids</title>
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

    <h2 class="mb-1">👁️ Mi panel</h2>
    <p class="text-muted mb-0">Estudiantes, DBA, sesiones y progreso de los cursos a tu cargo.</p>

    <a href="../sesiones/paso1_curso.php" class="btn btn-primary btn-lg panel-cta">
        ▶️ Iniciar sesión de clase
    </a>

    <div class="panel-tiles">

        <!-- Mis estudiantes -->
        <div class="panel-tile panel-tile--estudiantes" data-abierto="false" id="tileEstudiantes">
            <button type="button" class="panel-tile__header" aria-expanded="false" aria-controls="contenidoEstudiantes">
                <span class="panel-tile__icono">👨‍🎓</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">Mis estudiantes</span>
                    <span class="panel-tile__subtitulo"><?= count($estudiantes) ?> a cargo</span>
                </span>
                <span class="panel-tile__chevron">▾</span>
            </button>
            <div class="panel-tile__contenido" id="contenidoEstudiantes">
                <div class="panel-tile__contenido-inner">

                    <div class="d-flex justify-content-end mb-3 mt-2">
                        <a href="../estudiantes/nuevo.php" class="btn btn-sm btn-success">+ Nuevo estudiante</a>
                    </div>

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
                                        <a href="../estudiantes/editar.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- Mis sesiones de clase -->
        <div class="panel-tile panel-tile--sesiones" data-abierto="false" id="tileSesiones">
            <button type="button" class="panel-tile__header" aria-expanded="false" aria-controls="contenidoSesiones">
                <span class="panel-tile__icono">📅</span>
                <span class="panel-tile__texto">
                    <span class="panel-tile__titulo">Mis sesiones de clase</span>
                    <span class="panel-tile__subtitulo"><?= count($sesiones) ?> registradas</span>
                </span>
                <span class="panel-tile__chevron">▾</span>
            </button>
            <div class="panel-tile__contenido" id="contenidoSesiones">
                <div class="panel-tile__contenido-inner">

                    <div class="table-responsive mt-2">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Curso</th>
                                    <th>DBA trabajado</th>
                                    <th>Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sesiones)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        No hay sesiones registradas todavía. Usa
                                        "▶️ Iniciar sesión de clase" arriba.
                                    </td>
                                </tr>
                                <?php else: foreach ($sesiones as $sesion): ?>
                                <tr>
                                    <td><?= htmlspecialchars($sesion["fecha"]) ?></td>
                                    <td><?= $sesion["grado"] !== null ? (int) $sesion["grado"] . "°" . htmlspecialchars($sesion["grupo"]) : "-" ?></td>
                                    <td><?= htmlspecialchars($sesion["dba_codigo"] ?? "-") ?></td>
                                    <td>
                                        <a href="../sesiones/registrar_progreso.php?id_sesion=<?= (int) $sesion['id_sesion'] ?>"
                                           class="btn btn-sm btn-primary">Registrar progreso</a>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- Progreso de mis estudiantes -->
        <div class="panel-tile panel-tile--progreso" data-abierto="false" id="tileProgreso">
            <button type="button" class="panel-tile__header" aria-expanded="false" aria-controls="contenidoProgreso">
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
                <span class="panel-tile__chevron">▾</span>
            </button>
            <div class="panel-tile__contenido" id="contenidoProgreso">
                <div class="panel-tile__contenido-inner">

                    <?php if (empty($progresoPorEstudiante)): ?>
                        <p class="text-muted mt-2 mb-0">
                            Todavía no has registrado progreso de ningún estudiante.
                            Usa "Registrar progreso" desde una sesión de clase.
                        </p>
                    <?php else: ?>
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered mb-2">
                                <thead>
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>DBA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($progresoPorEstudiante as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r["nombres"] . " " . $r["apellidos"]) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $colores[$r["estado"]] ?? "secondary" ?>">
                                                <?= htmlspecialchars($r["estado"]) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($r["fecha"]) ?></td>
                                        <td><?= htmlspecialchars($r["dba_codigo"] ?? "-") ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="../progreso/panel.php" class="btn btn-sm btn-outline-primary">Ver historial completo &rarr;</a>
                    <?php endif; ?>

                </div>
            </div>
        </div>

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
    document.querySelectorAll('.panel-tile__header').forEach(function (boton) {
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
