<?php

require_once __DIR__ . "/../config/database.php";

/**
 * Nota: la columna PK de esta tabla ya se confirmó como "id_sesion"
 * (el typo "id_eesion" no aplicaba a la base real). Además, esta
 * tabla guarda id_dba (no id_actividad) — una sesión queda ligada
 * al DBA trabajado, no a una actividad específica.
 */
class SesionClase
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    /**
     * Lista las sesiones de clase de un docente, con datos del
     * curso y del DBA trabajado.
     */
    public function listarPorDocente($idDocente)
    {
        $sql = "SELECT s.id_sesion, s.fecha, s.observaciones,
                       c.grado, c.grupo,
                       d.codigo AS dba_codigo, d.descripcion AS dba_descripcion
                FROM SesionesClase s
                LEFT JOIN Cursos c ON s.id_curso = c.id_curso
                LEFT JOIN DBA d ON s.id_dba = d.id_dba
                WHERE s.id_docente = ?
                ORDER BY s.fecha DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registra una nueva sesión de clase.
     */
    public function crear($idDocente, $idCurso, $idDba, $fecha, $observaciones)
    {
        $sql = "INSERT INTO SesionesClase (id_docente, id_curso, id_dba, fecha, observaciones)
                VALUES (?, ?, ?, ?, ?)";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente, $idCurso, $idDba, $fecha, $observaciones]);

        // Devuelve el id de la sesión recién creada, útil para
        // luego registrar el progreso de cada estudiante.
        return $this->conexion->lastInsertId();
    }

    public function obtenerPorId($idSesion)
    {
        $sql = "SELECT id_sesion, id_docente, id_curso, id_dba, fecha, observaciones
                FROM SesionesClase
                WHERE id_sesion = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idSesion]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}