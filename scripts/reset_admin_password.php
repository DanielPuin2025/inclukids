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
 */
define("INCLUKIDS", true);
require __DIR__ . "/../app/config/database.php";

$db = (new Database())->conectar();
$hash = password_hash("admin123", PASSWORD_DEFAULT);

$st = $db->prepare("UPDATE docentes SET contraseña = ? WHERE correo = ? AND rol = 'administrador'");
$st->execute([$hash, "admin@inclukids.com"]);

echo $st->rowCount() === 1
    ? "OK: admin@inclukids.com -> contraseña 'admin123'. Cámbiala al entrar.\n"
    : "No se actualizó ninguna fila (¿otro correo de admin?).\n";
