<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit(); }

csrf_verificar();

require_once __DIR__ . "/../../controllers/ContenidoActividadController.php";

$idContenido = (int) ($_POST["id_contenido"] ?? 0);
$idActividad = (int) ($_POST["id_actividad"] ?? 0);

if ($idContenido > 0) {
    $contenidoController = new ContenidoActividadController();
    $contenidoController->eliminar($idContenido);
}

header("Location: contenido.php?id_actividad=" . $idActividad);
exit();