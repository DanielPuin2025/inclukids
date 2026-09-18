<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../config/database.php";

class Actividad
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    public function listar()
    {
        $sql = "SELECT a.*, d.codigo AS dba_codigo
                FROM actividades a
                LEFT JOIN dba d ON a.id_dba = d.id_dba
                ORDER BY a.nombre ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorDba($idDba)
    {
        $sql = "SELECT * FROM actividades WHERE id_dba = ? ORDER BY nombre ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDba]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idActividad)
    {
        $sql = "SELECT * FROM actividades WHERE id_actividad = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idActividad]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($idDba, $idTipo, $nombre, $descripcion, $instrucciones)
    {
        $sql = "INSERT INTO actividades (id_dba, id_tipo, nombre, descripcion, instrucciones)
                VALUES (?, ?, ?, ?, ?)";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDba, $idTipo, $nombre, $descripcion, $instrucciones]);

        return $this->conexion->lastInsertId();
    }

    public function actualizar($idActividad, $idDba, $idTipo, $nombre, $descripcion, $instrucciones)
    {
        $sql = "UPDATE actividades
                SET id_dba = ?, id_tipo = ?, nombre = ?, descripcion = ?, instrucciones = ?
                WHERE id_actividad = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idDba, $idTipo, $nombre, $descripcion, $instrucciones, $idActividad]);
    }

    public function eliminar($idActividad)
    {
        $sql = "DELETE FROM actividades WHERE id_actividad = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idActividad]);
    }
}