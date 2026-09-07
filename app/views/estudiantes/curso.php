<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../helpers/auth.php";
requireAdministrador("../dashboard/visualizador.php");

require_once __DIR__ . "/../../helpers/csrf.php";
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?> - Estudiantes - IncluKids</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
<link href="/IncluKids/public/css/panel.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

    <a href="index.php" class="text-decoration-none">&larr; Elegir otro curso</a>

    <div class="d-flex justify-content-between align-items-center mt-3 mb-1">
        <h2 class="mb-0">🏫 <?= (int) $curso['grado'] ?>°<?= htmlspecialchars($curso['grupo']) ?></h2>
        <a href="nuevo.php?id_curso=<?= $idCurso ?>" class="btn btn-success btn-sm">+ Nuevo estudiante</a>
    </div>
    <p class="text-muted mb-4">
        Año <?= (int) $curso['anio'] ?>
        <?php if ($docente): ?>
            · Docente a cargo: <?= htmlspecialchars($docente['nombres'] . ' ' . $docente['apellidos']) ?>
        <?php else: ?>
            · Sin docente asignado
        <?php endif; ?>
    </p>

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
                                      onsubmit="return confirm('¿Eliminar este estudiante? También se eliminará el progreso que tenga registrado.');">
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

    <h5 class="mb-3">📊 Progreso por sesión</h5>

    <?php if (empty($progresoPorSesion)): ?>
        <p class="text-muted">Todavía no se ha registrado progreso en ninguna sesión de este curso.</p>
    <?php else: ?>
        <?php foreach ($progresoPorSesion as $idSesionCard => $sesion): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="mb-1">📅 <?= htmlspecialchars($sesion["fecha"]) ?></h6>
                    <form method="POST" action="../sesiones/eliminar.php" class="d-inline"
                          onsubmit="return confirm('¿Eliminar esta sesión? También se eliminará el progreso y las actividades registradas en ella.');">
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
