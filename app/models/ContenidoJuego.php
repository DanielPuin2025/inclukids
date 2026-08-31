<?php

require_once __DIR__ . "/../config/database.php";

/**
 * Contenido jugable para mecánicas distintas a la de opción múltiple
 * (esa la sigue cubriendo ContenidoActividad). Guarda un bloque de
 * datos en JSON por actividad; cada motor de juego interpreta su
 * propia forma de JSON (rondas, pares, piezas...).
 */
class ContenidoJuego
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    public function obtenerPorActividad($idActividad)
    {
        $sql = "SELECT id_contenido_juego, id_actividad, datos_json
                FROM contenido_juego
                WHERE id_actividad = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idActividad]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function guardar($idActividad, $datosJson)
    {
        $sql = "INSERT INTO contenido_juego (id_actividad, datos_json)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE datos_json = VALUES(datos_json)";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idActividad, $datosJson]);
    }
}
