<?php
require_once __DIR__ . "/../../helpers/session_boot.php";
sesion_iniciar();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../helpers/juegos.php";
require_once __DIR__ . "/../../controllers/ActividadController.php";
require_once __DIR__ . "/../../controllers/ContenidoActividadController.php";

$idActividad = (int) ($_GET["id_actividad"] ?? 0);
$idSesion = (int) ($_GET["id_sesion"] ?? 0);

// Sin id_sesion se entra en "vista previa": el docente abre el juego
// desde el catálogo para explorarlo, sin una sesión de clase detrás.
$urlVolver = juego_url_volver($idSesion);
$textoVolver = $idSesion > 0 ? "Volver a la sesión" : "Salir de la prueba";

$actividadController = new ActividadController();
$actividad = $actividadController->obtenerPorId($idActividad);

if (!$actividad) {
    die("Actividad no encontrada.");
}

$contenidoController = new ContenidoActividadController();
$preguntas = $contenidoController->listarPorActividad($idActividad);
$totalPreguntas = count($preguntas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($actividad['nombre']) ?></title>
<link href="/IncluKids/public/css/juegos.css" rel="stylesheet">
<<<<<<< HEAD
<link href="/IncluKids/public/css/juego-salida.css?v=1" rel="stylesheet">
=======
>>>>>>> upstream/main
<link href="/IncluKids/public/css/a11y.css?v=10" rel="stylesheet">
<script src="/IncluKids/public/js/a11y.js?v=10" defer></script>
<script src="/IncluKids/public/js/juegos-voz.js?v=3" defer></script>
</head>
<body class="juego">

<?php include __DIR__ . "/_salir.php"; ?>

<div class="juego-encabezado">
    <h1><?= htmlspecialchars($actividad['nombre']) ?></h1>
</div>

<?php $notasDocente = trim((string) ($actividad['instrucciones'] ?? '')); ?>
<?php $objetivoDocente = trim((string) ($actividad['descripcion'] ?? '')); ?>
<?php if ($notasDocente !== '' || $objetivoDocente !== ''): ?>
<div id="notas-docente" hidden data-objetivo="<?= htmlspecialchars($objetivoDocente, ENT_QUOTES) ?>"><?= htmlspecialchars($notasDocente, ENT_QUOTES) ?></div>
<?php endif; ?>

<?php if (empty($preguntas)): ?>

    <p style="text-align:center; max-width: 520px; color:#5B6B66;">
        Esta actividad todavía no tiene preguntas cargadas.
        Agrega contenido desde "Actividades" &rarr; "Preguntas" para
        esta actividad (id_actividad = <?= $idActividad ?>).
    </p>
    <a href="<?= htmlspecialchars($urlVolver) ?>" class="btn-volver">&larr; Volver</a>

<?php else: ?>

    <!-- Pantalla de inicio: el juego no arranca hasta pulsar "Empezar" -->
    <div class="juego-inicio" id="pantallaInicio">
        <div class="juego-inicio__emoji" aria-hidden="true">🧩</div>
        <h2>¿List@ para empezar?</h2>
        <p>
            Vas a responder <?= $totalPreguntas ?>
            <?= $totalPreguntas === 1 ? "pregunta" : "preguntas" ?>.
            Tómate tu tiempo.
        </p>
        <button type="button" class="btn-primario" id="btnEmpezar">Empezar</button>
    </div>

    <!-- Juego -->
    <div id="pantallaJuego" hidden>
        <div class="juego-paso" data-juego-paso>Paso 1 de <?= $totalPreguntas ?></div>
        <div class="juego-progreso" id="barraProgreso"></div>

        <p class="juego-instruccion" id="textoPregunta"></p>

        <div style="text-align:center; margin-bottom: 26px;">
            <button type="button" class="btn-escuchar" id="btnEscuchar" aria-pressed="false">
                🔊 Escuchar la pregunta
            </button>
        </div>

        <div class="opciones" id="contenedorOpciones"></div>

        <div class="juego-mensaje" id="mensajeFeedback" role="status" aria-live="polite"></div>

        <div style="text-align:center;">
            <button type="button" class="btn-primario" id="btnContinuar" hidden>Continuar ▶</button>
        </div>
    </div>

    <!-- Pantalla final: la rellena IncluKidsJuegos.pantallaFinal() -->
    <div class="juego-final" id="pantallaFinal" hidden></div>

<?php endif; ?>

<script src="/IncluKids/public/js/juegos-comun.js?v=2"></script>

<?php if (!empty($preguntas)): ?>
<script>
    const preguntas = <?= json_encode($preguntas, JSON_UNESCAPED_UNICODE) ?>;
    const URL_VOLVER = <?= json_encode($urlVolver) ?>;

    let indiceActual = 0;
    let resuelta = false; // true cuando ya se acertó la pregunta actual
    let fallos = 0;       // opciones equivocadas tocadas en toda la actividad

    const pantallaInicio = document.getElementById('pantallaInicio');
    const pantallaJuego = document.getElementById('pantallaJuego');
    const pantallaFinal = document.getElementById('pantallaFinal');
    const barraProgreso = document.getElementById('barraProgreso');
    const textoPregunta = document.getElementById('textoPregunta');
    const contenedorOpciones = document.getElementById('contenedorOpciones');
    const mensajeFeedback = document.getElementById('mensajeFeedback');
    const btnContinuar = document.getElementById('btnContinuar');
    const btnEscuchar = document.getElementById('btnEscuchar');
    const btnEmpezar = document.getElementById('btnEmpezar');

    function preguntaActual() {
        return preguntas[indiceActual];
    }

    function opcionesDe(p) {
        return [
            { letra: 'A', texto: p.opcion_a },
            { letra: 'B', texto: p.opcion_b },
            { letra: 'C', texto: p.opcion_c },
            { letra: 'D', texto: p.opcion_d },
        ].filter(op => op.texto !== null && op.texto !== '');
    }

    function mostrarPregunta() {
        const p = preguntaActual();
        resuelta = false;

        textoPregunta.textContent = p.pregunta;
        mensajeFeedback.textContent = '';
        mensajeFeedback.className = 'juego-mensaje';
        btnContinuar.hidden = true;
        btnEscuchar.setAttribute('aria-pressed', 'false');
        contenedorOpciones.innerHTML = '';

        opcionesDe(p).forEach(op => {
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'opcion';
            boton.textContent = op.texto;
            boton.addEventListener('click', () => responder(op.letra, boton));
            contenedorOpciones.appendChild(boton);
        });

        IncluKidsJuegos.barraProgreso(barraProgreso, preguntas.length, indiceActual);
        IncluKidsJuegos.foco(contenedorOpciones.firstElementChild);
    }

    function responder(letraElegida, botonElegido) {
        if (resuelta || botonElegido.disabled) return;
        const p = preguntaActual();

        if (letraElegida === p.respuesta_correcta) {
            resuelta = true;
            botonElegido.classList.remove('no-es-esa');
            botonElegido.classList.add('acierto');
            Array.from(contenedorOpciones.children).forEach(b => { b.disabled = true; });

            mensajeFeedback.textContent = '¡Muy bien!';
            mensajeFeedback.className = 'juego-mensaje acierto';

            btnContinuar.hidden = false;
            IncluKidsJuegos.foco(btnContinuar);
            IncluKidsJuegos.marcarAcierto();
        } else {
            // Sin castigo: se marca en neutro (nunca rojo) y se puede
            // volver a intentar cuantas veces haga falta.
            fallos++;
            botonElegido.classList.add('no-es-esa');
            mensajeFeedback.textContent = 'Inténtalo otra vez';
            mensajeFeedback.className = 'juego-mensaje neutro';
            IncluKidsJuegos.marcarError();
        }
    }

    btnEscuchar.addEventListener('click', () => {
        btnEscuchar.setAttribute('aria-pressed', 'true');
        // Lee la pregunta y también cada opción, para el estudiante que
        // solo mira y escucha.
        const p = preguntaActual();
        const ops = opcionesDe(p).map((op, i) => (i + 1) + ': ' + op.texto).join('. ');
        IncluKidsJuegos.hablar(p.pregunta + '. Opciones. ' + ops);
    });

    btnContinuar.addEventListener('click', () => {
        indiceActual++;
        if (indiceActual < preguntas.length) {
            mostrarPregunta();
        } else {
            pantallaJuego.hidden = true;
            IncluKidsJuegos.pantallaFinal(pantallaFinal, {
                titulo: '¡Terminaste todas las preguntas!',
                urlVolver: URL_VOLVER,
                textoVolver: <?= json_encode("← " . $textoVolver) ?>,
                detalle: preguntas.length + (preguntas.length === 1 ? ' pregunta' : ' preguntas') +
                    (fallos > 0 ? ' · ayudas: ' + fallos : ' · sin ayudas 🌟')
            });
        }
    });

    btnEmpezar.addEventListener('click', () => {
        pantallaInicio.hidden = true;
        pantallaJuego.hidden = false;
        mostrarPregunta();
    });
</script>
<?php endif; ?>

</body>
</html>
