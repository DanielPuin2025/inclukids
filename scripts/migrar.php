<?php
/**
 * Aplica las migraciones pendientes de database/migrations/.
 *
 * Uso (con MySQL de XAMPP encendido):
 *     C:\xampp\php\php.exe scripts\migrar.php
 *     C:\xampp\php\php.exe scripts\migrar.php --list   (solo muestra el estado, no aplica nada)
 *
 * Lleva registro en la tabla `migraciones` (una fila por archivo ya
 * aplicado). Es seguro correrlo varias veces: lo que ya está no se
 * vuelve a ejecutar. Los errores de "ya existe" (columna / tabla /
 * clave) se toleran, para que también funcione sobre una base creada
 * con database/schema.sql, que ya incluye lo que estas migraciones
 * añaden — la migración se marca como aplicada igual.
 *
 * Se apoya en la misma conexión que la app (app/config/database.php,
 * incluye app/config/database.local.php si existe).
 */

define("INCLUKIDS", true);
require __DIR__ . "/../app/config/database.php";

$dir = __DIR__ . "/../database/migrations";
$soloListar = in_array("--list", $argv, true);

$db = (new Database())->conectar();

$db->exec(
    "CREATE TABLE IF NOT EXISTS migraciones (
        archivo      VARCHAR(255) NOT NULL,
        aplicada_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (archivo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
);

$aplicadas = array_flip(
    $db->query("SELECT archivo FROM migraciones")->fetchAll(PDO::FETCH_COLUMN)
);

$archivos = glob($dir . "/*.sql");
sort($archivos);

if (!$archivos) {
    echo "No hay archivos .sql en database/migrations/.\n";
    exit(0);
}

// Substrings de errores que significan "esto ya estaba" y no son un
// fallo real (base creada desde schema.sql, o migración recorrida antes
// de existir esta tabla de registro).
$toleradas = [
    "Duplicate column name",
    "already exists",
    "Duplicate key name",
    "Multiple primary key defined",
];

$pendientes = 0;

foreach ($archivos as $ruta) {
    $nombre = basename($ruta);
    $ya = isset($aplicadas[$nombre]);

    if ($soloListar) {
        echo ($ya ? "[x] " : "[ ] ") . $nombre . "\n";
        continue;
    }

    if ($ya) {
        continue;
    }

    $pendientes++;
    echo "-> $nombre ... ";

    $sql = file_get_contents($ruta);
    $sql = preg_replace('/^\s*--.*$/m', "", $sql);          // quita comentarios de línea
    $sentencias = array_filter(array_map("trim", explode(";", $sql)), "strlen");

    $errFatal = null;
    foreach ($sentencias as $sentencia) {
        try {
            $db->exec($sentencia);
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            $tolerado = false;
            foreach ($toleradas as $t) {
                if (stripos($msg, $t) !== false) {
                    $tolerado = true;
                    break;
                }
            }
            if (!$tolerado) {
                $errFatal = $msg;
                break;
            }
            echo "(ya estaba) ";
        }
    }

    if ($errFatal !== null) {
        echo "ERROR\n   $errFatal\n";
        echo "Detenido. No se marcó como aplicada. Corrige y vuelve a correr.\n";
        exit(1);
    }

    $db->prepare("INSERT INTO migraciones (archivo) VALUES (?)")->execute([$nombre]);
    echo "ok\n";
}

if ($soloListar) {
    exit(0);
}

echo $pendientes === 0
    ? "Nada pendiente: la base está al día.\n"
    : "Listo: $pendientes migración(es) procesada(s).\n";
