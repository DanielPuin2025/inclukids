<?php
// Incluido por nuevo.php y editar.php. Espera $actividad (null
// si es nuevo), $dbaLista y $tipos para los selectores.
$esEdicion = $actividad !== null;
?>
<?= flash_render() ?>
<form method="POST" action="guardar.php">

    <?= csrf_campo() ?>

    <?php if ($esEdicion): ?>
        <input type="hidden" name="id_actividad" value="<?= (int) $actividad['id_actividad'] ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre"
               value="<?= $esEdicion ? htmlspecialchars($actividad['nombre']) : '' ?>" required>
    </div>

    <div class="mb-3">
        <label for="id_dba" class="form-label">DBA al que pertenece</label>
        <select class="form-select" id="id_dba" name="id_dba" required>
            <option value="">-- Selecciona --</option>
            <?php foreach ($dbaLista as $dba): ?>
                <option value="<?= (int) $dba['id_dba'] ?>"
                    <?= ($esEdicion && (int) $actividad['id_dba'] === (int) $dba['id_dba']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dba['materia'] ?? '') ?> · <?= htmlspecialchars($dba['codigo']) ?> - <?= htmlspecialchars($dba['descripcion']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="id_tipo" class="form-label">Tipo de actividad</label>
        <select class="form-select" id="id_tipo" name="id_tipo" required>
            <option value="">-- Selecciona --</option>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?= (int) $tipo['id_tipo'] ?>"
                    <?= ($esEdicion && (int) $actividad['id_tipo'] === (int) $tipo['id_tipo']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tipo['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Si no hay opciones aquí, ve a la tabla "tipoactividad" en MySQL y agrega algunas (ej. "Selección múltiple").</div>
    </div>

    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="2"><?= $esEdicion ? htmlspecialchars($actividad['descripcion'] ?? '') : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label for="instrucciones" class="form-label">Instrucciones para el docente</label>
        <textarea class="form-control" id="instrucciones" name="instrucciones" rows="3"><?= $esEdicion ? htmlspecialchars($actividad['instrucciones'] ?? '') : '' ?></textarea>
    </div>

    <button type="submit" class="btn btn-success w-100"><?= $esEdicion ? 'Guardar cambios' : 'Crear actividad' ?></button>
</form>