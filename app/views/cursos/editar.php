<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/DocenteController.php";

$idCurso = (int) ($_GET["id_curso"] ?? 0);

$cursoController = new CursoController();
$curso = $cursoController->obtenerPorId($idCurso);

if (!$curso) {
    header("Location: index.php");
    exit();
}

$docenteController = new DocenteController();
$docentes = $docenteController->listar();
?>
<?php render_head("Editar curso"); ?>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <h2 class="mt-3 mb-4">Editar curso</h2>

    <?php include __DIR__ . "/_formulario.php"; ?>

</div>
</body>
</html>