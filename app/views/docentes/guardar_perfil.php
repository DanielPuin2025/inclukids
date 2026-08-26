<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: perfil.php"); exit(); }

require_once __DIR__ . "/../../controllers/DocenteController.php";

$nombres = trim($_POST["nombres"] ?? "");
$apellidos = trim($_POST["apellidos"] ?? "");
$correo = trim($_POST["correo"] ?? "");

if ($nombres === "" || $apellidos === "" || $correo === "") {
    die("Faltan datos. <a href='perfil.php'>Volver</a>");
}

$docenteController = new DocenteController();

try {
    $docenteController->actualizar($_SESSION["id_docente"], $nombres, $apellidos, $correo);
} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        die("Ese correo ya está en uso por otra cuenta. <a href='perfil.php'>Volver</a>");
    }
    die("Ocurrió un error al guardar. <a href='perfil.php'>Volver</a>");
}

// Actualiza el nombre en la sesión, para que se refleje ya mismo
// en el círculo y el saludo del dashboard
$_SESSION["nombre"] = $nombres;

$_SESSION["mensaje_perfil"] = "Tus datos se actualizaron correctamente.";
header("Location: perfil.php");
exit();