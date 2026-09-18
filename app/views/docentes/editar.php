<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/DocenteController.php";

$idDocente = (int) ($_GET["id_docente"] ?? 0);

$docenteController = new DocenteController();
$docente = $docenteController->obtenerPorId($idDocente);

if (!$docente) {
    flash_set("error", "Ese docente no existe.");
    header("Location: index.php");
    exit();
}

if ($docente["rol"] === "administrador") {
    flash_set("error", "Las cuentas de administrador no se editan desde aquí.");
    header("Location: index.php");
    exit();
}
?>
<?php render_head("Editar docente"); ?>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <h2 class="mt-3 mb-4">🧑‍🏫 Editar docente</h2>

    <?php include __DIR__ . "/_formulario.php"; ?>

</div>
</body>
</html>
