<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/DocenteController.php";

$idDocente = (int) ($_POST["id_docente"] ?? 0);

$docenteController = new DocenteController();
$docente = $docenteController->obtenerPorId($idDocente);

if (!$docente) {
    flash_set("error", "Ese docente no existe.");
    header("Location: index.php");
    exit();
}

// El reset por el panel es solo para cuentas de docente. Un
// administrador cambia su propia contraseña desde "Mi perfil" (o, si la
// perdió del todo, con scripts/reset_admin_password.php).
if ($docente["rol"] === "administrador") {
    flash_set("error", "No se restablece la contraseña de una cuenta de administrador desde aquí.");
    header("Location: index.php");
    exit();
}

// Contraseña temporal legible: dos bloques cortos separados por guion.
$temporal = "IK-" . random_int(1000, 9999) . "-" . bin2hex(random_bytes(2));

$docenteController->cambiarContrasena($idDocente, $temporal);

auditar("reset_password", "docente", $idDocente,
    trim(($docente["nombres"] ?? "") . " " . ($docente["apellidos"] ?? "")) . " — restablecida por admin");

flash_set("exito",
    "Contraseña temporal para " . $docente["nombres"] . " " . $docente["apellidos"]
    . ": " . $temporal . " — pásasela por un canal seguro y pídele que la cambie al entrar (Mi perfil).");

header("Location: index.php");
exit();
