<?php

/**
 * Corta el acceso a las páginas de administración (gestión de cursos,
 * DBA, actividades, y el panel global) para cualquier sesión que no
 * sea de rol 'administrador'. Se llama DESPUÉS del guard normal de
 * "hay que estar logueado" que ya tiene cada vista.
 */
function requireAdministrador($redirectSiNoAdmin)
{
    if (($_SESSION["rol"] ?? "docente") !== "administrador") {
        header("Location: $redirectSiNoAdmin");
        exit();
    }
}
