<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/DbaController.php";
require_once __DIR__ . "/../../controllers/TipoActividadController.php";

$dbaController = new DbaController();
$dbaLista = $dbaController->listar();

$tipoController = new TipoActividadController();
$tipos = $tipoController->listar();

$actividad = null;
?>
<?php render_head("Nueva actividad"); ?>
<body>
<div class="container mt-5" style="max-width: 600px;">
    <h2 class="mt-3 mb-4">Nueva actividad</h2>
    <?php include __DIR__ . "/_formulario.php"; ?>
</div>
</body>
</html>