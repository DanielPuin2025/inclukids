<?php

require_once __DIR__ . "/app/helpers/session_boot.php";
sesion_iniciar();

require_once __DIR__ . "/app/helpers/csrf.php";
require_once __DIR__ . "/app/controllers/LoginController.php";

$login = new LoginController();
$login->iniciarSesion();

require_once __DIR__ . "/app/views/auth/login.php";