<?php

require_once __DIR__ . "/../config/database.php";

class Curso
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    public function listar()
    {
        $sql = "SELECT c.id_curso, c.grado, c.grupo, c.anio, c.id_docente,
                       d.nombres AS docente_nombres, d.apellidos AS docente_apellidos,
                       (SELECT COUNT(*) FROM estudiantes e WHERE e.id_curso = c.id_curso) AS n_estudiantes
                FROM cursos c
                LEFT JOIN docentes d ON c.id_docente = d.id_docente
                ORDER BY c.grado, c.grupo";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorDocente($idDocente)
    {
        $sql = "SELECT id_curso, grado, grupo, anio
                FROM cursos
                WHERE id_docente = ?
                ORDER BY grado, grupo";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idCurso)
    {
        $sql = "SELECT id_curso, grado, grupo, anio, id_docente
                FROM cursos
                WHERE id_curso = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idCurso]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($grado, $grupo, $anio, $idDocente)
    {
        $sql = "INSERT INTO cursos (grado, grupo, anio, id_docente)
                VALUES (?, ?, ?, ?)";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$grado, $grupo, $anio, $idDocente]);
    }

    public function actualizar($idCurso, $grado, $grupo, $anio, $idDocente)
    {
        $sql = "UPDATE cursos
                SET grado = ?, grupo = ?, anio = ?, id_docente = ?
                WHERE id_curso = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$grado, $grupo, $anio, $idDocente, $idCurso]);
    }

    public function eliminar($idCurso)
    {
        $sql = "DELETE FROM cursos WHERE id_curso = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idCurso]);
    }
}