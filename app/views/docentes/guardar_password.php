<?php
require __DIR__ . "/../../bootstrap.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: perfil.php"); exit(); }

csrf_verificar();

require_once __DIR__ . "/../../controllers/DocenteController.php";

$nueva = $_POST["nueva"] ?? "";
$confirmar = $_POST["confirmar"] ?? "";

if (strlen($nueva) < 6) {
    die("La contraseña debe tener al menos 6 caracteres. <a href='perfil.php'>Volver</a>");
}

if ($nueva !== $confirmar) {
    die("Las contraseñas no coinciden. <a href='perfil.php'>Volver</a>");
}

$docenteController = new DocenteController();
$docenteController->cambiarContrasena($_SESSION["id_docente"], $nueva);

$_SESSION["mensaje_perfil"] = "Tu contraseña se actualizó correctamente.";
header("Location: perfil.php");
exit();