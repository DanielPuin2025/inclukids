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
        font-size: 27px;
        font-weight: 600;
        color: var(--color-primario-oscuro);
        margin: 0 0 4px;
        text-align: center;
        letter-spacing: -0.01em;
    }
    .instruccion {
        font-size: 34px;
        font-weight: 700;
        line-height: 1.3;
        text-align: center;
        max-width: 860px;
        margin: 14px 0 30px;
    }
    .btn-escuchar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        min-height: 52px;
        padding: 12px 26px;
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

    .zonas { display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; margin-bottom: 48px; }
    .zona {
        width: 176px;
        height: 176px;
        border-radius: 28px;
        border: 4px dashed #CBBE9C;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 58px;
        transition: border-color 0.2s ease, background-color 0.2s ease,
                    transform 0.15s ease, box-shadow 0.2s ease;
    }
    .zona .zona-etiqueta { font-size: 23px; font-weight: 700; margin-top: 4px; color: var(--color-primario-oscuro); }
    .zona-imagen { width: 44%; max-width: 68px; aspect-ratio: 1 / 1; border-radius: 14px; pointer-events: none; }
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
        gap: 26px;
        justify-content: center;
        max-width: 900px;
    }
    .pieza {
        width: 138px;
        height: 138px;
        border-radius: 24px;
        background: white;
        box-shadow: var(--sombra-pieza);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 52px;
        cursor: grab;
        touch-action: none;
        position: relative;
        transition: box-shadow 0.15s ease, transform 0.12s ease;
    }
    .pieza:hover { transform: translateY(-2px); }
    .pieza:focus-visible { outline: 3px solid var(--color-primario); outline-offset: 3px; }
    .pieza .pieza-etiqueta { font-size: 15px; font-weight: 600; margin-top: 2px; color: var(--color-texto); }
    .pieza-imagen { width: 60%; max-width: 76px; aspect-ratio: 1 / 1; border-radius: 14px; pointer-events: none; }
    .pieza.arrastrando { box-shadow: var(--sombra-pieza-alta); cursor: grabbing; z-index: 50; transform: scale(1.05); }
    .pieza.oculta { visibility: hidden; pointer-events: none; }

    @keyframes rebote {
        0%, 100% { transform: translate(0,0); }
        25% { transform: translate(-8px, 0); }
        75% { transform: translate(8px, 0); }
    }
    .pieza.rebotando { animation: rebote 0.3s ease; }

    .mensaje { font-size: 28px; font-weight: 700; margin-top: 28px; min-height: 36px; }
    .mensaje.correcto { color: var(--color-correcto); }

    .btn-siguiente {
        margin-top: 18px;
        font-family: inherit;
        font-size: 24px;
        font-weight: 700;
        min-height: 62px;
        padding: 16px 50px;
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
    .pantalla-final h2 { font-size: 44px; color: var(--color-correcto); }
    .btn-volver {
        display: inline-block;
        margin-top: 22px;
        min-height: 58px;
        font-size: 20px;
        font-weight: 600;
        padding: 16px 38px;
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

    /* Confeti al terminar TODA la actividad — solo si el niño activó
       "más color y premios" en el engranaje de ajustes (Seleccionar).
       Nunca aparece durante el juego, solo una vez al final. */
    .confeti-capa { position: fixed; inset: 0; pointer-events: none; overflow: hidden; z-index: 200; }
    .confeti-pieza {
        position: absolute; top: -24px;
        width: var(--tam, 10px); height: var(--tam, 10px);
        background: var(--color, var(--color-primario));
        border-radius: 3px; opacity: 0.9;
        animation: confeti-caer var(--duracion, 2200ms) ease-in var(--retraso, 0ms) forwards;
    }
    @keyframes confeti-caer {
        0% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0.95; }
        85% { opacity: 0.9; }
        100% { transform: translateY(105vh) translateX(var(--deriva, 40px)) rotate(var(--giro, 300deg)); opacity: 0; }
    }
</style>
<link href="/IncluKids/public/css/a11y.css?v=3" rel="stylesheet">
<script src="/IncluKids/public/js/a11y.js?v=3" defer></script>
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

        // Confeti + sonido de cierre: solo el "premio extra" de quien
        // activó "más color y premios" (mismo ajuste que usa el juego de
        // Seleccionar, guardado en localStorage y compartido entre todos
        // los juegos aunque este motor no tenga su propio engranaje).
        function celebrarFinal() {
            let prefsEstimulo = false;
            try { prefsEstimulo = !!(JSON.parse(localStorage.getItem('inclukids_juegos') || '{}').estimulo); } catch (e) {}
            if (!prefsEstimulo) return;

            if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                const colores = ['#2F6E73', '#3C9D5C', '#C98A2B', '#B24632', '#6B5B95'];
                const capa = document.createElement('div');
                capa.className = 'confeti-capa';
                capa.setAttribute('aria-hidden', 'true');
                for (let i = 0; i < 26; i++) {
                    const pieza = document.createElement('div');
                    pieza.className = 'confeti-pieza';
                    pieza.style.left = (Math.random() * 100) + 'vw';
                    pieza.style.setProperty('--tam', (6 + Math.random() * 8) + 'px');
                    pieza.style.setProperty('--duracion', (1600 + Math.random() * 1400) + 'ms');
                    pieza.style.setProperty('--retraso', (Math.random() * 400) + 'ms');
                    pieza.style.setProperty('--deriva', ((Math.random() - 0.5) * 160) + 'px');
                    pieza.style.setProperty('--giro', (180 + Math.random() * 360) + 'deg');
                    pieza.style.setProperty('--color', colores[i % colores.length]);
                    capa.appendChild(pieza);
                }
                document.body.appendChild(capa);
                setTimeout(() => capa.remove(), 3200);
            }

            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const ahora = ctx.currentTime;
                [523.25, 659.25, 783.99].forEach((frecuencia, i) => {
                    const osc = ctx.createOscillator();
                    const ganancia = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.value = frecuencia;
                    ganancia.gain.setValueAtTime(0, ahora);
                    ganancia.gain.linearRampToValueAtTime(0.06, ahora + 0.02);
                    ganancia.gain.exponentialRampToValueAtTime(0.0001, ahora + 0.5);
                    osc.connect(ganancia);
                    ganancia.connect(ctx.destination);
                    osc.start(ahora + i * 0.09);
                    osc.stop(ahora + 0.5 + i * 0.09);
                });
            } catch (e) { /* Web Audio no disponible: no pasa nada */ }
        }

        // Sonido de toque: un "tick" corto en cuanto se toca cualquier
        // botón o pieza, desde el momento en que se entra al juego. A
        // diferencia de celebrarFinal(), esto NO depende de "más color y
        // premios" — es solo confirmar que el toque se registró.
        let ctxToque = null;
        function tonoClic() {
            try {
                if (!ctxToque) {
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (!AudioCtx) return;
                    ctxToque = new AudioCtx();
                }
                if (ctxToque.state === 'suspended') ctxToque.resume();
                const ahora = ctxToque.currentTime;
                const osc = ctxToque.createOscillator();
                const ganancia = ctxToque.createGain();
                osc.type = 'sine';
                osc.frequency.value = 700;
                ganancia.gain.setValueAtTime(0, ahora);
                ganancia.gain.linearRampToValueAtTime(0.045, ahora + 0.008);
                ganancia.gain.exponentialRampToValueAtTime(0.0001, ahora + 0.09);
                osc.connect(ganancia);
                ganancia.connect(ctxToque.destination);
                osc.start(ahora);
                osc.stop(ahora + 0.1);
            } catch (e) { /* Web Audio no disponible: no pasa nada */ }
        }
        document.addEventListener('pointerdown', (e) => {
            if (e.target.closest && e.target.closest('button, .pieza, a.btn-volver, [role="button"]')) {
                tonoClic();
            }
        }, true);

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
            if (JSON.parse(localStorage.getItem('inclukids_juegos') || '{}').voz === false) return;
            window.speechSynthesis.cancel();
            const u = new SpeechSynthesisUtterance(texto);
            u.lang = 'es-ES';
            u.rate = 0.9;
            window.speechSynthesis.speak(u);
        }

        let zonasLlenas = new Set();

        // Texto que lee el botón 🔊: la consigna + lo que hay en pantalla
        // (casillas y fichas), para el estudiante que solo mira y escucha.
        function textoParaEscuchar(ronda) {
            const partes = [ronda.instruccion || ''];
            const casillas = (ronda.zonas || []).map(z => z.etiqueta).filter(Boolean);
            const fichas = (ronda.piezas || []).map(p => p.etiqueta).filter(Boolean);
            if (casillas.length) partes.push('Casillas: ' + casillas.join(', '));
            if (fichas.length) partes.push('Fichas: ' + fichas.join(', '));
            return partes.filter(Boolean).join('. ');
        }

        function mostrarRonda() {
            const ronda = rondas[indiceRonda];
            mensajeEl.textContent = '';
            mensajeEl.className = 'mensaje';
            btnSiguiente.style.display = 'none';
            zonasLlenas = new Set();

            instruccionEl.textContent = ronda.instruccion || '';
            btnEscuchar.onclick = () => hablar(textoParaEscuchar(ronda));

            contenedorZonas.innerHTML = '';
            (ronda.zonas || []).forEach(zona => {
                const div = document.createElement('div');
                div.className = 'zona';
                div.dataset.zonaId = zona.id;
                const contenidoZona = zona.imagen
                    ? `<img class="zona-imagen" src="${zona.imagen}" alt="${zona.etiqueta ?? ''}">`
                    : (zona.emoji ? `<span>${zona.emoji}</span>` : '');
                div.innerHTML = contenidoZona +
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
                const contenidoPieza = pieza.imagen
                    ? `<img class="pieza-imagen" src="${pieza.imagen}" alt="${pieza.etiqueta ?? ''}">`
                    : (pieza.emoji ? `<span>${pieza.emoji}</span>` : '');
                div.innerHTML = contenidoPieza +
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
                celebrarFinal();
            }
        };

        mostrarRonda();
    </script>

<?php endif; ?>

</body>
</html>
