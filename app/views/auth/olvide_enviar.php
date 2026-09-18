<?php
require_once __DIR__ . "/../../helpers/session_boot.php";
sesion_iniciar();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: olvide_password.php");
    exit();
}

require_once __DIR__ . "/../../helpers/csrf.php";
csrf_verificar();

require_once __DIR__ . "/../../controllers/DocenteController.php";

$correo = trim($_POST["correo"] ?? "");

if ($correo === "") {
    $_SESSION["error_olvide"] = "Escribe tu correo.";
    header("Location: olvide_password.php");
    exit();
}

$docenteController = new DocenteController();
$docente = $docenteController->buscarPorCorreo($correo);

// Mismo mensaje exista o no esa cuenta: si cambiara según el caso,
// cualquiera podría usar este formulario para averiguar qué correos
// están registrados. Las cuentas de administrador no pasan por aquí
// (ver scripts/reset_admin_password.php), así que tampoco se marcan.
if ($docente && $docente["rol"] !== "administrador") {
    $docenteController->marcarSolicitudPassword((int) $docente["id_docente"]);
}

header("Location: ../../../index.php?solicitud=1");
exit();
