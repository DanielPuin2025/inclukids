<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/CursoController.php";
require_once __DIR__ . "/../../controllers/EstudianteController.php";

/**
 * Alta de estudiantes en lote: se pega una lista (una persona por
 * línea, "Nombres, Apellidos") y se dan de alta todos de una vez en
 * el mismo curso, dentro de una transacción.
 *
 * GET  -> muestra el formulario.
 * POST -> valida cada línea; si hay errores, vuelve a pintar el
 *         formulario con el texto y los errores; si no, inserta todo
 *         y redirige con un aviso.
 */
$esAdmin = ($_SESSION["rol"] ?? "docente") === "administrador";
$idDocente = (int) $_SESSION["id_docente"];

$cursoController = new CursoController();
$cursos = $esAdmin
    ? $cursoController->listar()
    : $cursoController->listarPorDocente($idDocente);

$idCursoSel = (int) ($_GET["id_curso"] ?? $_POST["id_curso"] ?? 0);
$texto = $_POST["lista"] ?? "";
$errores = [];

/** A dónde volver tras guardar. */
function volver_estudiantes($esAdmin, $idCurso)
{
    if ($esAdmin) {
        return $idCurso > 0 ? "curso.php?id_curso=" . $idCurso : "index.php";
    }
    return "mis_estudiantes.php";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    csrf_verificar();

    // El docente solo puede importar a un curso suyo (se valida aquí
    // además del <select>, porque el POST se puede forjar).
    $cursoDestino = $cursoController->obtenerPorId($idCursoSel);
    $puede = $cursoDestino
        && ($esAdmin || (int) $cursoDestino["id_docente"] === $idDocente);

    if (!$puede) {
        $errores[] = "Elige un curso válido.";
    }

    // Una línea por estudiante: "Nombres, Apellidos" (coma o tabulación).
    $filas = [];
    $lineas = preg_split('/\r\n|\r|\n/', (string) $texto);

    foreach ($lineas as $i => $linea) {
        $linea = trim($linea);
        if ($linea === "") {
            continue;
        }

        $n = $i + 1;

        if (strpos($linea, "\t") !== false) {
            $partes = explode("\t", $linea, 2);
        } elseif (strpos($linea, ",") !== false) {
            $partes = explode(",", $linea, 2);
        } else {
            $errores[] = "Línea $n: separa nombres y apellidos con una coma.";
            continue;
        }

        $nombres = trim($partes[0]);
        $apellidos = trim($partes[1] ?? "");

        if ($nombres === "" || $apellidos === "") {
            $errores[] = "Línea $n: faltan los nombres o los apellidos.";
            continue;
        }
        if (mb_strlen($nombres) > 100 || mb_strlen($apellidos) > 100) {
            $errores[] = "Línea $n: nombres o apellidos demasiado largos (máx. 100).";
            continue;
        }

        $filas[] = [$nombres, $apellidos];
    }

    if (!$errores && empty($filas)) {
        $errores[] = "No hay ninguna línea con datos para importar.";
    }

    if (!$errores) {
        $estudianteController = new EstudianteController();
        $n = $estudianteController->crearVarios($idCursoSel, $filas);

        $etiquetaCurso = (int) $cursoDestino["grado"] . "°" . $cursoDestino["grupo"];
        flash_set("exito", "$n estudiante" . ($n === 1 ? "" : "s") . " agregado" . ($n === 1 ? "" : "s") . " a $etiquetaCurso.");
        header("Location: " . volver_estudiantes($esAdmin, $idCursoSel));
        exit();
    }
}
?>
<?php render_head("Importar estudiantes - IncluKids"); ?>
<body>
<div class="container mt-5" style="max-width: 640px;">

    <h2 class="mt-3 mb-1">📋 Importar estudiantes</h2>
    <p class="text-muted mb-4">
        Pega una lista con <strong>una persona por línea</strong>, separando
        nombres y apellidos con una coma. Ejemplo:
    </p>
    <pre class="codigo" style="display:block; white-space:pre; padding:12px;">Ana María, Gómez Ruiz
Luis, Pérez Salgado
Sofía, Rodríguez</pre>

    <?php if (empty($cursos)): ?>
        <div class="alert alert-warning mt-3">
            <?= $esAdmin
                ? 'No hay cursos registrados todavía. Crea uno desde "Cursos" antes de importar.'
                : 'No tienes cursos a tu cargo todavía.' ?>
        </div>
    <?php else: ?>

        <?php if ($errores): ?>
            <div class="alert alert-danger mt-3">
                <strong>No se importó nada.</strong> Revisa:
                <ul class="mb-0 mt-2">
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="importar.php" class="mt-3">
            <?= csrf_campo() ?>

            <div class="mb-3">
                <label for="id_curso" class="form-label">Curso</label>
                <select class="form-select" id="id_curso" name="id_curso" required>
                    <option value="">-- Selecciona --</option>
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= (int) $c['id_curso'] ?>"
                            <?= ((int) $c['id_curso'] === $idCursoSel) ? 'selected' : '' ?>>
                            <?= (int) $c['grado'] ?>°<?= htmlspecialchars($c['grupo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="lista" class="form-label">Lista de estudiantes</label>
                <textarea class="form-control" id="lista" name="lista" rows="12"
                          placeholder="Nombres, Apellidos" required><?= htmlspecialchars($texto) ?></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100">Importar</button>
        </form>

    <?php endif; ?>

</div>
</body>
</html>
