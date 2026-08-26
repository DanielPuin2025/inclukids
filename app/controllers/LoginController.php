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

            if ($docente) {

                $_SESSION["id_docente"] = $docente["id_docente"];
                $_SESSION["nombre"] = $docente["nombres"];

                header("Location: app/views/dashboard/home.php");
                exit();

            } else {

                echo "<script>alert('Correo o contraseña incorrectos');</script>";

            }
        }
    }
}