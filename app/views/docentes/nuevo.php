<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

$docente = null; // modo "nuevo": no hay datos previos que precargar
?>
<?php render_head("Nuevo docente"); ?>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <h2 class="mt-3 mb-4">🧑‍🏫 Nuevo docente</h2>

    <?php include __DIR__ . "/_formulario.php"; ?>

</div>
</body>
</html>
