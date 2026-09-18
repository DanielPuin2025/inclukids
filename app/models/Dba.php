<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

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

    /**
     * Cobertura de DBA de un curso: todos los DBA del grado del curso,
     * con cuántas sesiones de clase de ESE curso los han trabajado ya
     * (n_sesiones = 0  ->  DBA pendiente). Alimenta la vista de
     * "Cobertura de DBA por curso".
     */
    public function coberturaPorCurso($idCurso)
    {
        $sql = "SELECT d.id_dba, d.periodo, d.codigo, d.descripcion,
                       m.nombre AS materia,
                       (SELECT COUNT(*) FROM sesionesclase s
                        WHERE s.id_curso = ? AND s.id_dba = d.id_dba) AS n_sesiones
                FROM dba d
                INNER JOIN cursos c ON c.id_curso = ?
                LEFT JOIN materias m ON d.id_materia = m.id_materia
                WHERE d.grado = c.grado
                ORDER BY m.nombre, d.periodo, d.codigo, d.id_dba";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idCurso, $idCurso]);

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

    /**
     * True si este DBA ya se usó en algo real: una sesión de clase
     * dictada con él, o una actividad suya que ya se asignó a una
     * sesión. En ese caso NO se debe permitir borrar en cascada (se
     * perdería historial real de clase), así que el llamador debe
     * bloquear el borrado.
     */
    public function tieneUsoReal($idDba)
    {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM sesionesclase WHERE id_dba = ?)
                    +
                    (SELECT COUNT(*) FROM sesionactividad sa
                        INNER JOIN actividades a ON sa.id_actividad = a.id_actividad
                        WHERE a.id_dba = ?)
                    AS total";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDba, $idDba]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Actividades (juegos) asociadas a este DBA, solo para mostrarle
     * al administrador qué se va a borrar junto con el DBA.
     */
    public function actividadesAsociadas($idDba)
    {
        $sql = "SELECT id_actividad, nombre FROM actividades WHERE id_dba = ? ORDER BY nombre";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDba]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina el DBA junto con sus actividades y el contenido jugable
     * de esas actividades (preguntas y rondas). Solo debe llamarse
     * después de confirmar con tieneUsoReal() que nada de esto está
     * ligado a una sesión de clase real.
     */
    public function eliminarConActividades($idDba)
    {
        $this->conexion->beginTransaction();

        try {
            $actividades = $this->actividadesAsociadas($idDba);

            foreach ($actividades as $actividad) {
                $idActividad = $actividad["id_actividad"];

                $this->conexion->prepare("DELETE FROM contenidoactividad WHERE id_actividad = ?")
                    ->execute([$idActividad]);
                $this->conexion->prepare("DELETE FROM contenido_juego WHERE id_actividad = ?")
                    ->execute([$idActividad]);
                $this->conexion->prepare("DELETE FROM actividades WHERE id_actividad = ?")
                    ->execute([$idActividad]);
            }

            $this->conexion->prepare("DELETE FROM dba WHERE id_dba = ?")
                ->execute([$idDba]);

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }
}