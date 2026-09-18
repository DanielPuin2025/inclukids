<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/ProgresoController.php";

/**
 * Descarga el progreso registrado como CSV (para pegar en el boletín
 * o abrir en Excel). Según el rol y los parámetros:
 *   - docente            -> todo su progreso
 *   - admin ?id_curso=N   -> el progreso de ese curso
 *   - admin ?id_docente=N -> el progreso de ese docente
 *   - admin (sin nada)    -> todo el colegio
 */

$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
$idDocente = (int) $_SESSION["id_docente"];
$idCurso = (int) ($_GET["id_curso"] ?? 0);
$filtroDocente = (int) ($_GET["id_docente"] ?? 0);

$pc = new ProgresoController();

if ($esAdmin && $idCurso > 0) {
    $registros = $pc->listarPorCurso($idCurso);
    $base = "progreso_curso_" . $idCurso;
    $conDocente = false;
} elseif ($esAdmin) {
    $registros = $pc->listarTodo($filtroDocente > 0 ? $filtroDocente : null);
    $base = $filtroDocente > 0 ? "progreso_docente_" . $filtroDocente : "progreso_global";
    $conDocente = true;
} else {
    $registros = $pc->listarPorDocente($idDocente);
    $base = "mi_progreso";
    $conDocente = false;
}

$nombreArchivo = $base . "_" . date("Y-m-d") . ".csv";

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"" . $nombreArchivo . "\"");
header("Cache-Control: no-store");

$salida = fopen("php://output", "w");
fwrite($salida, "\xEF\xBB\xBF");   // BOM: que Excel abra bien los acentos

$cabecera = ["Fecha", "Curso", "DBA", "Descripcion DBA", "Estudiante", "Estado", "Observaciones"];
if ($conDocente) {
    $cabecera[] = "Docente";
}
fputcsv($salida, $cabecera, ";");

foreach ($registros as $r) {
    $curso = (isset($r["grado"]) && $r["grado"] !== null)
        ? (int) $r["grado"] . "\u{00B0}" . ($r["grupo"] ?? "")
        : "";

    $fila = [
        $r["fecha"] ?? "",
        $curso,
        $r["dba_codigo"] ?? "",
        $r["dba_descripcion"] ?? "",
        trim(($r["nombres"] ?? "") . " " . ($r["apellidos"] ?? "")),
        $r["estado"] ?? "",
        $r["observaciones"] ?? "",
    ];
    if ($conDocente) {
        $fila[] = trim(($r["docente_nombres"] ?? "") . " " . ($r["docente_apellidos"] ?? ""));
    }
    fputcsv($salida, $fila, ";");
}

fclose($salida);
exit();
