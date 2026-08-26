<?php

require_once __DIR__ . "/../config/database.php";

class Dba
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    public function listar()
    {
        $sql = "SELECT d.id_dba, d.id_materia, d.grado, d.periodo, d.codigo, d.descripcion,
                       m.nombre AS materia
                FROM dba d
                LEFT JOIN materias m ON d.id_materia = m.id_materia
                ORDER BY m.nombre, d.grado, d.periodo, d.codigo";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Este es el filtro real para el flujo del docente:
     * Materia -> Grado -> Periodo -> lista de DBA.
     */
    public function listarPorMateriaGradoPeriodo($idMateria, $grado, $periodo)
    {
        $sql = "SELECT id_dba, id_materia, grado, periodo, codigo, descripcion
                FROM dba
                WHERE id_materia = ? AND grado = ? AND periodo = ?
                ORDER BY codigo";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idMateria, $grado, $periodo]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * DBA correspondientes a los cursos a cargo de un docente: un
     * docente está a cargo de un grado/grupo completo (no de una
     * materia específica), así que ve el DBA de todas las materias
     * para los grados de sus cursos.
     */
    public function listarPorDocente($idDocente)
    {
        $sql = "SELECT DISTINCT d.id_dba, d.id_materia, d.grado, d.periodo, d.codigo, d.descripcion,
                       m.nombre AS materia
                FROM dba d
                INNER JOIN cursos c ON c.grado = d.grado
                LEFT JOIN materias m ON d.id_materia = m.id_materia
                WHERE c.id_docente = ?
                ORDER BY d.grado, m.nombre, d.periodo, d.codigo";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idDba)
    {
        $sql = "SELECT id_dba, id_materia, grado, periodo, codigo, descripcion
                FROM dba
                WHERE id_dba = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDba]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($idMateria, $grado, $periodo, $codigo, $descripcion)
    {
        $sql = "INSERT INTO dba (id_materia, grado, periodo, codigo, descripcion)
                VALUES (?, ?, ?, ?, ?)";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idMateria, $grado, $periodo, $codigo, $descripcion]);
    }

    public function actualizar($idDba, $idMateria, $grado, $periodo, $codigo, $descripcion)
    {
        $sql = "UPDATE dba
                SET id_materia = ?, grado = ?, periodo = ?, codigo = ?, descripcion = ?
                WHERE id_dba = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idMateria, $grado, $periodo, $codigo, $descripcion, $idDba]);
    }

    public function eliminar($idDba)
    {
        $sql = "DELETE FROM dba WHERE id_dba = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idDba]);
    }
}