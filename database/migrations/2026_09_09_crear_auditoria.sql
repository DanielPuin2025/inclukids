-- Bitácora de auditoría.
--
-- Una fila por acción "que importa" (sobre todo borrados): quién la
-- hizo, sobre qué, cuándo y desde qué IP. Sirve para reconstruir qué
-- pasó cuando algo desaparece y hay varios docentes + un admin
-- trabajando sobre la misma base.
--
-- app/helpers/auditoria.php la escribe con auditar(). Si esta tabla no
-- existe todavía, auditar() no rompe nada: simplemente no registra.

CREATE TABLE IF NOT EXISTS auditoria (
    id             INT          NOT NULL AUTO_INCREMENT,
    fecha          DATETIME     NOT NULL,
    id_docente     INT          DEFAULT NULL,
    docente_nombre VARCHAR(200) DEFAULT NULL,
    rol            VARCHAR(20)  DEFAULT NULL,
    accion         VARCHAR(40)  NOT NULL,   -- p. ej. "retirar", "restaurar", "borrar_definitivo", "eliminar"
    entidad        VARCHAR(40)  NOT NULL,   -- p. ej. "estudiante", "curso", "dba", "actividad", "sesion"
    id_entidad     INT          DEFAULT NULL,
    detalle        VARCHAR(500) DEFAULT NULL,
    ip             VARCHAR(45)  DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_auditoria_fecha (fecha),
    KEY idx_auditoria_entidad (entidad, id_entidad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
