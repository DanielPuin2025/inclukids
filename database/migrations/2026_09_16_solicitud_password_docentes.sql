-- Reemplaza el intento anterior de recuperar contraseña por código de
-- correo (nunca se llegó a configurar el SMTP) por algo más simple: el
-- docente manda una "solicitud de cambio" y el administrador la atiende
-- a mano generando una contraseña temporal (ya existía ese flujo, ver
-- docentes/restablecer_password.php).

DROP TABLE IF EXISTS recuperacion_password;

ALTER TABLE docentes
    ADD COLUMN solicitud_password_en DATETIME DEFAULT NULL AFTER contraseña;
