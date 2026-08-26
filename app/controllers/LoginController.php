<?php

require_once __DIR__ . "/../models/Docente.php";

class LoginController
{
    public function iniciarSesion()
    {
        if (isset($_POST["login"])) {

            $correo = $_POST["correo"];
            $password = $_POST["password"];
            $rolSeleccionado = $_POST["rol"] ?? "docente";

            $modelo = new Docente();
            $docente = $modelo->autenticar($correo, $password);

            if (!$docente) {
                $_SESSION["error_login"] = "Correo o contraseña incorrectos.";
                return;
            }

            if ($docente["rol"] !== $rolSeleccionado) {
                $_SESSION["error_login"] = $docente["rol"] === "administrador"
                    ? "Esta cuenta es de administrador. Selecciona esa opción para ingresar."
                    : "Esta cuenta es de docente. Selecciona esa opción para ingresar.";
                return;
            }

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