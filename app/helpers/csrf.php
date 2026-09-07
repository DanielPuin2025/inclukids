<?php

/**
 * Protección CSRF para todos los formularios que cambian datos.
 *
 * Requiere que session_start() ya se haya llamado (todas las vistas
 * lo hacen al inicio). El token es POR SESIÓN, no por request: alcanza
 * contra CSRF y no rompe el botón "atrás" ni tener varias pestañas
 * abiertas.
 *
 *   - csrf_token()     -> el token de la sesión (lo crea si no existe)
 *   - csrf_campo()     -> el <input type="hidden"> listo para pegar
 *                         dentro de un <form>
 *   - csrf_verificar() -> corta la petición con 419 si el token que
 *                         llegó por POST no coincide
 */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

function csrf_verificar(): void
{
    $enviado = $_POST['csrf_token'] ?? '';
    $real = $_SESSION['csrf_token'] ?? '';

    if ($real === '' || !is_string($enviado) || !hash_equals($real, $enviado)) {
        http_response_code(403);
        die('Sesión expirada o solicitud no válida. Vuelve atrás, recarga la '
          . 'página e inténtalo de nuevo.');
    }
}
