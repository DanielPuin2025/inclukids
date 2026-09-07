<?php

/**
 * Arranque común de toda vista de gestión (docente / administrador).
 *
 * Cada archivo de app/views/<modulo>/ empieza con una sola línea:
 *
 *     <?php require __DIR__ . "/../../bootstrap.php";
 *
 * y, si la página es solo para administradores, justo después:
 *
 *     requireAdministrador("../dashboard/visualizador.php");
 *
 * Hace, en orden:
 *   1. session_start()
 *   2. cierre de sesión por inactividad + rotación periódica del id
 *      (app/helpers/sesion.php)
 *   3. guard de "hay que estar logueado"
 * y deja cargados los helpers de CSRF (csrf_campo / csrf_verificar) y
 * de rol (requireAdministrador).
 *
 * NO lo usan las pantallas de juego (app/views/juegos/*): corren en la
 * sesión del docente pero sin peticiones al servidor mientras el niño
 * juega, así que el timeout no aplica ahí.
 *
 * La ruta de redirección al login ("../../../index.php") es la misma
 * para toda vista, porque todas viven en app/views/<modulo>/.
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . "/helpers/sesion.php";
require_once __DIR__ . "/helpers/csrf.php";
require_once __DIR__ . "/helpers/auth.php";

// Si la sesión caducó por inactividad, esto ya redirige y corta.
sesion_verificar_timeout("../../../index.php");

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}
