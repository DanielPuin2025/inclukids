<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../config/database.php";

class Materia
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    /**
     * Lista las materias (debería devolver solo Lenguaje y
     * Ciencias Naturales, según el alcance del proyecto).
     */
    public function listar()
    {
        $sql = "SELECT id_materia, nombre FROM Materias ORDER BY nombre";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idMateria)
    {
        $sql = "SELECT id_materia, nombre FROM Materias WHERE id_materia = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idMateria]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}