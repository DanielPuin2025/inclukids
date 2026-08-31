<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../controllers/ActividadController.php";
require_once __DIR__ . "/../../controllers/ContenidoJuegoController.php";

$idActividad = (int) ($_GET["id_actividad"] ?? 0);
$idSesion = (int) ($_GET["id_sesion"] ?? 0);

$actividadController = new ActividadController();
$actividad = $actividadController->obtenerPorId($idActividad);

if (!$actividad) {
    die("Actividad no encontrada.");
}

$contenidoController = new ContenidoJuegoController();
$contenido = $contenidoController->obtenerPorActividad($idActividad);

$rondas = [];
if ($contenido) {
    $datos = json_decode($contenido["datos_json"], true);
    $rondas = $datos["rondas"] ?? [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
<title><?= htmlspecialchars($actividad['nombre']) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        /* Paleta derivada de la marca IncluKids (ver public/css/inclukids.css),
           en clave cálida para las pantallas que usan los niños. */
        --color-fondo: #FCF4E4;
        --color-primario: #2F6E73;
        --color-primario-oscuro: #1D4448;
        --color-texto: #24303A;
        --color-correcto: #3C9D5C;
        --color-incorrecto: #B24632;
        --color-zona: #EDE3CC;
        --color-acento: #C98A2B;
        --sombra-pieza: 0 6px 16px rgba(36, 48, 58, 0.12);
        --sombra-pieza-alta: 0 14px 30px rgba(36, 48, 58, 0.24);
    }
    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    html, body {
        touch-action: none;
    }
    body {
        margin: 0;
        font-family: 'IBM Plex Sans', 'Segoe UI', system-ui, sans-serif;
        background:
            radial-gradient(circle at 12% 0%, rgba(47, 110, 115, 0.07), transparent 45%),
            radial-gradient(circle at 90% 8%, rgba(201, 138, 43, 0.09), transparent 40%),
            var(--color-fondo);
        color: var(--color-texto);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 28px 16px 64px;
        user-select: none;
    }
    .barra-progreso { display: flex; gap: 10px; margin-bottom: 24px; }
    .punto {
        width: 18px; height: 18px; border-radius: 50%;
        background: #E4D9C0;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    .punto.activo { background: var(--color-primario); transform: scale(1.15); }
    .punto.hecho { background: var(--color-correcto); }

    h1 {
        font-size: 22px;
        font-weight: 600;
        color: var(--color-primario-oscuro);
        margin: 0 0 4px;
        text-align: center;
        letter-spacing: -0.01em;
    }
    .instruccion {
        font-size: 27px;
        font-weight: 700;
        line-height: 1.3;
        text-align: center;
        max-width: 640px;
        margin: 14px 0 30px;
    }
    .btn-escuchar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        min-height: 46px;
        padding: 10px 22px;
        border-radius: 999px;
        border: 2px solid transparent;
        background: white;
        box-shadow: 0 3px 10px rgba(36, 48, 58, 0.1);
        font-family: inherit;
        font-size: 16px;
        font-weight: 600;
        color: var(--color-primario-oscuro);
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .btn-escuchar:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(36, 48, 58, 0.16); }
    .btn-escuchar:active { transform: translateY(0); }
    .btn-escuchar:focus-visible { outline: none; border-color: var(--color-primario); }

    .zonas { display: flex; flex-wrap: wrap; gap: 26px; justify-content: center; margin-bottom: 44px; }
    .zona {
        width: 148px;
        height: 148px;
        border-radius: 26px;
        border: 4px dashed #CBBE9C;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        transition: border-color 0.2s ease, background-color 0.2s ease,
                    transform 0.15s ease, box-shadow 0.2s ease;
    }
    .zona .zona-etiqueta { font-size: 20px; font-weight: 700; margin-top: 4px; color: var(--color-primario-oscuro); }
    .zona.resaltada {
        border-color: var(--color-primario);
        background: rgba(47, 110, 115, 0.12);
        transform: scale(1.05);
        box-shadow: 0 0 0 6px rgba(47, 110, 115, 0.14);
    }
    .zona.llena {
        border-style: solid;
        border-color: var(--color-correcto);
        background: rgba(60, 157, 92, 0.14);
    }

    .piezas {
        display: flex;
        flex-wrap: wrap;
        gap: 22px;
        justify-content: center;
        max-width: 720px;
    }
    .pieza {
        width: 116px;
        height: 116px;
        border-radius: 22px;
        background: white;
        box-shadow: var(--sombra-pieza);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 44px;
        cursor: grab;
        touch-action: none;
        position: relative;
        transition: box-shadow 0.15s ease, transform 0.12s ease;
    }
    .pieza:hover { transform: translateY(-2px); }
    .pieza:focus-visible { outline: 3px solid var(--color-primario); outline-offset: 3px; }
    .pieza .pieza-etiqueta { font-size: 13px; font-weight: 600; margin-top: 2px; color: var(--color-texto); }
    .pieza.arrastrando { box-shadow: var(--sombra-pieza-alta); cursor: grabbing; z-index: 50; transform: scale(1.05); }
    .pieza.oculta { visibility: hidden; pointer-events: none; }

    @keyframes rebote {
        0%, 100% { transform: translate(0,0); }
        25% { transform: translate(-8px, 0); }
        75% { transform: translate(8px, 0); }
    }
    .pieza.rebotando { animation: rebote 0.3s ease; }

    .mensaje { font-size: 24px; font-weight: 700; margin-top: 28px; min-height: 32px; }
    .mensaje.correcto { color: var(--color-correcto); }

    .btn-siguiente {
        margin-top: 18px;
        font-family: inherit;
        font-size: 21px;
        font-weight: 700;
        min-height: 56px;
        padding: 14px 44px;
        border-radius: 999px;
        border: none;
        background: var(--color-acento);
        color: white;
        cursor: pointer;
        display: none;
        box-shadow: 0 6px 18px rgba(201, 138, 43, 0.35);
        transition: transform 0.12s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    }
    .btn-siguiente:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(201, 138, 43, 0.42); }
    .btn-siguiente:active { transform: translateY(0); }
    .btn-siguiente:focus-visible { outline: 3px solid var(--color-primario-oscuro); outline-offset: 3px; }

    .pantalla-final { text-align: center; display: none; }
    .pantalla-final h2 { font-size: 38px; color: var(--color-correcto); }
    .btn-volver {
        display: inline-block;
        margin-top: 22px;
        min-height: 52px;
        font-size: 18px;
        font-weight: 600;
        padding: 14px 34px;
        border-radius: 999px;
        background: var(--color-primario);
        color: white;
        text-decoration: none;
        box-shadow: 0 5px 14px rgba(47, 110, 115, 0.3);
        transition: transform 0.12s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    }
    .btn-volver:hover { transform: translateY(-1px); background: var(--color-primario-oscuro); color: white; box-shadow: 0 8px 20px rgba(47, 110, 115, 0.38); }
    .btn-volver:focus-visible { outline: 3px solid var(--color-primario-oscuro); outline-offset: 3px; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.001ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.001ms !important;
        }
        .punto.activo { transform: none; }
        .zona.resaltada { transform: none; }
        .pieza:hover, .pieza.arrastrando { transform: none; }
        .btn-escuchar:hover, .btn-siguiente:hover, .btn-volver:hover { transform: none; }
    }
</style>
</head>
<body>

<h1><?= htmlspecialchars($actividad['nombre']) ?></h1>

<?php if (empty($rondas)): ?>

    <p style="text-align:center; max-width: 500px;">
        Este juego todavía no tiene rondas cargadas.
    </p>
    <a href="../sesiones/confirmacion.php?id_sesion=<?= $idSesion ?>" class="btn-volver">&larr; Volver</a>

<?php else: ?>

    <div class="barra-progreso" id="barraProgreso"></div>

    <div id="pantallaJuego">
        <div class="instruccion" id="instruccion"></div>
        <div style="text-align:center;">
            <button class="btn-escuchar" id="btnEscuchar" type="button">🔊 Escuchar</button>
        </div>

        <div class="zonas" id="contenedorZonas"></div>
        <div class="piezas" id="contenedorPiezas"></div>

        <div style="text-align:center;">
            <div class="mensaje" id="mensaje"></div>
            <button class="btn-siguiente" id="btnSiguiente">Siguiente ➡️</button>
        </div>
    </div>

    <div class="pantalla-final" id="pantallaFinal">
        <h2>🎉 ¡Actividad completada!</h2>
        <a href="../sesiones/confirmacion.php?id_sesion=<?= $idSesion ?>" class="btn-volver">&larr; Volver a la sesión</a>
    </div>

    <script>
        const rondas = <?= json_encode($rondas, JSON_UNESCAPED_UNICODE) ?>;
        let indiceRonda = 0;

        const barraProgreso = document.getElementById('barraProgreso');
        const instruccionEl = document.getElementById('instruccion');
        const contenedorZonas = document.getElementById('contenedorZonas');
        const contenedorPiezas = document.getElementById('contenedorPiezas');
        const mensajeEl = document.getElementById('mensaje');
        const btnSiguiente = document.getElementById('btnSiguiente');
        const btnEscuchar = document.getElementById('btnEscuchar');
        const pantallaJuego = document.getElementById('pantallaJuego');
        const pantallaFinal = document.getElementById('pantallaFinal');

        function dibujarBarraProgreso() {
            barraProgreso.innerHTML = '';
            rondas.forEach((_, i) => {
                const punto = document.createElement('div');
                punto.className = 'punto' + (i < indiceRonda ? ' hecho' : (i === indiceRonda ? ' activo' : ''));
                barraProgreso.appendChild(punto);
            });
        }

        function hablar(texto) {
            if (!('speechSynthesis' in window) || !texto) return;
            window.speechSynthesis.cancel();
            const u = new SpeechSynthesisUtterance(texto);
            u.lang = 'es-ES';
            u.rate = 0.9;
            window.speechSynthesis.speak(u);
        }

        let zonasLlenas = new Set();

        function mostrarRonda() {
            const ronda = rondas[indiceRonda];
            mensajeEl.textContent = '';
            mensajeEl.className = 'mensaje';
            btnSiguiente.style.display = 'none';
            zonasLlenas = new Set();

            instruccionEl.textContent = ronda.instruccion || '';
            btnEscuchar.onclick = () => hablar(ronda.instruccion || '');

            contenedorZonas.innerHTML = '';
            (ronda.zonas || []).forEach(zona => {
                const div = document.createElement('div');
                div.className = 'zona';
                div.dataset.zonaId = zona.id;
                div.innerHTML = (zona.emoji ? `<span>${zona.emoji}</span>` : '') +
                    `<span class="zona-etiqueta">${zona.etiqueta ?? ''}</span>`;
                contenedorZonas.appendChild(div);
            });

            contenedorPiezas.innerHTML = '';
            // Mezclar el orden de las piezas para que no siempre esté la correcta primero.
            const piezas = [...(ronda.piezas || [])].sort(() => Math.random() - 0.5);
            piezas.forEach(pieza => {
                const div = document.createElement('div');
                div.className = 'pieza';
                div.dataset.piezaZona = pieza.zona ?? '';
                div.innerHTML = (pieza.emoji ? `<span>${pieza.emoji}</span>` : '') +
                    (pieza.etiqueta ? `<span class="pieza-etiqueta">${pieza.etiqueta}</span>` : '');
                contenedorPiezas.appendChild(div);
                activarArrastre(div);
            });

            dibujarBarraProgreso();

            // Anuncia la ronda en voz alta automáticamente.
            hablar(ronda.instruccion || '');
        }

        function centro(rect) {
            return { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
        }

        function zonaBajoPunto(x, y) {
            const zonas = contenedorZonas.querySelectorAll('.zona:not(.llena)');
            for (const zona of zonas) {
                const r = zona.getBoundingClientRect();
                if (x >= r.left && x <= r.right && y >= r.top && y <= r.bottom) return zona;
            }
            return null;
        }

        function activarArrastre(pieza) {
            let offsetX = 0, offsetY = 0;
            let posOriginal = null;
            let arrastrando = false;

            pieza.addEventListener('pointerdown', (e) => {
                if (pieza.classList.contains('oculta')) return;
                arrastrando = true;
                pieza.setPointerCapture(e.pointerId);
                pieza.classList.add('arrastrando');

                const rect = pieza.getBoundingClientRect();
                posOriginal = { left: rect.left, top: rect.top };
                offsetX = e.clientX - rect.left;
                offsetY = e.clientY - rect.top;

                pieza.style.position = 'fixed';
                pieza.style.left = rect.left + 'px';
                pieza.style.top = rect.top + 'px';
                pieza.style.width = rect.width + 'px';
                pieza.style.margin = '0';
            });

            pieza.addEventListener('pointermove', (e) => {
                if (!arrastrando) return;
                const x = e.clientX - offsetX;
                const y = e.clientY - offsetY;
                pieza.style.left = x + 'px';
                pieza.style.top = y + 'px';

                const c = centro(pieza.getBoundingClientRect());
                document.querySelectorAll('.zona').forEach(z => z.classList.remove('resaltada'));
                const zonaActiva = zonaBajoPunto(c.x, c.y);
                if (zonaActiva) zonaActiva.classList.add('resaltada');
            });

            function soltar(e) {
                if (!arrastrando) return;
                arrastrando = false;
                pieza.classList.remove('arrastrando');
                document.querySelectorAll('.zona').forEach(z => z.classList.remove('resaltada'));

                const c = centro(pieza.getBoundingClientRect());
                const zona = zonaBajoPunto(c.x, c.y);

                if (zona && zona.dataset.zonaId === pieza.dataset.piezaZona) {
                    // Acierto: la pieza se "ancla" dentro de la zona.
                    zona.classList.add('llena');
                    zonasLlenas.add(zona.dataset.zonaId);
                    pieza.classList.add('oculta');
                    hablar('¡Correcto!');

                    const totalZonas = rondas[indiceRonda].zonas.length;
                    if (zonasLlenas.size >= totalZonas) {
                        mensajeEl.textContent = '¡Muy bien! 🎉';
                        mensajeEl.classList.add('correcto');
                        btnSiguiente.style.display = 'inline-block';
                    }
                } else {
                    // Error: la pieza regresa suavemente a su lugar, sin penalizar.
                    pieza.classList.add('rebotando');
                    setTimeout(() => pieza.classList.remove('rebotando'), 300);
                    pieza.style.left = posOriginal.left + 'px';
                    pieza.style.top = posOriginal.top + 'px';
                }
            }

            pieza.addEventListener('pointerup', soltar);
            pieza.addEventListener('pointercancel', soltar);
        }

        btnSiguiente.onclick = () => {
            indiceRonda++;
            if (indiceRonda < rondas.length) {
                mostrarRonda();
            } else {
                pantallaJuego.style.display = 'none';
                pantallaFinal.style.display = 'block';
            }
        };

        mostrarRonda();
    </script>

<?php endif; ?>

</body>
</html>
