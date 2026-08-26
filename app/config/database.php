<?php

class Database
{
    private $host = "localhost";
    private $dbname = "incluKids";
    private $user = "root";
    private $password = "";

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
            die("Error de conexión: " . $e->getMessage());
        }
    }
}