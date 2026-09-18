<?php
require __DIR__ . "/../../bootstrap.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: perfil.php"); exit(); }

csrf_verificar();

require_once __DIR__ . "/../../controllers/DocenteController.php";

$nombres = trim($_POST["nombres"] ?? "");
$apellidos = trim($_POST["apellidos"] ?? "");
$correo = trim($_POST["correo"] ?? "");

if ($nombres === "" || $apellidos === "" || $correo === "") {
    flash_set("error", "Faltan datos: nombres, apellidos y correo son obligatorios.");
    header("Location: perfil.php");
    exit();
}

$docenteController = new DocenteController();

try {
    $docenteController->actualizar($_SESSION["id_docente"], $nombres, $apellidos, $correo);
} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        flash_set("error", "Ese correo ya está en uso por otra cuenta.");
    } else {
        error_log("IncluKids - fallo al guardar perfil: " . $e->getMessage());
        flash_set("error", "Ocurrió un error al guardar. Inténtalo de nuevo.");
    }
    header("Location: perfil.php");
    exit();
}

// Actualiza el nombre en la sesión, para que se refleje ya mismo
// en el círculo y el saludo del dashboard
$_SESSION["nombre"] = $nombres;

flash_set("exito", "Tus datos se actualizaron correctamente.");
header("Location: perfil.php");
exit();