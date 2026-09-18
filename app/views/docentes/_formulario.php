<?php
// Este archivo NO se abre directo en el navegador. Lo incluyen
// nuevo.php y editar.php, que ya dejan lista la variable $docente
// (null si es nuevo, o un array con datos si es edición).
$esEdicion = $docente !== null;
?>
<?= flash_render() ?>

<form method="POST" action="guardar.php">

    <?= csrf_campo() ?>

    <?php if ($esEdicion): ?>
        <input type="hidden" name="id_docente" value="<?= (int) $docente['id_docente'] ?>">
    <?php endif; ?>

    <div class="row">
        <div class="col-6 mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres"
                   value="<?= $esEdicion ? htmlspecialchars($docente['nombres']) : '' ?>" required>
        </div>
        <div class="col-6 mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos"
                   value="<?= $esEdicion ? htmlspecialchars($docente['apellidos']) : '' ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="correo" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="correo" name="correo"
               value="<?= $esEdicion ? htmlspecialchars($docente['correo']) : '' ?>" required>
    </div>

    <?php if (!$esEdicion): ?>
        <p class="text-muted small">
            Se genera una contraseña temporal al crear la cuenta; se muestra
            una sola vez para que se la pases al docente. Puede cambiarla
            desde "Mi perfil" al entrar, o recuperarla por correo si la olvida.
        </p>
    <?php endif; ?>

    <button type="submit" class="btn btn-success w-100">
        <?= $esEdicion ? 'Guardar cambios' : 'Crear cuenta' ?>
    </button>

</form>
