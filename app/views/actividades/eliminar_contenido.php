<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/ContenidoActividadController.php";

$idContenido = (int) ($_GET["id_contenido"] ?? 0);
$idActividad = (int) ($_GET["id_actividad"] ?? 0);

if ($idContenido > 0) {
    $contenidoController = new ContenidoActividadController();
    $contenidoController->eliminar($idContenido);
}

header("Location: contenido.php?id_actividad=" . $idActividad);
exit();