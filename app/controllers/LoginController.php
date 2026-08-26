<?php

require_once __DIR__ . "/../models/Docente.php";

class LoginController
{
    public function iniciarSesion()
    {
        if (isset($_POST["login"])) {

            $correo = $_POST["correo"];
            $password = $_POST["password"];

            $modelo = new Docente();
            $docente = $modelo->autenticar($correo, $password);

            if (!$docente) {
                $_SESSION["error_login"] = "Correo o contraseña incorrectos.";
                return;
            }

            // El botón Docente/Administrador es solo una guía visual: quien
            // decide a dónde va cada quien es el rol real guardado en la
            // cuenta, nunca lo que el usuario haya marcado en el selector
            // (así unas credenciales correctas nunca se quedan "afuera"
            // por haber elegido el botón equivocado).
            $_SESSION["id_docente"] = $docente["id_docente"];
            $_SESSION["nombre"] = $docente["nombres"];
            $_SESSION["rol"] = $docente["rol"];

            $destino = $docente["rol"] === "administrador"
                ? "app/views/dashboard/home.php"
                : "app/views/dashboard/visualizador.php";

            header("Location: $destino");
            exit();
        }
    }
}