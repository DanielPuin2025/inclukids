<?php
require __DIR__ . "/../../bootstrap.php";

require_once __DIR__ . "/../../controllers/DocenteController.php";

$docenteController = new DocenteController();
$docente = $docenteController->obtenerPorId($_SESSION["id_docente"]);

?>
<?php render_head("Mi perfil"); ?>
<body>
<div class="container mt-5" style="max-width: 500px;">

    <h2 class="mt-3 mb-4">👤 Mi perfil</h2>

    <?php if (!empty($_SESSION["forzar_cambio_password"])): ?>
        <div class="alert alert-danger">
            <strong>Cambia tu contraseña para continuar.</strong> La cuenta sigue con
            una contraseña insegura (posiblemente la de fábrica). El resto del panel
            está bloqueado hasta que la cambies aquí abajo.
        </div>
    <?php endif; ?>

    <?= flash_render() ?>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Mis datos</h5>
            <form method="POST" action="guardar_perfil.php">
                <?= csrf_campo() ?>
                <div class="mb-3">
                    <label class="form-label">Nombres</label>
                    <input type="text" class="form-control" name="nombres"
                           value="<?= htmlspecialchars($docente['nombres']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" class="form-control" name="apellidos"
                           value="<?= htmlspecialchars($docente['apellidos']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo</label>
                    <input type="email" class="form-control" name="correo"
                           value="<?= htmlspecialchars($docente['correo']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Guardar datos</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Cambiar contraseña</h5>
            <form method="POST" action="guardar_password.php">
                <?= csrf_campo() ?>
                <div class="mb-3">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" class="form-control" name="nueva" minlength="8" required>
                    <small class="text-muted">Al menos 8 caracteres. No puede ser una contraseña común.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar nueva contraseña</label>
                    <input type="password" class="form-control" name="confirmar" minlength="8" required>
                </div>
                <button type="submit" class="btn btn-secondary w-100">Cambiar contraseña</button>
            </form>
        </div>
    </div>

</div>
</body>
</html>