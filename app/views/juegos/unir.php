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

// Reusa el mismo contenido (zonas/piezas) que arrastrar.php — es el
// mismo dato, solo cambia cómo se juega: aquí se toca la pieza y
// luego su pareja, en vez de arrastrarla.
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
        margin: 14px 0 8px;
    }
    .pista {
        text-align: center;
        color: var(--color-primario-oscuro);
        opacity: 0.75;
        font-size: 16px;
        font-weight: 500;
        margin: 0 0 14px;
    }
    .btn-escuchar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        margin-bottom: 30px;
        min-height: 52px;
        padding: 12px 26px;
        border-radius: 999px;
        border: 2px solid transparent;
        background: white;
        box-shadow: 0 3px 10px rgba(36, 48, 58, 0.1);
        font-family: inherit;
        font-size: 18px;
        font-weight: 600;
        color: var(--color-primario-oscuro);
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .btn-escuchar:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(36, 48, 58, 0.16); }
    .btn-escuchar:active { transform: translateY(0); }
    .btn-escuchar:focus-visible { outline: none; border-color: var(--color-primario); }

    /* ---------- Tablero de unir: dos columnas + líneas conectoras ---------- */
    .unir-tablero {
        position: relative;
        display: flex;
        gap: clamp(40px, 12vw, 140px);
        justify-content: center;
        width: 100%;
        max-width: 780px;
        margin: 0 auto;
    }
    .unir-lineas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        overflow: visible;
    }
    .unir-linea {
        stroke: var(--color-correcto);
        stroke-width: 4;
        stroke-linecap: round;
    }
    .unir-columna {
        display: flex;
        flex-direction: column;
        gap: 18px;
        flex: 1;
        max-width: 320px;
    }

    .unir-item {
        font-family: inherit;
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 76px;
        padding: 14px 20px;
        border-radius: 20px;
        border: 3px solid var(--color-zona);
        background: white;
        box-shadow: var(--sombra-pieza);
        cursor: pointer;
        text-align: left;
        transition: border-color 0.15s ease, box-shadow 0.15s ease,
                    transform 0.12s ease, background-color 0.15s ease, opacity 0.2s ease;
    }
    .unir-columna--zonas .unir-item { justify-content: center; text-align: center; }
    .unir-item__emoji { font-size: 34px; line-height: 1; flex-shrink: 0; }
    .unir-item__imagen { width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0; pointer-events: none; }
    .unir-item__etiqueta { font-size: 19px; font-weight: 700; color: var(--color-primario-oscuro); }

    .unir-item:hover:not(.conectada) { transform: translateY(-2px); border-color: var(--color-primario); }
    .unir-item:focus-visible { outline: 3px solid var(--color-primario); outline-offset: 3px; }
    .unir-item:active:not(.conectada) { transform: translateY(0); }

    .unir-item.seleccionada {
        border-color: var(--color-primario);
        background: rgba(47, 110, 115, 0.10);
        box-shadow: 0 0 0 5px rgba(47, 110, 115, 0.16);
    }
    .unir-item.conectada {
        border-color: var(--color-correcto);
        background: rgba(60, 157, 92, 0.12);
        cursor: default;
        opacity: 0.75;
    }
    .unir-item.conectada:hover { transform: none; }

    @keyframes rebote {
        0%, 100% { transform: translate(0,0); }
        25% { transform: translate(-6px, 0); }
        75% { transform: translate(6px, 0); }
    }
    .unir-item.rebotando { animation: rebote 0.3s ease; }

    .mensaje { font-size: 26px; font-weight: 700; margin-top: 28px; min-height: 36px; text-align: center; }
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

    @media (max-width: 640px) {
        .unir-tablero { gap: 24px; }
        .unir-item__etiqueta { font-size: 16px; }
    }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.001ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.001ms !important;
        }
        .punto.activo { transform: none; }
        .unir-item:hover { transform: none; }
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
        <p class="pista">Toca una tarjeta y luego su pareja</p>
        <div style="text-align:center;">
            <button class="btn-escuchar" id="btnEscuchar" type="button">🔊 Escuchar</button>
        </div>

        <div class="unir-tablero" id="tablero">
            <svg class="unir-lineas" id="svgLineas"></svg>
            <div class="unir-columna" id="columnaPiezas"></div>
            <div class="unir-columna unir-columna--zonas" id="columnaZonas"></div>
        </div>

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
        const columnaPiezas = document.getElementById('columnaPiezas');
        const columnaZonas = document.getElementById('columnaZonas');
        const svgLineas = document.getElementById('svgLineas');
        const tablero = document.getElementById('tablero');
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
        // botón o tarjeta, desde el momento en que se entra al juego. A
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
            if (e.target.closest && e.target.closest('button, a.btn-volver, [role="button"]')) {
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
            window.speechSynthesis.cancel();
            const u = new SpeechSynthesisUtterance(texto);
            u.lang = 'es-ES';
            u.rate = 0.9;
            window.speechSynthesis.speak(u);
        }

        let seleccion = null;      // { lado, id, el } | null
        let conectadas = new Set();
        let totalPares = 0;
        let paresParaLinea = [];   // [{piezaEl, zonaEl}] — para volver a dibujar si cambia el tamaño de ventana

        function mostrarRonda() {
            const ronda = rondas[indiceRonda];
            mensajeEl.textContent = '';
            mensajeEl.className = 'mensaje';
            btnSiguiente.style.display = 'none';
            seleccion = null;
            conectadas = new Set();
            paresParaLinea = [];
            svgLineas.innerHTML = '';

            instruccionEl.textContent = ronda.instruccion || '';
            btnEscuchar.onclick = () => hablar(ronda.instruccion || '');

            const zonas = ronda.zonas || [];
            totalPares = zonas.length;

            columnaPiezas.innerHTML = '';
            const piezas = [...(ronda.piezas || [])].sort(() => Math.random() - 0.5);
            piezas.forEach(pieza => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'unir-item';
                btn.dataset.id = pieza.id;
                btn.dataset.zonaCorrecta = pieza.zona ?? '';
                const contenidoPieza = pieza.imagen
                    ? `<img class="unir-item__imagen" src="${pieza.imagen}" alt="">`
                    : (pieza.emoji ? `<span class="unir-item__emoji">${pieza.emoji}</span>` : '');
                btn.innerHTML = contenidoPieza +
                    (pieza.etiqueta ? `<span class="unir-item__etiqueta">${pieza.etiqueta}</span>` : '');
                btn.addEventListener('click', () => manejarClic(btn, 'pieza'));
                columnaPiezas.appendChild(btn);
            });

            columnaZonas.innerHTML = '';
            const zonasMezcladas = [...zonas].sort(() => Math.random() - 0.5);
            zonasMezcladas.forEach(zona => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'unir-item';
                btn.dataset.id = zona.id;
                const contenidoZona = zona.imagen
                    ? `<img class="unir-item__imagen" src="${zona.imagen}" alt="">`
                    : (zona.emoji ? `<span class="unir-item__emoji">${zona.emoji}</span>` : '');
                btn.innerHTML = contenidoZona +
                    `<span class="unir-item__etiqueta">${zona.etiqueta ?? ''}</span>`;
                btn.addEventListener('click', () => manejarClic(btn, 'zona'));
                columnaZonas.appendChild(btn);
            });

            dibujarBarraProgreso();
            hablar(ronda.instruccion || '');
        }

        function manejarClic(el, lado) {
            if (el.classList.contains('conectada')) return;

            if (!seleccion) {
                seleccion = { lado, el };
                el.classList.add('seleccionada');
                return;
            }

            if (seleccion.lado === lado) {
                // Se tocó otra tarjeta del MISMO lado: cambia la selección.
                seleccion.el.classList.remove('seleccionada');
                seleccion = { lado, el };
                el.classList.add('seleccionada');
                return;
            }

            // Lados opuestos: comprobar si forman pareja.
            const piezaEl = lado === 'pieza' ? el : seleccion.el;
            const zonaEl = lado === 'zona' ? el : seleccion.el;

            if (piezaEl.dataset.zonaCorrecta === zonaEl.dataset.id) {
                piezaEl.classList.remove('seleccionada');
                zonaEl.classList.remove('seleccionada');
                piezaEl.classList.add('conectada');
                zonaEl.classList.add('conectada');
                paresParaLinea.push({ piezaEl, zonaEl });
                dibujarLinea(piezaEl, zonaEl);
                conectadas.add(zonaEl.dataset.id);
                hablar('¡Correcto!');
                seleccion = null;

                if (conectadas.size >= totalPares) {
                    mensajeEl.textContent = '¡Muy bien! 🎉';
                    mensajeEl.classList.add('correcto');
                    btnSiguiente.style.display = 'inline-block';
                }
            } else {
                // Sin castigo: se marca en neutro (nunca rojo) y se puede
                // volver a intentar cuantas veces haga falta.
                piezaEl.classList.add('rebotando');
                zonaEl.classList.add('rebotando');
                setTimeout(() => {
                    piezaEl.classList.remove('rebotando');
                    zonaEl.classList.remove('rebotando');
                }, 300);
                seleccion.el.classList.remove('seleccionada');
                seleccion = null;
            }
        }

        function dibujarLinea(piezaEl, zonaEl) {
            const base = tablero.getBoundingClientRect();
            const rp = piezaEl.getBoundingClientRect();
            const rz = zonaEl.getBoundingClientRect();
            const x1 = rp.right - base.left;
            const y1 = rp.top + rp.height / 2 - base.top;
            const x2 = rz.left - base.left;
            const y2 = rz.top + rz.height / 2 - base.top;

            const linea = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            linea.setAttribute('x1', x1);
            linea.setAttribute('y1', y1);
            linea.setAttribute('x2', x2);
            linea.setAttribute('y2', y2);
            linea.setAttribute('class', 'unir-linea');
            svgLineas.appendChild(linea);
        }

        let temporizadorResize = null;
        window.addEventListener('resize', () => {
            clearTimeout(temporizadorResize);
            temporizadorResize = setTimeout(() => {
                svgLineas.innerHTML = '';
                paresParaLinea.forEach(({ piezaEl, zonaEl }) => dibujarLinea(piezaEl, zonaEl));
            }, 150);
        });

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
