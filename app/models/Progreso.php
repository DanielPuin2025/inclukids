<?php

require_once __DIR__ . "/../config/database.php";

class Progreso
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    /**
     * Registra el avance de un estudiante en una sesión de clase.
     * $estado debe ser uno de: 'Logrado', 'En proceso', 'Requiere apoyo'
     * (según el ENUM definido en tu tabla Progreso).
     */
    public function registrar($idSesion, $idEstudiante, $estado, $observaciones)
    {
        $sql = "INSERT INTO Progreso (id_sesion, id_estudiante, estado, observaciones)
                VALUES (?, ?, ?, ?)";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idSesion, $idEstudiante, $estado, $observaciones]);
    }

    /**
     * Todo el progreso registrado para una sesión específica,
     * con el nombre del estudiante.
     */
    public function listarPorSesion($idSesion)
    {
        $sql = "SELECT p.id_progreso, p.estado, p.observaciones,
                       e.id_estudiante, e.nombres, e.apellidos
                FROM Progreso p
                LEFT JOIN Estudiantes e ON p.id_estudiante = e.id_estudiante
                WHERE p.id_sesion = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idSesion]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Todo el progreso que ha registrado un docente, a través de
     * todas sus sesiones y estudiantes. Es la fuente del panel de
     * progreso del docente. Ordenado por sesión (más reciente
     * primero) porque el progreso se lleva por sesión, no como un
     * estado único del estudiante — un mismo estudiante puede tener
     * un registro distinto en cada sesión. El orden global por fecha
     * también sirve para sacar el "más reciente por estudiante": el
     * primer registro que se encuentra de cada estudiante, recorriendo
     * de arriba hacia abajo, es necesariamente el más nuevo.
     */
    public function listarPorDocente($idDocente)
    {
        $sql = "SELECT p.id_progreso, p.estado, p.observaciones,
                       e.id_estudiante, e.nombres, e.apellidos,
                       s.id_sesion, s.fecha,
                       c.grado, c.grupo,
                       d.codigo AS dba_codigo, d.descripcion AS dba_descripcion
                FROM Progreso p
                INNER JOIN SesionesClase s ON p.id_sesion = s.id_sesion
                INNER JOIN Estudiantes e ON p.id_estudiante = e.id_estudiante
                LEFT JOIN Cursos c ON s.id_curso = c.id_curso
                LEFT JOIN DBA d ON s.id_dba = d.id_dba
                WHERE s.id_docente = ?
                ORDER BY s.fecha DESC, s.id_sesion DESC, e.nombres ASC, e.apellidos ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Historial de progreso de un estudiante específico a través
     * de todas sus sesiones, con fecha y actividad.
     */
    public function listarPorEstudiante($idEstudiante)
    {
        $sql = "SELECT p.estado, p.observaciones,
                       s.fecha, a.nombre AS actividad
                FROM Progreso p
                LEFT JOIN SesionesClase s ON p.id_sesion = s.id_sesion
                LEFT JOIN Actividades a ON s.id_actividad = a.id_actividad
                WHERE p.id_estudiante = ?
                ORDER BY s.fecha DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idEstudiante]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}