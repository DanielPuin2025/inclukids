<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../config/database.php";

/**
 * Tabla intermedia: registra qué actividades específicas se
 * usaron dentro de una sesión de clase. Una sesión (ligada a un
 * DBA) puede usar varias actividades de ese DBA.
 */
class SesionActividad
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    /**
     * Asocia una actividad a una sesión ya creada.
     */
    public function registrar($idSesion, $idActividad)
    {
        $sql = "INSERT INTO sesionactividad (id_sesion, id_actividad)
                VALUES (?, ?)";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idSesion, $idActividad]);
    }

    /**
     * Lista las actividades que se usaron en una sesión específica.
     */
    public function listarPorSesion($idSesion)
    {
        $sql = "SELECT sa.id_sesion_actividad, a.id_actividad, a.nombre, a.descripcion
                FROM sesionactividad sa
                LEFT JOIN actividades a ON sa.id_actividad = a.id_actividad
                WHERE sa.id_sesion = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idSesion]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function quitar($idSesionActividad)
    {
        $sql = "DELETE FROM sesionactividad WHERE id_sesion_actividad = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idSesionActividad]);
    }
}