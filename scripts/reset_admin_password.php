<?php
/**
 * Restablece la contraseña del administrador a "admin123".
 *
 * Uso (con MySQL de XAMPP encendido):
 *     C:\xampp\php\php.exe scripts\reset_admin_password.php
 *
 * Pensado para recuperar el acceso si se pierde la contraseña. Borra
 * este archivo cuando termines, o al menos cambia la contraseña desde
 * "Mi perfil" en cuanto entres.
 *
 * Defensa en profundidad: el .htaccess ya bloquea /scripts/ por URL,
 * pero por si se despliega sin él, este chequeo corta la ejecución si
 * alguien lo pide por HTTP en vez de por línea de comandos.
 */
if (PHP_SAPI !== "cli") {
    http_response_code(403);
    die("Este script solo se ejecuta por línea de comandos.");
}

define("INCLUKIDS", true);
require __DIR__ . "/../app/config/database.php";

$db = (new Database())->conectar();
$hash = password_hash("admin123", PASSWORD_DEFAULT);

$st = $db->prepare("UPDATE docentes SET contraseña = ? WHERE correo = ? AND rol = 'administrador'");
$st->execute([$hash, "admin@inclukids.com"]);

echo $st->rowCount() === 1
    ? "OK: admin@inclukids.com -> contraseña 'admin123'. Cámbiala al entrar.\n"
    : "No se actualizó ninguna fila (¿otro correo de admin?).\n";
