<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

/**
 * Cierre de sesión por inactividad + rotación periódica del id.
 *
 * Se llama al principio de cada vista autenticada, justo después de
 * arrancar la sesión (sesion_iniciar(), app/helpers/session_boot.php)
 * y ANTES del guard de "hay que estar logueado".
 *
 *   - Si pasaron más de SESION_TIMEOUT segundos desde la última
 *     petición, la sesión se destruye y se redirige al login con
 *     ?expirado=1 (para mostrar el aviso).
 *   - Si sigue viva, se refresca la marca de actividad y, cada
 *     SESION_REGENERAR_CADA segundos, se rota el id de sesión
 *     (defensa extra frente a robo de sesión en sesiones largas).
 *
 * Las pantallas de juego (app/views/juegos/*) NO usan esto: corren
 * en la sesión del docente pero sin peticiones al servidor mientras
 * el niño juega, así que el reloj no se refrescaría y una actividad
 * lenta cerraría la sesión del docente sin motivo.
 */

const SESION_TIMEOUT = 1800;         // 30 min sin actividad
const SESION_REGENERAR_CADA = 900;   // rota el id cada 15 min

function sesion_verificar_timeout(string $redir = "../../../index.php"): void
{
    // Sin sesión iniciada no hay nada que expirar; del "no estás
    // logueado" se encarga el guard normal de cada vista.
    if (!isset($_SESSION["id_docente"])) {
        return;
    }

    $ahora = time();
    $ultima = $_SESSION["ultima_actividad"] ?? $ahora;

    if ($ahora - $ultima > SESION_TIMEOUT) {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                "",
                $ahora - 42000,
                $p["path"],
                $p["domain"],
                $p["secure"],
                $p["httponly"]
            );
        }

        session_destroy();
        header("Location: " . $redir . "?expirado=1");
        exit();
    }

    $_SESSION["ultima_actividad"] = $ahora;

    $rotadaEn = $_SESSION["sesion_regenerada"] ?? 0;
    if ($rotadaEn === 0) {
        $_SESSION["sesion_regenerada"] = $ahora;
    } elseif ($ahora - $rotadaEn > SESION_REGENERAR_CADA) {
        session_regenerate_id(true);
        $_SESSION["sesion_regenerada"] = $ahora;
    }
}
