<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/MateriaController.php";

$materiaController = new MateriaController();
$materias = $materiaController->listar();
$dba = null;
?>
<?php render_head("Nuevo DBA"); ?>
<body>
<div class="container mt-5" style="max-width: 500px;">
    <h2 class="mt-3 mb-4">Nuevo DBA</h2>
    <?php include __DIR__ . "/_formulario.php"; ?>
</div>
</body>
</html>