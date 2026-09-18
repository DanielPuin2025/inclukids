<?php
// Este archivo NO se abre directo en el navegador. Lo incluyen
// nuevo.php y editar.php, que ya dejan lista la variable $curso
// (null si es nuevo, o un array con datos si es edición) y
// $docentes (lista para el selector).
$esEdicion = $curso !== null;
?>
<?= flash_render() ?>

<form method="POST" action="guardar.php">

    <?= csrf_campo() ?>

    <?php if ($esEdicion): ?>
        <input type="hidden" name="id_curso" value="<?= (int) $curso['id_curso'] ?>">
    <?php endif; ?>

    <div class="row">
        <div class="col-6 mb-3">
            <label for="grado" class="form-label">Grado</label>
            <select class="form-select" id="grado" name="grado" required>
                <option value="">--</option>
                <?php for ($g = 1; $g <= 3; $g++): ?>
                    <option value="<?= $g ?>" <?= ($esEdicion && (int) $curso['grado'] === $g) ? 'selected' : '' ?>>
                        <?= $g ?>°
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col-6 mb-3">
            <label for="grupo" class="form-label">Grupo</label>
            <input type="text" class="form-control" id="grupo" name="grupo" maxlength="1"
                   style="text-transform: uppercase;"
                   value="<?= $esEdicion ? htmlspecialchars($curso['grupo']) : '' ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="anio" class="form-label">Año</label>
        <input type="number" class="form-control" id="anio" name="anio"
               min="2020" max="2100"
               value="<?= $esEdicion ? (int) $curso['anio'] : date('Y') ?>" required>
    </div>

    <div class="mb-3">
        <label for="id_docente" class="form-label">Docente a cargo</label>
        <select class="form-select" id="id_docente" name="id_docente" required>
            <option value="">-- Selecciona --</option>
            <?php foreach ($docentes as $docente): ?>
                <option value="<?= (int) $docente['id_docente'] ?>"
                    <?= ($esEdicion && (int) $curso['id_docente'] === (int) $docente['id_docente']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($docente['nombres'] . ' ' . $docente['apellidos']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-success w-100">
        <?= $esEdicion ? 'Guardar cambios' : 'Crear curso' ?>
    </button>

</form>