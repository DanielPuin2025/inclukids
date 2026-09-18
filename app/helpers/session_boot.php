<?php

/**
 * Marca de "estamos dentro de la app". Se define aquí porque este
 * archivo es lo PRIMERO que incluye cada punto de entrada (bootstrap,
 * index.php, logout.php, juegos, auth). El resto de archivos de
 * app/config, app/models, app/controllers y app/helpers empiezan con
 *
 *     defined("INCLUKIDS") or exit("Acceso no permitido.");
 *
 * para que, si algún día se sirven por URL directa (sin el .htaccess
 * que ya los bloquea), no ejecuten nada.
 */
if (!defined("INCLUKIDS")) {
    define("INCLUKIDS", true);
}

/**
 * Arranque de la sesión PHP con la cookie endurecida.
 *
 * Se llama en TODOS los puntos de entrada antes de tocar $_SESSION:
 *   - app/bootstrap.php  (vistas de gestión de docente / administrador)
 *   - index.php          (login)
 *   - logout.php
 *   - app/views/juegos/* (pantallas de juego, que no pasan por bootstrap)
 *   - app/views/auth/*   (registro público de docente)
 *
 * Antes cada uno hacía `session_start()` a secas y la cookie de sesión
 * salía sin flags: legible desde JavaScript y enviada en peticiones
 * cross-site. Aquí se fija:
 *
 *   - HttpOnly : el JS del navegador nunca puede leer la cookie
 *                (defensa contra robo de sesión vía XSS).
 *   - SameSite=Lax : no se manda en POST desde otro sitio, que es la
 *                base de los ataques CSRF.
 *   - Secure : solo por HTTPS. Se activa SOLO si la petición actual ya
 *              viene por HTTPS, para no romper el XAMPP local por HTTP.
 */

function sesion_iniciar(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $porHttps = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) !== "off")
        || (int) ($_SERVER["SERVER_PORT"] ?? 0) === 443;

    session_set_cookie_params([
        "lifetime" => 0,
        "path"     => "/",
        "httponly" => true,
        "secure"   => $porHttps,
        "samesite" => "Lax",
    ]);

    session_start();
}
