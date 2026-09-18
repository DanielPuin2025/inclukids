<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

/**
 * Mensajes "flash": un aviso que se guarda en la sesión, sobrevive a
 * un redirect y se muestra UNA sola vez en la página siguiente.
 *
 * Sustituye a los `die("Faltan datos…")` que dejaban páginas muertas
 * sin estilo: ahora el handler guarda el aviso y redirige de vuelta al
 * formulario, que lo pinta con el estilo de la app.
 *
 *   flash_set("error", "Faltan datos.");
 *   header("Location: nuevo.php"); exit();
 *
 * y en la vista de destino, una sola vez donde deba salir el aviso:
 *
 *   <?= flash_render() ?>
 *
 * Requiere que la sesión ya esté arrancada (bootstrap lo hace).
 */

/** Guarda un aviso. $tipo: "exito" | "error" | "info". */
function flash_set(string $tipo, string $mensaje): void
{
    $_SESSION["_flash"] = ["tipo" => $tipo, "mensaje" => $mensaje];
}

/** Devuelve el aviso pendiente (y lo borra), o null si no hay. */
function flash_get(): ?array
{
    if (empty($_SESSION["_flash"])) {
        return null;
    }
    $flash = $_SESSION["_flash"];
    unset($_SESSION["_flash"]);
    return $flash;
}

/**
 * Devuelve el HTML de la alerta de Bootstrap con el aviso pendiente
 * (cadena vacía si no hay). Escapa el mensaje.
 */
function flash_render(): string
{
    $flash = flash_get();
    if (!$flash) {
        return "";
    }

    $clases = [
        "exito" => "alert-success",
        "error" => "alert-danger",
        "info"  => "alert-info",
    ];
    $clase = $clases[$flash["tipo"]] ?? "alert-secondary";

    return '<div class="alert ' . $clase . '" role="alert">'
        . htmlspecialchars($flash["mensaje"])
        . '</div>';
}
