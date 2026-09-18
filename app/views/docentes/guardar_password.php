<?php
require __DIR__ . "/../../bootstrap.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: perfil.php"); exit(); }

csrf_verificar();

require_once __DIR__ . "/../../controllers/DocenteController.php";

$nueva = $_POST["nueva"] ?? "";
$confirmar = $_POST["confirmar"] ?? "";

$errorPolitica = password_politica_error($nueva);
if ($errorPolitica !== null) {
    flash_set("error", $errorPolitica);
    header("Location: perfil.php");
    exit();
}

if ($nueva !== $confirmar) {
    flash_set("error", "Las contraseñas no coinciden.");
    header("Location: perfil.php");
    exit();
}

$docenteController = new DocenteController();
$docenteController->cambiarContrasena($_SESSION["id_docente"], $nueva);

// Ya cumplió: se levanta la obligación de cambiarla (si la había).
unset($_SESSION["forzar_cambio_password"]);

flash_set("exito", "Tu contraseña se actualizó correctamente.");
header("Location: perfil.php");
exit();