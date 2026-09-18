<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

/**
 * Bitácora de auditoría: deja constancia de las acciones que importan
 * (sobre todo borrados) para poder reconstruir qué pasó.
 *
 *   auditar("retirar", "estudiante", $idEstudiante, "Ana Pérez (2°A)");
 *
 * Toma el actor de la sesión. Si la tabla `auditoria` no existe
 * todavía (base sin migrar), NO rompe nada: no registra y sigue.
 *
 *   database/migrations/2026_09_09_crear_auditoria.sql
 */

require_once __DIR__ . "/../config/database.php";

function auditar(string $accion, string $entidad, ?int $idEntidad = null, string $detalle = ""): void
{
    try {
        $db = (new Database())->conectar();

        $consulta = $db->prepare(
            "INSERT INTO auditoria
                (fecha, id_docente, docente_nombre, rol, accion, entidad, id_entidad, detalle, ip)
             VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $consulta->execute([
            isset($_SESSION["id_docente"]) ? (int) $_SESSION["id_docente"] : null,
            mb_substr($_SESSION["nombre"] ?? "", 0, 200),
            mb_substr($_SESSION["rol"] ?? "", 0, 20),
            mb_substr($accion, 0, 40),
            mb_substr($entidad, 0, 40),
            $idEntidad,
            mb_substr($detalle, 0, 500),
            mb_substr($_SERVER["REMOTE_ADDR"] ?? "", 0, 45),
        ]);
    } catch (Throwable $e) {
        // Tabla ausente o BD con problemas: la auditoría nunca debe
        // impedir que la acción principal se complete.
        error_log("IncluKids - no se pudo auditar ($accion $entidad): " . $e->getMessage());
    }
}

/**
 * Últimas N entradas de la bitácora, más recientes primero.
 */
function auditoria_recientes(int $limite = 200): array
{
    try {
        $db = (new Database())->conectar();
        $limite = max(1, min($limite, 1000));
        $consulta = $db->query(
            "SELECT fecha, docente_nombre, rol, accion, entidad, id_entidad, detalle, ip
             FROM auditoria ORDER BY id DESC LIMIT " . $limite
        );
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}
