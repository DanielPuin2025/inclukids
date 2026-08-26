<?php

require_once __DIR__ . "/../config/database.php";

/**
 * Provee las estadísticas que se muestran en las tarjetas del
 * dashboard (Estudiantes, Actividades, DBA, Sesiones), leyendo
 * los conteos reales desde la base de datos en vez de tenerlos
 * escritos a mano en la vista.
 */
class DashboardController
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    public function obtenerEstadisticas()
    {
        return [
            "estudiantes" => $this->contar("estudiantes"),
            "actividades" => $this->contar("actividades"),
            "dba"         => $this->contar("dba"),
            "sesiones"    => $this->contar("sesionesclase"),
        ];
    }

    private function contar($tabla)
    {
        // $tabla siempre viene de una lista fija dentro de esta misma
        // clase (nunca de input del usuario), así que es seguro usarla
        // directamente en el SQL.
        $sql = "SELECT COUNT(*) AS total FROM $tabla";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        return (int) $resultado["total"];
    }
}