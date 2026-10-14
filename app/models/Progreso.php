<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../config/database.php";

class progreso
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
        $sql = "INSERT INTO progreso (id_sesion, id_estudiante, estado, observaciones)
                VALUES (?, ?, ?, ?)";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idSesion, $idEstudiante, $estado, $observaciones]);
    }

    /**
     * Registra el avance de un estudiante en una sesión, o lo ACTUALIZA
     * si ya existía uno. Así "Registrar progreso" sirve también para
     * corregir un estado mal marcado o una observación mal escrita sin
     * tener que borrar la sesión entera — y volver a guardar la misma
     * sesión ya no crea filas duplicadas. La tabla no tiene índice
     * único sobre (id_sesion, id_estudiante), por eso el SELECT previo
     * en vez de un ON DUPLICATE KEY.
     */
    public function registrarOActualizar($idSesion, $idEstudiante, $estado, $observaciones)
    {
        $sel = $this->conexion->prepare(
            "SELECT id_progreso FROM progreso
             WHERE id_sesion = ? AND id_estudiante = ? LIMIT 1"
        );
        $sel->execute([$idSesion, $idEstudiante]);
        $idprogreso = $sel->fetchColumn();

        if ($idprogreso) {
            $upd = $this->conexion->prepare(
                "UPDATE progreso SET estado = ?, observaciones = ? WHERE id_progreso = ?"
            );
            return $upd->execute([$estado, $observaciones, $idprogreso]);
        }

        return $this->registrar($idSesion, $idEstudiante, $estado, $observaciones);
    }

    /**
     * Todo el progreso registrado para una sesión específica,
     * con el nombre del estudiante.
     */
    public function listarPorSesion($idSesion)
    {
        $sql = "SELECT p.id_progreso, p.estado, p.observaciones,
                       e.id_estudiante, e.nombres, e.apellidos
                FROM progreso p
                LEFT JOIN estudiantes e ON p.id_estudiante = e.id_estudiante
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
                FROM progreso p
                INNER JOIN sesionesclase s ON p.id_sesion = s.id_sesion
                INNER JOIN estudiantes e ON p.id_estudiante = e.id_estudiante
                LEFT JOIN cursos c ON s.id_curso = c.id_curso
                LEFT JOIN dba d ON s.id_dba = d.id_dba
                WHERE s.id_docente = ?
                ORDER BY s.fecha DESC, s.id_sesion DESC, e.nombres ASC, e.apellidos ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Todo el progreso registrado en las sesiones de un curso
     * específico (sin importar qué docente haya dictado cada sesión).
     * Es la fuente del panel de "elige un curso" del administrador.
     */
    public function listarPorCurso($idCurso)
    {
        $sql = "SELECT p.id_progreso, p.estado, p.observaciones,
                       e.id_estudiante, e.nombres, e.apellidos,
                       s.id_sesion, s.fecha,
                       d.codigo AS dba_codigo, d.descripcion AS dba_descripcion
                FROM progreso p
                INNER JOIN sesionesclase s ON p.id_sesion = s.id_sesion
                INNER JOIN estudiantes e ON p.id_estudiante = e.id_estudiante
                LEFT JOIN dba d ON s.id_dba = d.id_dba
                WHERE s.id_curso = ?
                ORDER BY s.fecha DESC, s.id_sesion DESC, e.nombres ASC, e.apellidos ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idCurso]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Todo el progreso registrado en TODAS las sesiones de TODOS los
     * docentes. Es la fuente de la vista global de sesiones/progreso
     * del administrador. Si se pasa $idDocente, filtra solo lo de ese
     * docente (para el selector de la misma vista).
     */
    public function listarTodo($idDocente = null)
    {
        $sql = "SELECT p.id_progreso, p.estado, p.observaciones,
                       e.id_estudiante, e.nombres, e.apellidos,
                       s.id_sesion, s.fecha,
                       c.grado, c.grupo,
                       d.codigo AS dba_codigo, d.descripcion AS dba_descripcion,
                       doc.id_docente, doc.nombres AS docente_nombres, doc.apellidos AS docente_apellidos
                FROM progreso p
                INNER JOIN sesionesclase s ON p.id_sesion = s.id_sesion
                INNER JOIN estudiantes e ON p.id_estudiante = e.id_estudiante
                LEFT JOIN cursos c ON s.id_curso = c.id_curso
                LEFT JOIN dba d ON s.id_dba = d.id_dba
                LEFT JOIN docentes doc ON s.id_docente = doc.id_docente";

        $parametros = [];
        if ($idDocente !== null) {
            $sql .= " WHERE s.id_docente = ?";
            $parametros[] = $idDocente;
        }

        $sql .= " ORDER BY s.fecha DESC, s.id_sesion DESC, e.nombres ASC, e.apellidos ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Historial de progreso de un estudiante específico a través de
     * todas sus sesiones, con fecha, materia, DBA y las actividades
     * trabajadas en cada sesión (una sesión puede tener varias, de
     * ahí el GROUP_CONCAT en vez de un JOIN directo a Actividades).
     */
    public function listarPorEstudiante($idEstudiante)
    {
        $sql = "SELECT p.estado, p.observaciones,
                       s.id_sesion, s.fecha,
                       m.nombre AS materia,
                       d.codigo AS dba_codigo, d.descripcion AS dba_descripcion,
                       GROUP_CONCAT(DISTINCT a.nombre ORDER BY a.nombre SEPARATOR ', ') AS actividades
                FROM progreso p
                INNER JOIN sesionesclase s ON p.id_sesion = s.id_sesion
                LEFT JOIN dba d ON s.id_dba = d.id_dba
                LEFT JOIN materias m ON d.id_materia = m.id_materia
                LEFT JOIN sesionactividad sa ON sa.id_sesion = s.id_sesion
                LEFT JOIN actividades a ON sa.id_actividad = a.id_actividad
                WHERE p.id_estudiante = ?
                GROUP BY p.id_progreso
                ORDER BY s.fecha DESC, s.id_sesion DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idEstudiante]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}