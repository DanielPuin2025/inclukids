<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro.php");
    exit();
}

require_once __DIR__ . "/../../controllers/DocenteController.php";

$nombres = trim($_POST["nombres"] ?? "");
$apellidos = trim($_POST["apellidos"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$password = $_POST["password"] ?? "";
$passwordConfirmar = $_POST["password_confirmar"] ?? "";

// Validaciones básicas
if ($nombres === "" || $apellidos === "" || $correo === "" || $password === "") {
    $_SESSION["error_registro"] = "Por favor completa todos los campos.";
    header("Location: registro.php");
    exit();
}

if (strlen($password) < 6) {
    $_SESSION["error_registro"] = "La contraseña debe tener al menos 6 caracteres.";
    header("Location: registro.php");
    exit();
}

if ($password !== $passwordConfirmar) {
    $_SESSION["error_registro"] = "Las contraseñas no coinciden.";
    header("Location: registro.php");
    exit();
}

$docenteController = new DocenteController();

try {
    $docenteController->crear($nombres, $apellidos, $correo, $password);
} catch (PDOException $e) {
    // Código 23000 = violación de restricción única (el correo ya existe,
    // porque la columna "correo" tiene UNIQUE en tu tabla docentes)
    if ($e->getCode() === "23000") {
        $_SESSION["error_registro"] = "Ya existe una cuenta registrada con ese correo.";
    } else {
        $_SESSION["error_registro"] = "Ocurrió un error al crear la cuenta. Inténtalo de nuevo.";
    }
    header("Location: registro.php");
    exit();
}

header("Location: ../../../index.php?registrado=1");
exit();