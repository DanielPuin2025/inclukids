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
<style>
    :root {
        --color-fondo: #FFFBF0;
        --color-primario: #4A90A4;
        --color-primario-oscuro: #366E7E;
        --color-texto: #2C3338;
        --color-correcto: #3C9D5C;
    }
    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    html, body { touch-action: none; }
    body {
        margin: 0;
        font-family: 'Segoe UI', Arial, sans-serif;
        background: var(--color-fondo);
        color: var(--color-texto);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 24px 16px 60px;
        user-select: none;
    }
    .barra-progreso { display: flex; gap: 8px; margin-bottom: 20px; }
    .punto { width: 16px; height: 16px; border-radius: 50%; background: #E0DCC8; }
    .punto.activo { background: var(--color-primario); }
    .punto.hecho { background: var(--color-correcto); }

    h1 { font-size: 20px; color: var(--color-primario-oscuro); margin: 0 0 4px; text-align: center; }
    .instruccion { font-size: 22px; font-weight: bold; text-align: center; max-width: 600px; margin: 12px 0 8px; }
    .btn-escuchar {
        display: inline-flex; align-items: center; gap: 8px; margin-top: 4px; margin-bottom: 28px;
        padding: 8px 18px; border-radius: 999px; border: none; background: white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08); font-size: 15px; color: var(--color-primario-oscuro); cursor: pointer;
    }

    .fila-slots { display: flex; gap: 14px; justify-content: center; margin-bottom: 40px; flex-wrap: wrap; }
    .slot {
        width: 110px;
        height: 110px;
        border-radius: 16px;
        border: 3px dashed #C9C2A6;
        background: rgba(255,255,255,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }
    .slot.resaltado { border-color: var(--color-primario); background: rgba(74,144,164,0.12); }
    .slot .numero {
        position: absolute;
        top: -10px;
        left: -10px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--color-primario);
        color: white;
        font-size: 13px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bandeja { display: flex; flex-wrap: wrap; gap: 16px; justify-content: center; max-width: 700px; min-height: 100px; }

    .ficha {
        width: 100px;
        height: 100px;
        border-radius: 14px;
        background: white;
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: bold;
        cursor: grab;
        touch-action: none;
        position: relative;
        transition: box-shadow 0.15s ease;
    }
    .ficha .ficha-etiqueta { font-size: 12px; font-weight: normal; margin-top: 2px; }
    .ficha.arrastrando { box-shadow: 0 8px 20px rgba(0,0,0,0.22); cursor: grabbing; z-index: 50; }

    /* Estilo "rompecabezas": muescas simples a los lados con pseudo-elementos */
    body.rompecabezas .ficha {
        border-radius: 10px;
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

    .mensaje { font-size: 20px; font-weight: bold; margin-top: 22px; min-height: 30px; text-align: center; }
    .mensaje.correcto { color: var(--color-correcto); }
    .mensaje.intentando { color: var(--color-primario-oscuro); }

    .btn-comprobar, .btn-siguiente {
        margin-top: 10px;
        font-size: 18px;
        font-weight: bold;
        padding: 12px 34px;
        border-radius: 14px;
        border: none;
        background: var(--color-primario);
        color: white;
        cursor: pointer;
    }
    .btn-siguiente { display: none; }

    .pantalla-final { text-align: center; display: none; }
    .pantalla-final h2 { font-size: 36px; color: var(--color-correcto); }
    .btn-volver {
        display: inline-block; margin-top: 20px; font-size: 18px; padding: 12px 30px;
        border-radius: 12px; background: var(--color-primario); color: white; text-decoration: none;
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
            }
        };

        mostrarRonda();
    </script>

<?php endif; ?>

</body>
</html>
