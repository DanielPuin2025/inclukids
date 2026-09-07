<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit(); }

require_once __DIR__ . "/../../helpers/csrf.php";
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

if ($idActividad === 0 || $pregunta === "" || $opcionA === "" || $opcionB === "") {
    die("Faltan datos. <a href='javascript:history.back()'>Volver</a>");
}

if (($respuestaCorrecta === "C" && $opcionC === "") || ($respuestaCorrecta === "D" && $opcionD === "")) {
    die("Marcaste una opción como correcta pero la dejaste vacía. <a href='javascript:history.back()'>Volver</a>");
}

$contenidoController = new ContenidoActividadController();
$contenidoController->crear($idActividad, $pregunta, $opcionA, $opcionB, $opcionC, $opcionD, $respuestaCorrecta, $orden);

header("Location: contenido.php?id_actividad=" . $idActividad);
exit();