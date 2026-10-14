<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

/**
 * Conexión PDO a MySQL.
 *
 * Los valores por defecto son los de un XAMPP recién instalado. Para
 * usar otras credenciales (otro servidor, contraseña de root, etc.)
 * NO se edita este archivo: se crea, en esta misma carpeta,
 *
 *     app/config/database.local.php
 *
 * que devuelve un array con las claves que quieras sobrescribir:
 *
 *     <?php return ["password" => "secreta", "dbname" => "inclukids"];
 *
 * Ese archivo está en .gitignore (no se sube al repo). Hay una
 * plantilla en app/config/database.local.php.example.
 */

class Database
{
    private $host = "localhost";
    private $dbname = "incluKids";
    private $user = "root";
    private $password = "";

    

    public function __construct()
    {
        $local = __DIR__ . "/database.local.php";
        if (is_file($local)) {
            $cfg = require $local;
            if (is_array($cfg)) {
                foreach (["host", "dbname", "user", "password"] as $clave) {
                    if (array_key_exists($clave, $cfg)) {
                        $this->$clave = $cfg[$clave];
                    }
                }
            }
        }
    }

    public function conectar()
    {
        try {
            $conexion = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->user,
                $this->password
            );

            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conexion;

        } catch (PDOException $e) {
            // El detalle (host, usuario, motivo) va al log del servidor,
            // nunca al navegador: expondría datos de la infraestructura.
            error_log("IncluKids - fallo de conexion a la BD: " . $e->getMessage());
            http_response_code(500);
     
    die("Error real de conexión: " . $e->getMessage());

    

        }
    }
}
