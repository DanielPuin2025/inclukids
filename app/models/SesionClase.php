<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

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
                       d.codigo AS dba_codigo, d.descripcion AS dba_descripcion,
                       (SELECT COUNT(*) FROM progreso p WHERE p.id_sesion = s.id_sesion) AS n_progreso
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
     * Cuántas sesiones de clase de este docente todavía NO tienen
     * ningún progreso registrado. Alimenta el aviso del panel del
     * docente ("tienes N sesiones sin progreso").
     */
    public function contarSinProgreso($idDocente)
    {
        $sql = "SELECT COUNT(*)
                FROM SesionesClase s
                WHERE s.id_docente = ?
                  AND NOT EXISTS (
                      SELECT 1 FROM progreso p WHERE p.id_sesion = s.id_sesion
                  )";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente]);

        return (int) $consulta->fetchColumn();
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

    /**
     * Todas las sesiones de clase de TODOS los docentes, con el
     * nombre del docente, el curso y el DBA trabajado. Es la fuente
     * de la vista global de sesiones/progreso del administrador.
     * Si se pasa $idDocente, filtra solo las de ese docente (para el
     * selector de la misma vista).
     */
    public function listarTodo($idDocente = null)
    {
        $sql = "SELECT s.id_sesion, s.fecha, s.observaciones,
                       c.id_curso, c.grado, c.grupo,
                       d.codigo AS dba_codigo, d.descripcion AS dba_descripcion,
                       doc.id_docente, doc.nombres AS docente_nombres, doc.apellidos AS docente_apellidos
                FROM SesionesClase s
                LEFT JOIN Cursos c ON s.id_curso = c.id_curso
                LEFT JOIN DBA d ON s.id_dba = d.id_dba
                LEFT JOIN docentes doc ON s.id_docente = doc.id_docente";

        $parametros = [];
        if ($idDocente !== null) {
            $sql .= " WHERE s.id_docente = ?";
            $parametros[] = $idDocente;
        }

        $sql .= " ORDER BY s.fecha DESC, s.id_sesion DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
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

    /**
     * Duplica una sesión: crea una nueva con el mismo docente, curso,
     * DBA y observaciones, en otra fecha, y copia las actividades
     * marcadas (sesionactividad). NO copia el progreso — la sesión
     * nueva empieza en blanco. Devuelve el id de la sesión creada.
     *
     * Sirve para el docente que trabaja el mismo DBA con el mismo
     * curso varios días: no tiene que repetir el asistente de 5 pasos.
     */
    public function duplicar($idSesion, $nuevaFecha)
    {
        $orig = $this->obtenerPorId($idSesion);
        if (!$orig) {
            return false;
        }

        $this->conexion->beginTransaction();

        try {
            $ins = $this->conexion->prepare(
                "INSERT INTO SesionesClase (id_docente, id_curso, id_dba, fecha, observaciones)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $ins->execute([
                $orig["id_docente"],
                $orig["id_curso"],
                $orig["id_dba"],
                $nuevaFecha,
                $orig["observaciones"],
            ]);
            $nuevoId = $this->conexion->lastInsertId();

            $this->conexion->prepare(
                "INSERT INTO sesionactividad (id_sesion, id_actividad)
                 SELECT ?, id_actividad FROM sesionactividad WHERE id_sesion = ?"
            )->execute([$nuevoId, $idSesion]);

            $this->conexion->commit();
            return $nuevoId;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    /**
     * Elimina una sesión de clase junto con lo que le pertenece
     * exclusivamente a ella: el progreso registrado en esa sesión y
     * las actividades que se marcaron para trabajarse en ella. A
     * diferencia de un DBA (cuyas actividades se pueden reusar en
     * otras sesiones), el progreso y las actividades de UNA sesión
     * no tienen ningún significado fuera de ella, así que no hace
     * falta bloquear el borrado por su causa — siempre se puede
     * eliminar una sesión.
     */
    public function eliminar($idSesion)
    {
        $this->conexion->beginTransaction();

        try {
            $this->conexion->prepare("DELETE FROM progreso WHERE id_sesion = ?")
                ->execute([$idSesion]);
            $this->conexion->prepare("DELETE FROM sesionactividad WHERE id_sesion = ?")
                ->execute([$idSesion]);

            $consulta = $this->conexion->prepare("DELETE FROM SesionesClase WHERE id_sesion = ?");
            $consulta->execute([$idSesion]);

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }
}