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

$pares = [];
if ($contenido) {
    $datos = json_decode($contenido["datos_json"], true);
    $pares = $datos["pares"] ?? [];
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
        --color-acento: #C98A2B;
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
    h1 {
        font-size: 22px;
        font-weight: 600;
        color: var(--color-primario-oscuro);
        margin: 0 0 4px;
        text-align: center;
        letter-spacing: -0.01em;
    }
    .subtitulo { color: var(--color-primario-oscuro); opacity: 0.7; font-size: 15px; font-weight: 500; margin-bottom: 24px; text-align: center; }

    .tablero {
        display: grid;
        grid-template-columns: repeat(var(--columnas, 4), 1fr);
        gap: 16px;
        max-width: 640px;
        width: 100%;
    }

    .carta {
        aspect-ratio: 1 / 1;
        perspective: 800px;
        cursor: pointer;
    }
    .carta-interior {
        position: relative;
        width: 100%;
        height: 100%;
        transition: transform 0.35s ease;
        transform-style: preserve-3d;
    }
    .carta.volteada .carta-interior,
    .carta.encontrada .carta-interior {
        transform: rotateY(180deg);
    }
    .carta:focus-visible { outline: 3px solid var(--color-primario); outline-offset: 3px; border-radius: 20px; }
    .cara {
        position: absolute;
        inset: 0;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        backface-visibility: hidden;
        box-shadow: 0 6px 16px rgba(36, 48, 58, 0.12);
    }
    .cara-dorso {
        background: linear-gradient(150deg, var(--color-primario), var(--color-primario-oscuro));
        color: rgba(255, 255, 255, 0.85);
        font-size: 32px;
    }
    .cara-frente {
        background: white;
        transform: rotateY(180deg);
        flex-direction: column;
        font-size: 36px;
        padding: 6px;
        text-align: center;
    }
    .cara-frente .etiqueta { font-size: 12px; font-weight: 600; margin-top: 2px; color: var(--color-texto); }

    .carta.encontrada .cara-frente {
        background: #E7F5EC;
        outline: 3px solid var(--color-correcto);
    }

    @keyframes rebote {
        0%, 100% { transform: rotateY(180deg) translateX(0); }
        25% { transform: rotateY(180deg) translateX(-6px); }
        75% { transform: rotateY(180deg) translateX(6px); }
    }
    .carta.rebotando .carta-interior { animation: rebote 0.3s ease; }

    .mensaje { font-size: 24px; font-weight: 700; margin-top: 28px; min-height: 32px; color: var(--color-correcto); }

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
        .btn-volver:hover { transform: none; }
    }
</style>
</head>
<body>

<h1><?= htmlspecialchars($actividad['nombre']) ?></h1>

<?php if (empty($pares)): ?>

    <p style="text-align:center; max-width: 500px;">
        Este juego todavía no tiene parejas cargadas.
    </p>
    <a href="../sesiones/confirmacion.php?id_sesion=<?= $idSesion ?>" class="btn-volver">&larr; Volver</a>

<?php else: ?>

    <p class="subtitulo">Encuentra las parejas. Toca dos cartas para voltearlas.</p>

    <div id="pantallaJuego">
        <div class="tablero" id="tablero"></div>
        <div style="text-align:center;">
            <div class="mensaje" id="mensaje"></div>
        </div>
    </div>

    <div class="pantalla-final" id="pantallaFinal">
        <h2>🎉 ¡Encontraste todas las parejas!</h2>
        <a href="../sesiones/confirmacion.php?id_sesion=<?= $idSesion ?>" class="btn-volver">&larr; Volver a la sesión</a>
    </div>

    <script>
        const pares = <?= json_encode($pares, JSON_UNESCAPED_UNICODE) ?>;

        const tablero = document.getElementById('tablero');
        const mensajeEl = document.getElementById('mensaje');
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

        // Construye 2 cartas por pareja: una con la "letra", otra con la "imagen".
        let cartas = [];
        pares.forEach((par, i) => {
            cartas.push({ parejaId: i, tipo: 'letra', contenido: par.letra, etiqueta: par.letra });
            cartas.push({ parejaId: i, tipo: 'imagen', contenido: par.imagen_emoji, etiqueta: par.imagen_texto || '' });
        });
        cartas = cartas.sort(() => Math.random() - 0.5);

        const columnas = cartas.length <= 8 ? 4 : (cartas.length <= 12 ? 4 : 5);
        tablero.style.setProperty('--columnas', columnas);

        let primeraCarta = null;
        let segundaCarta = null;
        let bloqueado = false;
        let encontradas = 0;

        function crearCartaEl(carta, indice) {
            const div = document.createElement('div');
            div.className = 'carta';
            div.dataset.indice = indice;
            div.innerHTML = `
                <div class="carta-interior">
                    <div class="cara cara-dorso">❓</div>
                    <div class="cara cara-frente">
                        <span>${carta.contenido}</span>
                        ${carta.etiqueta ? `<span class="etiqueta">${carta.etiqueta}</span>` : ''}
                    </div>
                </div>
            `;
            div.addEventListener('click', () => voltear(div, carta));
            return div;
        }

        cartas.forEach((carta, i) => tablero.appendChild(crearCartaEl(carta, i)));

        function voltear(el, carta) {
            if (bloqueado) return;
            if (el.classList.contains('volteada') || el.classList.contains('encontrada')) return;

            el.classList.add('volteada');
            hablar(carta.etiqueta || carta.contenido);

            if (!primeraCarta) {
                primeraCarta = { el, carta };
                return;
            }

            segundaCarta = { el, carta };
            bloqueado = true;

            if (primeraCarta.carta.parejaId === segundaCarta.carta.parejaId) {
                setTimeout(() => {
                    primeraCarta.el.classList.add('encontrada');
                    segundaCarta.el.classList.add('encontrada');
                    encontradas++;
                    primeraCarta = null;
                    segundaCarta = null;
                    bloqueado = false;

                    if (encontradas === pares.length) {
                        mensajeEl.textContent = '¡Excelente memoria! 🎉';
                        setTimeout(() => {
                            pantallaJuego.style.display = 'none';
                            pantallaFinal.style.display = 'block';
                        }, 900);
                    }
                }, 500);
            } else {
                primeraCarta.el.classList.add('rebotando');
                segundaCarta.el.classList.add('rebotando');
                setTimeout(() => {
                    primeraCarta.el.classList.remove('volteada', 'rebotando');
                    segundaCarta.el.classList.remove('volteada', 'rebotando');
                    primeraCarta = null;
                    segundaCarta = null;
                    bloqueado = false;
                }, 900);
            }
        }
    </script>

<?php endif; ?>

</body>
</html>
