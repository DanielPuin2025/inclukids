<?php

require_once __DIR__ . "/../models/Docente.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/csrf.php";
require_once __DIR__ . "/../helpers/rate_limit.php";

class LoginController
{
    public function iniciarSesion()
    {
        if (isset($_POST["login"])) {

            csrf_verificar();

            $ip = login_ip_cliente();
            $db = (new Database())->conectar();

            // Fuerza-bruta: si esta IP ya acumuló demasiados fallos,
            // no se procesa el intento hasta que pase el bloqueo.
            $espera = login_bloqueado($db, $ip);
            if ($espera !== null) {
                $minutos = (int) ceil($espera / 60);
                $_SESSION["error_login"] = "Demasiados intentos fallidos. Espera unos "
                    . $minutos . " minuto(s) antes de volver a intentar.";
                return;
            }

            $correo = $_POST["correo"];
            $password = $_POST["password"];
            $rolSeleccionado = $_POST["rol"] ?? "docente";

            $modelo = new Docente();
            $docente = $modelo->autenticar($correo, $password);

            if (!$docente) {
                login_registrar_fallo($db, $ip, $correo);
                $_SESSION["error_login"] = "Correo o contraseña incorrectos.";
                return;
            }

            // Credenciales correctas: se limpia el contador aunque el
            // rol elegido no cuadre (no es un intento de fuerza-bruta).
            login_limpiar($db, $ip);

            // El botón sí decide algo real, pero nunca en el sentido
            // peligroso: una cuenta de docente que marca "Administrador"
            // se rechaza aquí mismo (esa cuenta de verdad no tiene esos
            // permisos, no tiene sentido dejarla pasar). Un administrador
            // nunca se bloquea sin importar qué marque — si elige
            // "Docente" simplemente entra a la vista de docente de su
            // propia cuenta, para ver el panel como lo vería un profesor.
            if ($rolSeleccionado === "administrador" && $docente["rol"] !== "administrador") {
                $_SESSION["error_login"] = "Esta cuenta no tiene permisos de administrador. Ingresa marcando \"Docente\".";
                return;
            }

            // Evita session fixation: un id de sesión conocido por un
            // atacante antes del login deja de ser válido después.
            session_regenerate_id(true);

            $_SESSION["id_docente"] = $docente["id_docente"];
            $_SESSION["nombre"] = $docente["nombres"];
            $_SESSION["rol"] = $docente["rol"];

            $destino = ($docente["rol"] === "administrador" && $rolSeleccionado === "administrador")
                ? "app/views/dashboard/home.php"
                : "app/views/dashboard/visualizador.php";

            header("Location: $destino");
            exit();
        }
    }
}