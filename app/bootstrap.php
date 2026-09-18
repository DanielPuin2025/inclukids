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
 *   1. arranque de la sesión con la cookie endurecida
 *      (HttpOnly + SameSite=Lax + Secure por HTTPS; app/helpers/session_boot.php)
 *   2. cierre de sesión por inactividad + rotación periódica del id
 *      (app/helpers/sesion.php)
 *   3. guard de "hay que estar logueado"
 * y deja cargados los helpers de CSRF (csrf_campo / csrf_verificar),
 * de rol (requireAdministrador), de vista (render_head) y de avisos
 * (flash_set / flash_render).
 *
 * NO lo usan las pantallas de juego (app/views/juegos/*): corren en la
 * sesión del docente pero sin peticiones al servidor mientras el niño
 * juega, así que el timeout no aplica ahí.
 *
 * La ruta de redirección al login ("../../../index.php") es la misma
 * para toda vista, porque todas viven en app/views/<modulo>/.
 */

require_once __DIR__ . "/helpers/session_boot.php";
sesion_iniciar();

require_once __DIR__ . "/helpers/sesion.php";
require_once __DIR__ . "/helpers/csrf.php";
require_once __DIR__ . "/helpers/auth.php";
require_once __DIR__ . "/helpers/vista.php";
require_once __DIR__ . "/helpers/flash.php";
require_once __DIR__ . "/helpers/auditoria.php";
require_once __DIR__ . "/helpers/password.php";

// Si la sesión caducó por inactividad, esto ya redirige y corta.
sesion_verificar_timeout("../../../index.php");

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

// Si el administrador entró con una contraseña insegura (p. ej. la de
// fábrica "admin123"), no se le deja usar el resto del panel hasta que
// la cambie desde "Mi perfil". La marca la pone LoginController al
// iniciar sesión y la quita guardar_password.php al cambiarla.
if (!empty($_SESSION["forzar_cambio_password"])) {
    $script = basename($_SERVER["SCRIPT_NAME"] ?? "");
    $permitidos = ["perfil.php", "guardar_password.php", "guardar_perfil.php"];
    if (!in_array($script, $permitidos, true)) {
        header("Location: ../docentes/perfil.php");
        exit();
    }
}
