<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

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
                WHERE e.eliminado_en IS NULL
                ORDER BY e.nombres ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorCurso($idCurso)
    {
        $sql = "SELECT id_estudiante, nombres, apellidos
                FROM estudiantes
                WHERE id_curso = ? AND eliminado_en IS NULL
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
                WHERE c.id_docente = ? AND e.eliminado_en IS NULL
                ORDER BY c.grado, c.grupo, e.nombres ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Búsqueda por id SIN filtrar por `eliminado_en`: la usan los
     * chequeos de permiso y la ficha de historial, que deben seguir
     * funcionando para un estudiante ya retirado. Lo que lo oculta de
     * la interfaz son los métodos de listado (todos filtran los
     * retirados).
     */
    public function obtenerPorId($idEstudiante)
    {
        $sql = "SELECT id_estudiante, nombres, apellidos, id_curso, eliminado_en
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

    /**
     * Alta en lote (importación): inserta varios estudiantes en el
     * mismo curso dentro de una sola transacción — o entran todos, o
     * no entra ninguno. $filas es una lista de pares [nombres, apellidos]
     * ya validados y recortados por quien llama.
     */
    public function crearVarios($idCurso, array $filas)
    {
        $this->conexion->beginTransaction();

        try {
            $sql = "INSERT INTO estudiantes (nombres, apellidos, id_curso)
                    VALUES (?, ?, ?)";
            $consulta = $this->conexion->prepare($sql);

            foreach ($filas as $fila) {
                $consulta->execute([$fila[0], $fila[1], $idCurso]);
            }

            $this->conexion->commit();
            return count($filas);
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw $e;
        }
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
     * Borrado suave: marca al estudiante con la fecha actual en
     * `eliminado_en`. Deja de aparecer en los listados y en el
     * registro de progreso, pero su historial se conserva intacto y
     * se puede deshacer con restaurar().
     *
     * Antes esto borraba de verdad al estudiante Y todo su progreso
     * en una misma transacción, sin vuelta atrás: un clic mal dado se
     * llevaba por delante un periodo de evaluación entero.
     */
    public function eliminar($idEstudiante)
    {
        $sql = "UPDATE estudiantes
                SET eliminado_en = NOW()
                WHERE id_estudiante = ? AND eliminado_en IS NULL";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idEstudiante]);

        return $consulta->rowCount() > 0;
    }

    /**
     * Deshace un borrado suave: vuelve a dejar `eliminado_en` en NULL.
     */
    public function restaurar($idEstudiante)
    {
        $sql = "UPDATE estudiantes
                SET eliminado_en = NULL
                WHERE id_estudiante = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idEstudiante]);
    }

    /**
     * Estudiantes retirados (borrado suave) — la "papelera". Solo los
     * que tienen `eliminado_en`, con el curso al que pertenecían.
     * Si se pasa $idDocente, solo los de los cursos de ese docente.
     */
    public function listarEliminados($idDocente = null)
    {
        $sql = "SELECT e.id_estudiante, e.nombres, e.apellidos, e.id_curso,
                       e.eliminado_en, c.grado, c.grupo, c.id_docente
                FROM estudiantes e
                LEFT JOIN cursos c ON e.id_curso = c.id_curso
                WHERE e.eliminado_en IS NOT NULL";

        $parametros = [];
        if ($idDocente !== null) {
            $sql .= " AND c.id_docente = ?";
            $parametros[] = $idDocente;
        }
        $sql .= " ORDER BY e.eliminado_en DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuántos estudiantes hay en la papelera (para la insignia del
     * enlace). Igual que listarEliminados() pero solo el conteo.
     */
    public function contarEliminados($idDocente = null)
    {
        $sql = "SELECT COUNT(*)
                FROM estudiantes e
                LEFT JOIN cursos c ON e.id_curso = c.id_curso
                WHERE e.eliminado_en IS NOT NULL";

        $parametros = [];
        if ($idDocente !== null) {
            $sql .= " AND c.id_docente = ?";
            $parametros[] = $idDocente;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Borrado DEFINITIVO: quita al estudiante y su progreso de la base
     * de datos, sin vuelta atrás. Solo se ofrece desde la papelera, y
     * solo sobre estudiantes ya retirados.
     */
    public function eliminarDefinitivo($idEstudiante)
    {
        $this->conexion->beginTransaction();

        try {
            $this->conexion->prepare("DELETE FROM progreso WHERE id_estudiante = ?")
                ->execute([$idEstudiante]);

            $consulta = $this->conexion->prepare(
                "DELETE FROM estudiantes WHERE id_estudiante = ? AND eliminado_en IS NOT NULL"
            );
            $consulta->execute([$idEstudiante]);

            $this->conexion->commit();
            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }
}