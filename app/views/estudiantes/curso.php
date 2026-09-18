<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/ProgresoController.php";
require_once __DIR__ . "/../../controllers/DocenteController.php";

$idCurso = (int) ($_GET["id_curso"] ?? 0);

$cursoController = new CursoController();
$curso = $cursoController->obtenerPorId($idCurso);

if (!$curso) {
    header("Location: index.php");
    exit();
}

$docente = null;
if ($curso["id_docente"]) {
    $docenteController = new DocenteController();
    $docente = $docenteController->obtenerPorId($curso["id_docente"]);
}

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listarPorCurso($idCurso);

$progresoController = new ProgresoController();
$registrosProgreso = $progresoController->listarPorCurso($idCurso);

// El progreso se lleva por sesión: se agrupa así en vez de como un
// estado único por estudiante (un estudiante puede tener un
// resultado distinto en cada sesión).
$progresoPorSesion = [];
foreach ($registrosProgreso as $r) {
    $idSesionProgreso = (int) $r["id_sesion"];
    if (!isset($progresoPorSesion[$idSesionProgreso])) {
        $progresoPorSesion[$idSesionProgreso] = [
            "fecha" => $r["fecha"],
            "dba_codigo" => $r["dba_codigo"],
            "dba_descripcion" => $r["dba_descripcion"],
            "estudiantes" => [],
        ];
    }
    $progresoPorSesion[$idSesionProgreso]["estudiantes"][] = $r;
}

$colores = ["Logrado" => "success", "En proceso" => "warning", "Requiere apoyo" => "danger"];

// Resumen por DBA: para cada DBA, el estado MÁS RECIENTE de cada
// estudiante (la consulta ya viene ordenada por fecha desc).
$resumenDba = [];
$vistoEstDba = [];
foreach ($registrosProgreso as $r) {
    $dbaCod = $r["dba_codigo"] ?: "(sin DBA)";
    $clave = $dbaCod . "|" . $r["id_estudiante"];
    if (isset($vistoEstDba[$clave])) continue;
    $vistoEstDba[$clave] = true;
    if (!isset($resumenDba[$dbaCod])) {
        $resumenDba[$dbaCod] = [
            "desc" => $r["dba_descripcion"] ?? "",
            "Logrado" => 0, "En proceso" => 0, "Requiere apoyo" => 0,
        ];
    }
    if (isset($resumenDba[$dbaCod][$r["estado"]])) {
        $resumenDba[$dbaCod][$r["estado"]]++;
    }
}
?>
<?php render_head(
    ((int) $curso['grado']) . "°" . $curso['grupo'] . " - Estudiantes - IncluKids",
    ["css" => ["panel.css"]]
); ?>
<body>
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-1">
        <h2 class="mb-0">🏫 <?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?></h2>
        <span class="d-flex gap-2 flex-wrap">
            <a href="importar.php?id_curso=<?= $idCurso ?>" class="btn btn-outline-secondary btn-sm">📋 Importar</a>
            <a href="nuevo.php?id_curso=<?= $idCurso ?>" class="btn btn-success btn-sm">+ Nuevo estudiante</a>
        </span>
    </div>
    <p class="text-muted mb-4">
        Año <?= (int) $curso['anio'] ?>
        <?php if ($docente): ?>
            · Docente a cargo: <?= htmlspecialchars($docente['nombres'] . ' ' . $docente['apellidos']) ?>
        <?php else: ?>
            · Sin docente asignado
        <?php endif; ?>
    </p>

    <?= flash_render() ?>

    <h5 class="mb-3">Estudiantes</h5>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estudiantes)): ?>
                        <tr>
                            <td colspan="3" class="text-center">Este curso no tiene estudiantes registrados.</td>
                        </tr>
                        <?php else: foreach ($estudiantes as $estudiante): ?>
                        <tr>
                            <td><?= htmlspecialchars($estudiante["nombres"]) ?></td>
                            <td><?= htmlspecialchars($estudiante["apellidos"]) ?></td>
                            <td>
                                <a href="editar.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="../progreso/reporte_estudiante.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-secondary">📄 Reporte</a>
                                <form method="POST" action="eliminar.php" class="d-inline"
                                      data-confirmar="¿Eliminar este estudiante? También se eliminará el progreso que tenga registrado.">
                                    <?= csrf_campo() ?>
                                    <input type="hidden" name="id_estudiante" value="<?= (int) $estudiante['id_estudiante'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0">📊 Progreso por sesión</h5>
        <span class="d-flex gap-2 flex-wrap">
            <a href="../progreso/cobertura.php?id_curso=<?= (int) $idCurso ?>"
               class="btn btn-sm btn-outline-secondary">🗺️ Cobertura de DBA</a>
            <a href="../progreso/reporte_curso.php?id_curso=<?= (int) $idCurso ?>"
               class="btn btn-sm btn-outline-secondary">🖨️ Reporte</a>
            <a href="../progreso/exportar.php?id_curso=<?= (int) $idCurso ?>"
               class="btn btn-sm btn-outline-secondary">⬇️ Exportar CSV</a>
        </span>
    </div>

    <?php if (!empty($resumenDba)): ?>
        <h4 class="mt-4">Resumen por DBA</h4>
        <p class="text-muted small mb-2">Estado más reciente de cada estudiante en cada DBA.</p>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>DBA</th>
                        <th class="text-center">✅ Logrado</th>
                        <th class="text-center">🟡 En proceso</th>
                        <th class="text-center">🆘 Requiere apoyo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resumenDba as $cod => $fila): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($cod) ?></strong>
                            <?php if ($fila["desc"]): ?><br><span class="text-muted small"><?= htmlspecialchars($fila["desc"]) ?></span><?php endif; ?>
                        </td>
                        <td class="text-center"><?= $fila["Logrado"] ?></td>
                        <td class="text-center"><?= $fila["En proceso"] ?></td>
                        <td class="text-center"><?= $fila["Requiere apoyo"] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (empty($progresoPorSesion)): ?>
        <p class="text-muted">Todavía no se ha registrado progreso en ninguna sesión de este curso.</p>
    <?php else: ?>
        <?php foreach ($progresoPorSesion as $idSesionCard => $sesion): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="mb-1">📅 <?= htmlspecialchars($sesion["fecha"]) ?></h6>
                    <form method="POST" action="../sesiones/eliminar.php" class="d-inline"
                          data-confirmar="¿Eliminar esta sesión? También se eliminará el progreso y las actividades registradas en ella.">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id_sesion" value="<?= (int) $idSesionCard ?>">
                        <input type="hidden" name="volver" value="curso:<?= (int) $idCurso ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar sesión</button>
                    </form>
                </div>
                <?php if ($sesion["dba_codigo"]): ?>
                <p class="text-muted small mb-3">
                    <strong><?= htmlspecialchars($sesion["dba_codigo"]) ?></strong>
                    — <?= htmlspecialchars($sesion["dba_descripcion"] ?? "") ?>
                </p>
                <?php endif; ?>
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sesion["estudiantes"] as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r["nombres"] . " " . $r["apellidos"]) ?></td>
                            <td>
                                <span class="badge bg-<?= $colores[$r["estado"]] ?? "secondary" ?>">
                                    <?= htmlspecialchars($r["estado"]) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($r["observaciones"] ?? "") ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
</body>
</html>
