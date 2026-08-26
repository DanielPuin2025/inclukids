<?php
session_start();

if (!isset($_SESSION["id_docente"])) {
    header("Location: ../../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/DashboardController.php";
require_once __DIR__ . "/../../controllers/EstudianteController.php";
require_once __DIR__ . "/../../controllers/ActividadController.php";
require_once __DIR__ . "/../../controllers/DbaController.php";
require_once __DIR__ . "/../../controllers/SesionClaseController.php";
require_once __DIR__ . "/../../controllers/ProgresoController.php";

$modulo = $_GET["modulo"] ?? "dashboard";
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>IncluKids</title>

<link href="/IncluKids/public/css/bootstrap.min.css" rel="stylesheet">
<link href="/IncluKids/public/css/inclukids.css" rel="stylesheet">

</head>

<body>
   
<div class="dropdown" style="position: fixed; top: 20px; right: 30px; z-index: 1050;">
    <button class="btn rounded-circle d-flex align-items-center justify-content-center"
            type="button" data-bs-toggle="dropdown" aria-expanded="false"
            style="width: 46px; height: 46px; background-color: #3A7CA5; color: white; font-weight: bold; font-size: 18px; border: none;">
        <?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow">
        <li><h6 class="dropdown-header"><?= htmlspecialchars($_SESSION['nombre']) ?></h6></li>
        <li><a class="dropdown-item" href="../docentes/perfil.php">👤 Mi perfil</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="../../../logout.php">🚪 Cerrar sesión</a></li>
    </ul>
</div>


<div class="sidebar">

<h2>IncluKids</h2>

<a href="home.php?modulo=dashboard">🏠 Inicio</a>

<a href="home.php?modulo=estudiantes">👨‍🎓 Estudiantes</a>

<a href="../cursos/index.php">🏫 Cursos</a>

<a href="../dba/index.php">📚 DBA</a>

<a href="../actividades/index.php">🎮 Actividades</a>

<a href="home.php?modulo=sesiones">📅 Sesiones</a>

<a href="home.php?modulo=progreso">📊 Progreso</a>

<hr>

<a href="visualizador.php">👁️ Mi visualizador</a>

<hr>


</div>

<div class="content">

<?php

switch($modulo){

    case "dashboard":

?>

<h1>Bienvenido, <?php echo $_SESSION["nombre"]; ?> 👋</h1>

<p>Panel principal de IncluKids.</p>

<a href="../sesiones/paso1_curso.php" class="btn btn-primary btn-lg mb-3">
    ▶️ Iniciar sesión de clase
</a>

<?php
$dashboardController = new DashboardController();
$estadisticas = $dashboardController->obtenerEstadisticas();
?>

<div class="row mt-4">

<div class="col-md-3">
<div class="card">
<div class="card-body text-center">
<h5>Estudiantes</h5>
<h2><?= $estadisticas["estudiantes"] ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card">
<div class="card-body text-center">
<h5>Actividades</h5>
<h2><?= $estadisticas["actividades"] ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card">
<div class="card-body text-center">
<h5>DBA</h5>
<h2><?= $estadisticas["dba"] ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card">
<div class="card-body text-center">
<h5>Sesiones</h5>
<h2><?= $estadisticas["sesiones"] ?></h2>
</div>
</div>
</div>

</div>

<?php
break;

case "estudiantes":

$estudianteController = new EstudianteController();
$estudiantes = $estudianteController->listar();
?>

<h2>👨‍🎓 Gestión de Estudiantes</h2>

<a href="../estudiantes/nuevo.php" class="btn btn-success mb-3">
    + Nuevo estudiante
</a>

<div class="card">
    <div class="card-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Curso</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($estudiantes)): ?>
                <tr>
                    <td colspan="5" class="text-center">
                        No hay estudiantes registrados.
                    </td>
                </tr>
                <?php else: foreach ($estudiantes as $estudiante): ?>
                <tr>
                    <td><?= (int) $estudiante["id_estudiante"] ?></td>
                    <td><?= htmlspecialchars($estudiante["nombres"]) ?></td>
                    <td><?= htmlspecialchars($estudiante["apellidos"]) ?></td>
                    <td><?= $estudiante["grado"] !== null ? (int) $estudiante["grado"] . "°" . htmlspecialchars($estudiante["grupo"]) : "Sin curso" ?></td>
                    <td>
    <a href="../estudiantes/editar.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
    <a href="../estudiantes/eliminar.php?id_estudiante=<?= (int) $estudiante['id_estudiante'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este estudiante?');">Eliminar</a>
</td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>

        </table>

    </div>
</div>

<?php
break;
case "nuevo_estudiante":
?>

<!-- Aquí irá el formulario -->

<?php
break;

case "dba":

$dbaController = new DbaController();
$dbaLista = $dbaController->listar();
?>

<h2>📚 DBA</h2>

<p class="text-muted">
    Nota: falta agregar la columna "periodo" en la tabla DBA para
    completar el flujo Materia → Grado → Periodo → DBA.
</p>

<div class="card">
    <div class="card-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Materia</th>
                    <th>Grado</th>
                    <th>Código</th>
                    <th>Descripción</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($dbaLista)): ?>
                <tr>
                    <td colspan="5" class="text-center">
                        No hay DBA registrados.
                    </td>
                </tr>
                <?php else: foreach ($dbaLista as $dba): ?>
                <tr>
                    <td><?= (int) $dba["id_dba"] ?></td>
                    <td><?= htmlspecialchars($dba["materia"] ?? "Sin materia") ?></td>
                    <td><?= (int) $dba["grado"] ?>°</td>
                    <td><?= htmlspecialchars($dba["codigo"]) ?></td>
                    <td><?= htmlspecialchars($dba["descripcion"]) ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>

        </table>

    </div>
</div>

<?php
break;

case "actividades":

$actividadController = new ActividadController();
$actividades = $actividadController->listar();
?>

<h2>🎮 Actividades</h2>

<div class="card">
    <div class="card-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($actividades)): ?>
                <tr>
                    <td colspan="3" class="text-center">
                        No hay actividades registradas.
                    </td>
                </tr>
                <?php else: foreach ($actividades as $actividad): ?>
                <tr>
                    <td><?= (int) $actividad["id_actividad"] ?></td>
                    <td><?= htmlspecialchars($actividad["nombre"]) ?></td>
                    <td><?= htmlspecialchars($actividad["descripcion"]) ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>

        </table>

    </div>
</div>

<?php
break;

case "sesiones":

$sesionController = new SesionClaseController();
$sesiones = $sesionController->listarPorDocente($_SESSION["id_docente"]);
?>

<h2>📅 Sesiones de clase</h2>

<div class="card">
    <div class="card-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Curso</th>
                    <th>DBA trabajado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($sesiones)): ?>
                <tr>
                    <td colspan="5" class="text-center">
                        No hay sesiones registradas todavía.
                    </td>
                </tr>
                <?php else: foreach ($sesiones as $sesion): ?>
                <tr>
                    <td><?= (int) $sesion["id_sesion"] ?></td>
                    <td><?= htmlspecialchars($sesion["fecha"]) ?></td>
                    <td><?= $sesion["grado"] !== null ? (int) $sesion["grado"] . "°" . htmlspecialchars($sesion["grupo"]) : "-" ?></td>
                    <td><?= htmlspecialchars($sesion["dba_codigo"] ?? "-") ?> — <?= htmlspecialchars($sesion["dba_descripcion"] ?? "") ?></td>
                    <td><?= htmlspecialchars($sesion["observaciones"] ?? "") ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>

        </table>

    </div>
</div>

<?php
break;

case "progreso":

$sesionController = new SesionClaseController();
$sesionesParaProgreso = $sesionController->listarPorDocente($_SESSION["id_docente"]);
?>

<h2>📊 Progreso</h2>

<p class="text-muted">Elige una sesión para registrar o revisar el progreso de sus estudiantes.</p>

<div class="card">
    <div class="card-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Curso</th>
                    <th>DBA</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($sesionesParaProgreso)): ?>
                <tr>
                    <td colspan="4" class="text-center">
                        No hay sesiones registradas. Usa "▶️ Iniciar sesión de clase" primero.
                    </td>
                </tr>
                <?php else: foreach ($sesionesParaProgreso as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s["fecha"]) ?></td>
                    <td><?= $s["grado"] !== null ? (int) $s["grado"] . "°" . htmlspecialchars($s["grupo"]) : "-" ?></td>
                    <td><?= htmlspecialchars($s["dba_codigo"] ?? "-") ?></td>
                    <td>
                        <a href="../sesiones/registrar_progreso.php?id_sesion=<?= (int) $s['id_sesion'] ?>"
                           class="btn btn-sm btn-primary">Registrar progreso</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>

        </table>

    </div>
</div>

<?php
break;

default:
?>

<h2>Página no encontrada</h2>

<?php
break;

}

?>

</div>
<script src="/IncluKids/public/js/bootstrap.bundle.min.js"></script>

</body>

</html>