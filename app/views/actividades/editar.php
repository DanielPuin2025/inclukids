<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/ActividadController.php";
require_once __DIR__ . "/../../controllers/DbaController.php";
require_once __DIR__ . "/../../controllers/TipoActividadController.php";

$idActividad = (int) ($_GET["id_actividad"] ?? 0);

$actividadController = new ActividadController();
$actividad = $actividadController->obtenerPorId($idActividad);

if (!$actividad) { header("Location: index.php"); exit(); }

$dbaController = new DbaController();
$dbaLista = $dbaController->listar();

$tipoController = new TipoActividadController();
$tipos = $tipoController->listar();
?>
<?php render_head("Editar actividad"); ?>
<body>
<div class="container mt-5" style="max-width: 600px;">
    <h2 class="mt-3 mb-4">Editar actividad</h2>
    <?php include __DIR__ . "/_formulario.php"; ?>
</div>
</body>
</html>