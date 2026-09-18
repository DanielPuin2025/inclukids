<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/DocenteController.php";

$docenteController = new DocenteController();
$docentes = $docenteController->listar();

$curso = null; // modo "nuevo": no hay datos previos que precargar
?>
<?php render_head("Nuevo curso"); ?>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <h2 class="mt-3 mb-4">Nuevo curso</h2>

    <?php include __DIR__ . "/_formulario.php"; ?>

</div>
</body>
</html>