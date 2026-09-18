<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../config/database.php";

class ContenidoActividad
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    /**
     * Todas las preguntas de una actividad, en el orden en que
     * deben presentarse al estudiante.
     */
    public function listarPorActividad($idActividad)
    {
        $sql = "SELECT id_contenido, pregunta, opcion_a, opcion_b, opcion_c, opcion_d,
                       respuesta_correcta, imagen, audio, orden
                FROM contenidoactividad
                WHERE id_actividad = ?
                ORDER BY orden ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idActividad]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($idActividad, $pregunta, $opcionA, $opcionB, $opcionC, $opcionD, $respuestaCorrecta, $orden)
    {
        $sql = "INSERT INTO contenidoactividad
                    (id_actividad, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, orden)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            $idActividad, $pregunta, $opcionA, $opcionB,
            $opcionC ?: null, $opcionD ?: null, $respuestaCorrecta, $orden
        ]);
    }

    public function eliminar($idContenido)
    {
        $sql = "DELETE FROM contenidoactividad WHERE id_contenido = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idContenido]);
    }
}