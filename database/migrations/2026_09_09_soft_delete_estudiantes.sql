-- Borrado suave de estudiantes.
--
-- Antes, "eliminar" un estudiante lo borraba de la tabla junto con
-- TODO su progreso registrado, de forma irreversible (un clic mal dado
-- se llevaba por delante un periodo de evaluacion entero).
--
-- Ahora se marca con una fecha en `eliminado_en`: el estudiante deja
-- de aparecer en los listados y en el registro de progreso, pero su
-- historial se conserva y se puede restaurar. Las consultas de la app
-- filtran por `eliminado_en IS NULL`.

ALTER TABLE `estudiantes`
    ADD COLUMN `eliminado_en` datetime DEFAULT NULL,
    ADD KEY `idx_estudiante_eliminado` (`eliminado_en`);
