-- Límite de intentos de inicio de sesión por IP (anti fuerza-bruta).
-- Una fila por dirección IP; app/helpers/rate_limit.php la mantiene.
-- Tras 8 fallos seguidos, esa IP no puede volver a intentar durante
-- 15 minutos. Un inicio de sesión correcto borra la fila.

CREATE TABLE IF NOT EXISTS login_bloqueo (
    ip             VARCHAR(45)  NOT NULL,
    correo         VARCHAR(100) DEFAULT NULL,
    intentos       INT          NOT NULL DEFAULT 0,
    primer_intento DATETIME     NOT NULL,
    ultimo_intento DATETIME     NOT NULL,
    PRIMARY KEY (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
