<?php

require_once __DIR__ . "/../config/database.php";

class TipoActividad
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    public function listar()
    {
        $sql = "SELECT id_tipo, nombre FROM tipoactividad ORDER BY nombre";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}