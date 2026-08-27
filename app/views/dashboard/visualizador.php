<?php
session_start();
if (!isset($_SESSION["id_docente"])) { header("Location: ../../../index.php"); exit(); }

require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/DbaController.php";
require_once __DIR__ . "/../../controllers/SesionClaseController.php";

$idDocente = $_SESSION["id_docente"];

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listarPorDocente($idDocente);

$dbaController = new DbaController();
$dbaLista = $dbaController->listarPorDocente($idDocente);

$sesionController = new SesionClaseController();
$sesiones = $sesionController->listarPorDocente($idDocente);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi panel - IncluKids</title>
<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-1">👁️ Mi panel</h2>
        <a href="../../../logout.php" class="btn btn-sm btn-outline-secondary">🚪 Cerrar sesión</a>
    </div>
    <p class="text-muted">Estudiantes, DBA y sesiones de los cursos a tu cargo.</p>

    <a href="../sesiones/paso1_curso.php" class="btn btn-primary btn-lg mb-4">
        ▶️ Iniciar sesión de clase
    </a>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <h4 class="mb-0">👨‍🎓 Mis estudiantes</h4>
        <a href="../estudiantes/nuevo.php" class="btn btn-sm btn-success">+ Nuevo estudiante</a>
    </div>
    <div class="card mb-4 mt-2">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Curso</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($estudiantes)): ?>
                    <tr>
                        <td colspan="4" class="text-center">
                            No tienes estudiantes a cargo todavía.
                        </td>
                    </tr>
                    <?php else: foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td><?= htmlspecialchars($estudiante["nombres"]) ?></td>
                        <td><?= htmlspecialchars($estudiante["apellidos"]) ?></td>
                        <td><?= (int) $estudiante["grado"] ?>°<?= htmlspecialchars($estudiante["grupo"]) ?></td>
                        <td>
                            <a href="../estudiantes/editar.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <h4 class="mb-0">📅 Mis sesiones de clase</h4>
        <a href="../progreso/panel.php" class="btn btn-sm btn-outline-primary">📊 Ver progreso de mis estudiantes</a>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Curso</th>
                        <th>DBA trabajado</th>
                        <th>Progreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sesiones)): ?>
                    <tr>
                        <td colspan="4" class="text-center">
                            No hay sesiones registradas todavía. Usa
                            "▶️ Iniciar sesión de clase" arriba.
                        </td>
                    </tr>
                    <?php else: foreach ($sesiones as $sesion): ?>
                    <tr>
                        <td><?= htmlspecialchars($sesion["fecha"]) ?></td>
                        <td><?= $sesion["grado"] !== null ? (int) $sesion["grado"] . "°" . htmlspecialchars($sesion["grupo"]) : "-" ?></td>
                        <td><?= htmlspecialchars($sesion["dba_codigo"] ?? "-") ?></td>
                        <td>
                            <a href="../sesiones/registrar_progreso.php?id_sesion=<?= (int) $sesion['id_sesion'] ?>"
                               class="btn btn-sm btn-primary">Registrar progreso</a>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mt-4">📚 DBA correspondientes</h4>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Grado</th>
                        <th>Periodo</th>
                        <th>Código</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dbaLista)): ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            No hay DBA para los cursos a tu cargo.
                        </td>
                    </tr>
                    <?php else: foreach ($dbaLista as $dba): ?>
                    <tr>
                        <td><?= htmlspecialchars($dba["materia"] ?? "-") ?></td>
                        <td><?= (int) $dba["grado"] ?>°</td>
                        <td><?= (int) $dba["periodo"] ?></td>
                        <td><?= htmlspecialchars($dba["codigo"]) ?></td>
                        <td><?= htmlspecialchars($dba["descripcion"]) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
