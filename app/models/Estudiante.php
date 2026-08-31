<?php

require_once __DIR__ . "/../config/database.php";

class Estudiante
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    public function listar()
    {
        $sql = "SELECT e.id_estudiante, e.nombres, e.apellidos, e.id_curso,
                       c.grado, c.grupo
                FROM estudiantes e
                LEFT JOIN cursos c ON e.id_curso = c.id_curso
                ORDER BY e.nombres ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorCurso($idCurso)
    {
        $sql = "SELECT id_estudiante, nombres, apellidos
                FROM estudiantes
                WHERE id_curso = ?
                ORDER BY nombres ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idCurso]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Estudiantes de los cursos a cargo de un docente (para el
     * visualizador del docente, que solo debe ver su propio grupo).
     */
    public function listarPorDocente($idDocente)
    {
        $sql = "SELECT e.id_estudiante, e.nombres, e.apellidos, e.id_curso,
                       c.grado, c.grupo
                FROM estudiantes e
                INNER JOIN cursos c ON e.id_curso = c.id_curso
                WHERE c.id_docente = ?
                ORDER BY c.grado, c.grupo, e.nombres ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idEstudiante)
    {
        $sql = "SELECT id_estudiante, nombres, apellidos, id_curso
                FROM estudiantes
                WHERE id_estudiante = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idEstudiante]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombres, $apellidos, $idCurso)
    {
        $sql = "INSERT INTO estudiantes (nombres, apellidos, id_curso)
                VALUES (?, ?, ?)";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$nombres, $apellidos, $idCurso]);
    }

    public function actualizar($idEstudiante, $nombres, $apellidos, $idCurso)
    {
        $sql = "UPDATE estudiantes
                SET nombres = ?, apellidos = ?, id_curso = ?
                WHERE id_estudiante = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$nombres, $apellidos, $idCurso, $idEstudiante]);
    }

    /**
     * Elimina un estudiante junto con su progreso registrado. El
     * progreso de un estudiante no tiene ningún valor por sí solo una
     * vez que el estudiante ya no existe, así que se borra con él en
     * la misma transacción en vez de bloquear el borrado (que es lo
     * que pasaba antes: la restricción de la tabla Progreso impedía
     * eliminar a cualquier estudiante con progreso, sin dar forma de
     * resolverlo).
     */
    public function eliminar($idEstudiante)
    {
        $this->conexion->beginTransaction();

        try {
            $this->conexion->prepare("DELETE FROM progreso WHERE id_estudiante = ?")
                ->execute([$idEstudiante]);

            $consulta = $this->conexion->prepare("DELETE FROM estudiantes WHERE id_estudiante = ?");
            $consulta->execute([$idEstudiante]);

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }
}