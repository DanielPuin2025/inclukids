<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

/**
 * Límite de intentos de inicio de sesión, por dirección IP.
 *
 * Frena el fuerza-bruta de contraseñas: tras RATE_LOGIN_MAX intentos
 * fallidos seguidos desde la misma IP, esa IP queda bloqueada durante
 * RATE_LOGIN_LOCKOUT segundos (contados desde el último intento). Un
 * inicio de sesión correcto limpia el contador.
 *
 * El estado vive en la tabla `login_bloqueo` (una fila por IP).
 * Si la tabla no existe todavía (base sin migrar), todo falla en
 * abierto: nunca deja a nadie fuera por un problema de infraestructura.
 *
 *   database/migrations/2026_09_07_crear_login_bloqueo.sql
 */

const RATE_LOGIN_MAX = 8;          // intentos fallidos antes de bloquear
const RATE_LOGIN_LOCKOUT = 900;    // 15 minutos de bloqueo

/**
 * Segundos que faltan para que la IP pueda volver a intentar, o null
 * si no está bloqueada.
 */
function login_bloqueado(PDO $db, string $ip): ?int
{
    try {
        $consulta = $db->prepare(
            "SELECT intentos, UNIX_TIMESTAMP(ultimo_intento) AS ultimo
             FROM login_bloqueo WHERE ip = ?"
        );
        $consulta->execute([$ip]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null; // tabla ausente o BD con problemas: no bloquear
    }

    if (!$fila || (int) $fila["intentos"] < RATE_LOGIN_MAX) {
        return null;
    }

    $restante = RATE_LOGIN_LOCKOUT - (time() - (int) $fila["ultimo"]);
    return $restante > 0 ? $restante : null;
}

/**
 * Registra un intento fallido para la IP. Si el bloqueo anterior ya
 * expiró, el contador arranca de cero.
 */
function login_registrar_fallo(PDO $db, string $ip, string $correo): void
{
    try {
        $consulta = $db->prepare(
            "SELECT intentos, UNIX_TIMESTAMP(ultimo_intento) AS ultimo
             FROM login_bloqueo WHERE ip = ?"
        );
        $consulta->execute([$ip]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);

        $correo = mb_substr($correo, 0, 100);

        if (!$fila) {
            $db->prepare(
                "INSERT INTO login_bloqueo (ip, correo, intentos, primer_intento, ultimo_intento)
                 VALUES (?, ?, 1, NOW(), NOW())"
            )->execute([$ip, $correo]);
            return;
        }

        $bloqueoExpirado = (int) $fila["intentos"] >= RATE_LOGIN_MAX
            && (time() - (int) $fila["ultimo"]) >= RATE_LOGIN_LOCKOUT;

        if ($bloqueoExpirado) {
            $db->prepare(
                "UPDATE login_bloqueo
                 SET correo = ?, intentos = 1, primer_intento = NOW(), ultimo_intento = NOW()
                 WHERE ip = ?"
            )->execute([$correo, $ip]);
        } else {
            $db->prepare(
                "UPDATE login_bloqueo
                 SET correo = ?, intentos = intentos + 1, ultimo_intento = NOW()
                 WHERE ip = ?"
            )->execute([$correo, $ip]);
        }
    } catch (PDOException $e) {
        // tabla ausente o BD con problemas: el límite queda inactivo
    }
}

/**
 * Limpia el contador de la IP (llamar tras un inicio de sesión válido).
 */
function login_limpiar(PDO $db, string $ip): void
{
    try {
        $db->prepare("DELETE FROM login_bloqueo WHERE ip = ?")->execute([$ip]);
    } catch (PDOException $e) {
        // sin efecto si la tabla no existe
    }
}

/**
 * La IP del cliente. Se usa REMOTE_ADDR a secas (no X-Forwarded-For,
 * que el cliente puede falsificar). Si algún día se pone un proxy
 * inverso de confianza delante, aquí es donde habría que leer la
 * cabecera correspondiente.
 */
function login_ip_cliente(): string
{
    return $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
}
