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

// Rompecabezas usa este mismo motor de secuencia, solo con look de
// piezas encajables en vez de fichas rectas.
$esRompecabezas = (int) $actividad["id_tipo"] === 3;
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
        --color-acento: #C98A2B;
        --sombra-pieza: 0 6px 16px rgba(36, 48, 58, 0.12);
        --sombra-pieza-alta: 0 14px 30px rgba(36, 48, 58, 0.24);
    }
    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    html, body { touch-action: none; }
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
    .instruccion { font-size: 32px; font-weight: 700; line-height: 1.3; text-align: center; max-width: 860px; margin: 14px 0 10px; }
    .btn-escuchar {
        display: inline-flex; align-items: center; gap: 8px; margin-top: 4px; margin-bottom: 30px;
        min-height: 52px; padding: 12px 26px; border-radius: 999px; border: 2px solid transparent; background: white;
        box-shadow: 0 3px 10px rgba(36, 48, 58, 0.1); font-family: inherit; font-size: 18px; font-weight: 600;
        color: var(--color-primario-oscuro); cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .btn-escuchar:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(36, 48, 58, 0.16); }
    .btn-escuchar:active { transform: translateY(0); }
    .btn-escuchar:focus-visible { outline: none; border-color: var(--color-primario); }

    .fila-slots { display: flex; gap: 20px; justify-content: center; margin-bottom: 48px; flex-wrap: wrap; }
    .slot {
        width: 140px;
        height: 140px;
        border-radius: 22px;
        border: 3px dashed #CBBE9C;
        background: rgba(255, 255, 255, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
    }
    .slot.resaltado {
        border-color: var(--color-primario);
        background: rgba(47, 110, 115, 0.12);
        box-shadow: 0 0 0 6px rgba(47, 110, 115, 0.14);
    }
    .slot .numero {
        position: absolute;
        top: -10px;
        left: -10px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--color-primario);
        color: white;
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(47, 110, 115, 0.35);
    }

    .bandeja { display: flex; flex-wrap: wrap; gap: 22px; justify-content: center; max-width: 900px; min-height: 120px; }

    .ficha {
        width: 128px;
        height: 128px;
        border-radius: 20px;
        background: white;
        box-shadow: var(--sombra-pieza);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        font-weight: 700;
        cursor: grab;
        touch-action: none;
        position: relative;
        transition: box-shadow 0.15s ease, transform 0.12s ease;
    }
    .ficha:hover { transform: translateY(-2px); }
    .ficha:focus-visible { outline: 3px solid var(--color-primario); outline-offset: 3px; }
    .ficha .ficha-etiqueta { font-size: 14px; font-weight: 600; margin-top: 2px; }
    .ficha.arrastrando { box-shadow: var(--sombra-pieza-alta); cursor: grabbing; z-index: 50; transform: scale(1.05); }

    /* Estilo "rompecabezas": muescas simples a los lados con pseudo-elementos */
    body.rompecabezas .ficha {
        border-radius: 12px;
    }
    body.rompecabezas .ficha::after {
        content: '';
        position: absolute;
        right: -9px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        background: white;
        border-radius: 50%;
        box-shadow: inset -2px 0 3px rgba(0,0,0,0.06);
    }

    @keyframes rebote {
        0%, 100% { transform: translate(0,0); }
        25% { transform: translate(-6px, 0); }
        75% { transform: translate(6px, 0); }
    }
    .slot.rebotando { animation: rebote 0.3s ease; }

    .mensaje { font-size: 26px; font-weight: 700; margin-top: 24px; min-height: 36px; text-align: center; }
    .mensaje.correcto { color: var(--color-correcto); }
    .mensaje.intentando { color: var(--color-primario-oscuro); }

    .btn-comprobar, .btn-siguiente {
        margin-top: 12px;
        font-family: inherit;
        font-size: 22px;
        font-weight: 700;
        min-height: 62px;
        padding: 16px 46px;
        border-radius: 999px;
        border: none;
        color: white;
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    }
    .btn-comprobar {
        background: var(--color-primario);
        box-shadow: 0 6px 18px rgba(47, 110, 115, 0.3);
    }
    .btn-comprobar:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(47, 110, 115, 0.4); }
    .btn-siguiente {
        display: none;
        background: var(--color-acento);
        box-shadow: 0 6px 18px rgba(201, 138, 43, 0.35);
    }
    .btn-siguiente:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(201, 138, 43, 0.42); }
    .btn-comprobar:active, .btn-siguiente:active { transform: translateY(0); }
    .btn-comprobar:focus-visible, .btn-siguiente:focus-visible { outline: 3px solid var(--color-primario-oscuro); outline-offset: 3px; }

    .pantalla-final { text-align: center; display: none; }
    .pantalla-final h2 { font-size: 44px; color: var(--color-correcto); }
    .btn-volver {
        display: inline-block; margin-top: 22px; min-height: 58px; font-size: 20px; font-weight: 600; padding: 16px 38px;
        border-radius: 999px; background: var(--color-primario); color: white; text-decoration: none;
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
        .ficha:hover, .ficha.arrastrando { transform: none; }
        .btn-escuchar:hover, .btn-comprobar:hover, .btn-siguiente:hover, .btn-volver:hover { transform: none; }
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
<body class="<?= $esRompecabezas ? 'rompecabezas' : '' ?>">

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

        <div class="fila-slots" id="filaSlots"></div>
        <div class="bandeja" id="bandeja"></div>

        <div style="text-align:center;">
            <div class="mensaje" id="mensaje"></div>
            <button class="btn-comprobar" id="btnComprobar">Comprobar</button>
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
        const filaSlots = document.getElementById('filaSlots');
        const bandeja = document.getElementById('bandeja');
        const mensajeEl = document.getElementById('mensaje');
        const btnComprobar = document.getElementById('btnComprobar');
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
        // botón o ficha, desde el momento en que se entra al juego. A
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
            if (e.target.closest && e.target.closest('button, .ficha, a.btn-volver, [role="button"]')) {
                tonoClic();
            }
        }, true);

        function hablar(texto) {
            if (!('speechSynthesis' in window) || !texto) return;
            window.speechSynthesis.cancel();
            const u = new SpeechSynthesisUtterance(texto);
            u.lang = 'es-ES';
            u.rate = 0.9;
            window.speechSynthesis.speak(u);
        }

        function dibujarBarraProgreso() {
            barraProgreso.innerHTML = '';
            rondas.forEach((_, i) => {
                const punto = document.createElement('div');
                punto.className = 'punto' + (i < indiceRonda ? ' hecho' : (i === indiceRonda ? ' activo' : ''));
                barraProgreso.appendChild(punto);
            });
        }

        // slots[i] = id de la pieza colocada en el slot i, o null si está vacío.
        let slots = [];

        function piezaPorId(id) {
            return rondas[indiceRonda].piezas.find(p => p.id === id);
        }

        function crearFichaEl(pieza) {
            const div = document.createElement('div');
            div.className = 'ficha';
            div.dataset.piezaId = pieza.id;
            div.innerHTML = (pieza.emoji ? `<span>${pieza.emoji}</span>` : '') +
                `<span class="ficha-etiqueta">${pieza.etiqueta ?? ''}</span>`;
            activarArrastre(div);
            return div;
        }

        function repintar() {
            // Bandeja: piezas que no están en ningún slot.
            const enSlots = new Set(slots.filter(x => x !== null));
            bandeja.innerHTML = '';
            rondas[indiceRonda].piezas.forEach(pieza => {
                if (!enSlots.has(pieza.id)) {
                    bandeja.appendChild(crearFichaEl(pieza));
                }
            });

            // Slots: colocar la ficha correspondiente si tiene una.
            const slotEls = filaSlots.querySelectorAll('.slot');
            slotEls.forEach((slotEl, i) => {
                const existente = slotEl.querySelector('.ficha');
                if (existente) existente.remove();
                const idPieza = slots[i];
                if (idPieza !== null) {
                    const pieza = piezaPorId(idPieza);
                    slotEl.appendChild(crearFichaEl(pieza));
                }
            });
        }

        function mostrarRonda() {
            const ronda = rondas[indiceRonda];
            mensajeEl.textContent = '';
            mensajeEl.className = 'mensaje';
            btnSiguiente.style.display = 'none';
            btnComprobar.style.display = 'inline-block';

            instruccionEl.textContent = ronda.instruccion || '';
            btnEscuchar.onclick = () => hablar(ronda.instruccion || '');

            slots = new Array(ronda.orden_correcto.length).fill(null);

            filaSlots.innerHTML = '';
            ronda.orden_correcto.forEach((_, i) => {
                const slotEl = document.createElement('div');
                slotEl.className = 'slot';
                slotEl.dataset.indice = i;
                slotEl.innerHTML = `<span class="numero">${i + 1}</span>`;
                filaSlots.appendChild(slotEl);
            });

            repintar();
            dibujarBarraProgreso();
            hablar(ronda.instruccion || '');
        }

        function centro(rect) {
            return { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
        }

        function slotBajoPunto(x, y) {
            const els = filaSlots.querySelectorAll('.slot');
            for (const el of els) {
                const r = el.getBoundingClientRect();
                if (x >= r.left && x <= r.right && y >= r.top && y <= r.bottom) return el;
            }
            return null;
        }

        function activarArrastre(ficha) {
            let offsetX = 0, offsetY = 0;
            let arrastrando = false;

            ficha.addEventListener('pointerdown', (e) => {
                arrastrando = true;
                ficha.setPointerCapture(e.pointerId);
                ficha.classList.add('arrastrando');

                const rect = ficha.getBoundingClientRect();
                offsetX = e.clientX - rect.left;
                offsetY = e.clientY - rect.top;

                ficha.style.position = 'fixed';
                ficha.style.left = rect.left + 'px';
                ficha.style.top = rect.top + 'px';
                ficha.style.width = rect.width + 'px';
                ficha.style.margin = '0';
            });

            ficha.addEventListener('pointermove', (e) => {
                if (!arrastrando) return;
                ficha.style.left = (e.clientX - offsetX) + 'px';
                ficha.style.top = (e.clientY - offsetY) + 'px';

                const c = centro(ficha.getBoundingClientRect());
                document.querySelectorAll('.slot').forEach(s => s.classList.remove('resaltado'));
                const slotActivo = slotBajoPunto(c.x, c.y);
                if (slotActivo) slotActivo.classList.add('resaltado');
            });

            function soltar(e) {
                if (!arrastrando) return;
                arrastrando = false;
                ficha.classList.remove('arrastrando');
                document.querySelectorAll('.slot').forEach(s => s.classList.remove('resaltado'));

                const c = centro(ficha.getBoundingClientRect());
                const slotDestino = slotBajoPunto(c.x, c.y);
                const idPieza = ficha.dataset.piezaId;

                // Quita la pieza de donde estuviera antes (slot o bandeja).
                const indicePrevio = slots.indexOf(idPieza);
                if (indicePrevio !== -1) slots[indicePrevio] = null;

                if (slotDestino) {
                    const i = parseInt(slotDestino.dataset.indice, 10);
                    // Si el slot destino ya tenía otra pieza, esa vuelve a la bandeja.
                    slots[i] = idPieza;
                }
                // Si no se soltó sobre un slot, la pieza simplemente vuelve a la bandeja (ya se quitó arriba).

                mensajeEl.textContent = '';
                mensajeEl.className = 'mensaje';
                repintar();
            }

            ficha.addEventListener('pointerup', soltar);
            ficha.addEventListener('pointercancel', soltar);
        }

        btnComprobar.onclick = () => {
            if (slots.includes(null)) {
                mensajeEl.textContent = 'Coloca todas las piezas antes de comprobar.';
                mensajeEl.className = 'mensaje intentando';
                return;
            }

            const correcto = JSON.stringify(slots) === JSON.stringify(rondas[indiceRonda].orden_correcto);

            if (correcto) {
                mensajeEl.textContent = '¡Muy bien! 🎉';
                mensajeEl.className = 'mensaje correcto';
                hablar('¡Muy bien!');
                btnComprobar.style.display = 'none';
                btnSiguiente.style.display = 'inline-block';
            } else {
                mensajeEl.textContent = 'Todavía no. Reordena las piezas e inténtalo otra vez.';
                mensajeEl.className = 'mensaje intentando';
                filaSlots.querySelectorAll('.slot').forEach(s => {
                    s.classList.add('rebotando');
                    setTimeout(() => s.classList.remove('rebotando'), 300);
                });
            }
        };

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
