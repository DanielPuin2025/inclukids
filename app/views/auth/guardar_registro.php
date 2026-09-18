<?php
require_once __DIR__ . "/../../helpers/session_boot.php";
sesion_iniciar();

// El registro público se cerró (ver registro.php). Este handler ya no
// crea cuentas aunque alguien mande el POST directo sin pasar por el
// formulario: defensa en profundidad, no solo ocultar el link.
header("Location: registro.php");
exit();
