<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

/**
 * Política mínima de contraseña, en un solo sitio para que el cambio
 * desde "Mi perfil", el registro público y el chequeo de "contraseña
 * de fábrica" del login usen la misma regla.
 *
 * Devuelve un mensaje de error listo para mostrar, o null si la
 * contraseña es aceptable.
 */
function password_politica_error(string $password): ?string
{
    if (mb_strlen($password) < 8) {
        return "La contraseña debe tener al menos 8 caracteres.";
    }

    // Nada de las típicas (incluida la de fábrica "admin123").
    $triviales = [
        "admin123", "12345678", "password", "contrasena", "contraseña",
        "inclukids", "123456789", "qwertyui", "11111111",
    ];
    if (in_array(mb_strtolower($password), $triviales, true)) {
        return "Esa contraseña es demasiado común. Elige otra.";
    }

    // Un solo carácter repetido ("aaaaaaaa").
    if (preg_match('/^(.)\1+$/u', $password)) {
        return "La contraseña no puede ser un solo carácter repetido.";
    }

    return null;
}
