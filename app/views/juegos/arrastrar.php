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
<style>
    :root {
        --color-fondo: #FFFBF0;
        --color-primario: #4A90A4;
        --color-primario-oscuro: #366E7E;
        --color-texto: #2C3338;
        --color-correcto: #3C9D5C;
        --color-incorrecto: #C0392B;
        --color-zona: #E9E4D0;
    }
    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    html, body {
        touch-action: none;
    }
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
    .instruccion {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        max-width: 600px;
        margin: 12px 0 28px;
    }
    .btn-escuchar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        padding: 8px 18px;
        border-radius: 999px;
        border: none;
        background: white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        font-size: 15px;
        color: var(--color-primario-oscuro);
        cursor: pointer;
    }

    .zonas { display: flex; flex-wrap: wrap; gap: 24px; justify-content: center; margin-bottom: 40px; }
    .zona {
        width: 140px;
        height: 140px;
        border-radius: 24px;
        border: 4px dashed #C9C2A6;
        background: rgba(255,255,255,0.6);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        transition: border-color 0.2s ease, background-color 0.2s ease, transform 0.15s ease;
    }
    .zona .zona-etiqueta { font-size: 20px; font-weight: bold; margin-top: 4px; color: var(--color-primario-oscuro); }
    .zona.resaltada { border-color: var(--color-primario); background: rgba(74,144,164,0.12); transform: scale(1.04); }
    .zona.llena { border-style: solid; border-color: var(--color-correcto); background: rgba(60,157,92,0.12); }

    .piezas {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        max-width: 700px;
    }
    .pieza {
        width: 110px;
        height: 110px;
        border-radius: 20px;
        background: white;
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 42px;
        cursor: grab;
        touch-action: none;
        position: relative;
        transition: box-shadow 0.15s ease;
    }
    .pieza .pieza-etiqueta { font-size: 13px; margin-top: 2px; color: var(--color-texto); }
    .pieza.arrastrando { box-shadow: 0 8px 20px rgba(0,0,0,0.22); cursor: grabbing; z-index: 50; }
    .pieza.oculta { visibility: hidden; pointer-events: none; }

    @keyframes rebote {
        0%, 100% { transform: translate(0,0); }
        25% { transform: translate(-8px, 0); }
        75% { transform: translate(8px, 0); }
    }
    .pieza.rebotando { animation: rebote 0.3s ease; }

    .mensaje { font-size: 22px; font-weight: bold; margin-top: 26px; min-height: 30px; }
    .mensaje.correcto { color: var(--color-correcto); }

    .btn-siguiente {
        margin-top: 16px;
        font-size: 20px;
        font-weight: bold;
        padding: 14px 40px;
        border-radius: 14px;
        border: none;
        background: var(--color-primario);
        color: white;
        cursor: pointer;
        display: none;
    }
    .pantalla-final { text-align: center; display: none; }
    .pantalla-final h2 { font-size: 36px; color: var(--color-correcto); }
    .btn-volver {
        display: inline-block;
        margin-top: 20px;
        font-size: 18px;
        padding: 12px 30px;
        border-radius: 12px;
        background: var(--color-primario);
        color: white;
        text-decoration: none;
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
