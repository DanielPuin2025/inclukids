<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit(); }

csrf_verificar();

require_once __DIR__ . "/../../controllers/ContenidoActividadController.php";

$idActividad = (int) ($_POST["id_actividad"] ?? 0);
$pregunta = trim($_POST["pregunta"] ?? "");
$opcionA = trim($_POST["opcion_a"] ?? "");
$opcionB = trim($_POST["opcion_b"] ?? "");
$opcionC = trim($_POST["opcion_c"] ?? "");
$opcionD = trim($_POST["opcion_d"] ?? "");
$respuestaCorrecta = $_POST["respuesta_correcta"] ?? "";
$orden = (int) ($_POST["orden"] ?? 1);

$volver = "contenido.php?id_actividad=" . $idActividad;

if ($idActividad === 0 || $pregunta === "" || $opcionA === "" || $opcionB === "") {
    flash_set("error", "Faltan datos: la pregunta y las opciones A y B son obligatorias.");
    header("Location: $volver");
    exit();
}

if (($respuestaCorrecta === "C" && $opcionC === "") || ($respuestaCorrecta === "D" && $opcionD === "")) {
    flash_set("error", "Marcaste una opción como correcta pero la dejaste vacía.");
    header("Location: $volver");
    exit();
}

$contenidoController = new ContenidoActividadController();
$contenidoController->crear($idActividad, $pregunta, $opcionA, $opcionB, $opcionC, $opcionD, $respuestaCorrecta, $orden);
flash_set("exito", "Pregunta agregada.");

header("Location: $volver");
exit();