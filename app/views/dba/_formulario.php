<?php
// Incluido por nuevo.php y editar.php. Espera $dba (null si es
// nuevo) y $materias (lista para el selector).
$esEdicion = $dba !== null;
?>
<form method="POST" action="guardar.php">

    <?= csrf_campo() ?>

    <?php if ($esEdicion): ?>
        <input type="hidden" name="id_dba" value="<?= (int) $dba['id_dba'] ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label for="id_materia" class="form-label">Materia</label>
        <select class="form-select" id="id_materia" name="id_materia" required>
            <option value="">-- Selecciona --</option>
            <?php foreach ($materias as $materia): ?>
                <option value="<?= (int) $materia['id_materia'] ?>"
                    <?= ($esEdicion && (int) $dba['id_materia'] === (int) $materia['id_materia']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($materia['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row">
        <div class="col-4 mb-3">
            <label for="grado" class="form-label">Grado</label>
            <select class="form-select" id="grado" name="grado" required>
                <option value="">--</option>
                <?php for ($g = 1; $g <= 3; $g++): ?>
                    <option value="<?= $g ?>" <?= ($esEdicion && (int) $dba['grado'] === $g) ? 'selected' : '' ?>><?= $g ?>°</option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-4 mb-3">
            <label for="periodo" class="form-label">Periodo</label>
            <select class="form-select" id="periodo" name="periodo" required>
                <option value="">--</option>
                <?php for ($p = 1; $p <= 4; $p++): ?>
                    <option value="<?= $p ?>" <?= ($esEdicion && (int) $dba['periodo'] === $p) ? 'selected' : '' ?>><?= $p ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-4 mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" class="form-control" id="codigo" name="codigo"
                   value="<?= $esEdicion ? htmlspecialchars($dba['codigo']) : '' ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= $esEdicion ? htmlspecialchars($dba['descripcion']) : '' ?></textarea>
    </div>

    <button type="submit" class="btn btn-success w-100"><?= $esEdicion ? 'Guardar cambios' : 'Crear DBA' ?></button>
</form>