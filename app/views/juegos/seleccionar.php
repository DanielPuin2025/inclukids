<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/ActividadController.php";
require_once __DIR__ . "/../../controllers/ContenidoActividadController.php";

$idActividad = (int) ($_GET["id_actividad"] ?? 0);
$idSesion = (int) ($_GET["id_sesion"] ?? 0);

$actividadController = new ActividadController();
$actividad = $actividadController->obtenerPorId($idActividad);

if (!$actividad) {
    die("Actividad no encontrada.");
}

$contenidoController = new ContenidoActividadController();
$preguntas = $contenidoController->listarPorActividad($idActividad);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($actividad['nombre']) ?></title>
<style>
    :root {
        --color-fondo: #FFFBF0;
        --color-primario: #4A90A4;
        --color-primario-oscuro: #366E7E;
        --color-texto: #2C3338;
        --color-correcto: #3C9D5C;
        --color-incorrecto: #C0392B;
    }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: 'Segoe UI', Arial, sans-serif;
        background: var(--color-fondo);
        color: var(--color-texto);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px 16px;
    }
    .barra-progreso {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
    }
    .punto {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #E0DCC8;
    }
    .punto.activo { background: var(--color-primario); }
    .punto.hecho { background: var(--color-correcto); }
    h1 {
        font-size: 20px;
        color: var(--color-primario-oscuro);
        margin: 0 0 8px 0;
        text-align: center;
    }
    .pregunta {
        font-size: 32px;
        font-weight: bold;
        text-align: center;
        max-width: 700px;
        margin: 20px 0 40px 0;
    }
    .opciones {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        width: 100%;
        max-width: 700px;
    }
    .opcion {
        font-size: 26px;
        font-weight: bold;
        padding: 30px 20px;
        border-radius: 20px;
        border: none;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        cursor: pointer;
        color: var(--color-primario-oscuro);
        transition: transform 0.1s ease;
    }
    .opcion:active { transform: scale(0.97); }
    .opcion.correcta { background: var(--color-correcto); color: white; }
    .opcion.incorrecta { background: var(--color-incorrecto); color: white; }
    .opcion:disabled { cursor: default; }
    .mensaje {
        font-size: 24px;
        font-weight: bold;
        margin-top: 30px;
        min-height: 36px;
    }
    .mensaje.correcto { color: var(--color-correcto); }
    .mensaje.incorrecto { color: var(--color-incorrecto); }
    .btn-siguiente {
        margin-top: 20px;
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
    .pantalla-final {
        text-align: center;
        display: none;
    }
    .pantalla-final h2 {
        font-size: 36px;
        color: var(--color-correcto);
    }
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

<?php if (empty($preguntas)): ?>

    <p style="text-align:center; max-width: 500px;">
        Esta actividad todavía no tiene preguntas cargadas.
        Agrega contenido desde "Actividades" &rarr; "Preguntas" para
        esta actividad (id_actividad = <?= $idActividad ?>).
    </p>
    <a href="../sesiones/confirmacion.php?id_sesion=<?= $idSesion ?>" class="btn-volver">&larr; Volver</a>

<?php else: ?>

    <div class="barra-progreso" id="barraProgreso"></div>

    <div id="pantallaJuego">
        <div class="pregunta" id="textoPregunta"></div>
        <div class="opciones" id="contenedorOpciones"></div>
        <div class="mensaje" id="mensajeFeedback"></div>
        <div style="text-align:center;">
            <button class="btn-siguiente" id="btnSiguiente">Siguiente ➡️</button>
        </div>
    </div>

    <div class="pantalla-final" id="pantallaFinal">
        <h2>🎉 ¡Actividad completada!</h2>
        <a href="../sesiones/confirmacion.php?id_sesion=<?= $idSesion ?>" class="btn-volver">&larr; Volver a la sesión</a>
    </div>

    <script>
        const preguntas = <?= json_encode($preguntas, JSON_UNESCAPED_UNICODE) ?>;
        let indiceActual = 0;

        const barraProgreso = document.getElementById('barraProgreso');
        const textoPregunta = document.getElementById('textoPregunta');
        const contenedorOpciones = document.getElementById('contenedorOpciones');
        const mensajeFeedback = document.getElementById('mensajeFeedback');
        const btnSiguiente = document.getElementById('btnSiguiente');
        const pantallaJuego = document.getElementById('pantallaJuego');
        const pantallaFinal = document.getElementById('pantallaFinal');

        function dibujarBarraProgreso() {
            barraProgreso.innerHTML = '';
            preguntas.forEach((_, i) => {
                const punto = document.createElement('div');
                punto.className = 'punto' + (i < indiceActual ? ' hecho' : (i === indiceActual ? ' activo' : ''));
                barraProgreso.appendChild(punto);
            });
        }

        function mostrarPregunta() {
            const p = preguntas[indiceActual];
            textoPregunta.textContent = p.pregunta;
            mensajeFeedback.textContent = '';
            mensajeFeedback.className = 'mensaje';
            btnSiguiente.style.display = 'none';
            contenedorOpciones.innerHTML = '';

            const opciones = [
                { letra: 'A', texto: p.opcion_a },
                { letra: 'B', texto: p.opcion_b },
                { letra: 'C', texto: p.opcion_c },
                { letra: 'D', texto: p.opcion_d },
            ].filter(op => op.texto !== null && op.texto !== '');

            opciones.forEach(op => {
                const boton = document.createElement('button');
                boton.className = 'opcion';
                boton.textContent = op.texto;
                boton.onclick = () => responder(op.letra, boton);
                contenedorOpciones.appendChild(boton);
            });

            dibujarBarraProgreso();
        }

        function responder(letraElegida, botonElegido) {
            const p = preguntas[indiceActual];
            const esCorrecta = letraElegida === p.respuesta_correcta;

            Array.from(contenedorOpciones.children).forEach(boton => {
                boton.disabled = true;
            });

            const opciones = [
                { letra: 'A', texto: p.opcion_a },
                { letra: 'B', texto: p.opcion_b },
                { letra: 'C', texto: p.opcion_c },
                { letra: 'D', texto: p.opcion_d },
            ].filter(op => op.texto !== null && op.texto !== '');

            const indiceCorrecta = opciones.findIndex(op => op.letra === p.respuesta_correcta);
            contenedorOpciones.children[indiceCorrecta].classList.add('correcta');

            if (!esCorrecta) {
                botonElegido.classList.add('incorrecta');
            }

            mensajeFeedback.textContent = esCorrecta ? '¡Muy bien! 🎉' : 'Casi... ¡vamos con la siguiente! 💪';
            mensajeFeedback.classList.add(esCorrecta ? 'correcto' : 'incorrecto');

            btnSiguiente.style.display = 'inline-block';
        }

        btnSiguiente.onclick = () => {
            indiceActual++;
            if (indiceActual < preguntas.length) {
                mostrarPregunta();
            } else {
                pantallaJuego.style.display = 'none';
                document.getElementById('barraProgreso').style.display = 'none';
                pantallaFinal.style.display = 'block';
            }
        };

        mostrarPregunta();
    </script>

<?php endif; ?>

</body>
</html>