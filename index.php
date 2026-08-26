<?php

session_start();

require_once __DIR__ . "/app/controllers/LoginController.php";

$login = new LoginController();
$login->iniciarSesion();

require_once __DIR__ . "/app/views/auth/login.php";