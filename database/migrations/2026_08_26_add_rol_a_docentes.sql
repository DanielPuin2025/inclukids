-- Agrega el rol (administrador / docente) necesario para que el login
-- pueda diferenciar el panel global del panel restringido del docente.
--
-- Por defecto todos los docentes existentes quedan como 'docente'
-- (el registro público en app/views/auth/guardar_registro.php nunca
-- debe crear administradores). El correo admin@inclukids.com se
-- promueve a 'administrador' porque ya se usaba como la cuenta de
-- administración del sistema.

ALTER TABLE docentes
    ADD COLUMN rol ENUM('administrador', 'docente') NOT NULL DEFAULT 'docente' AFTER correo;

UPDATE docentes SET rol = 'administrador' WHERE correo = 'admin@inclukids.com';
