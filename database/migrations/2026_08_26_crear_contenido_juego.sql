-- Contenido jugable para las mecánicas que NO son de opción múltiple
-- (contenidoactividad ya cubre esa, con pregunta/opcion_a-d). Cada
-- mecánica nueva (arrastrar y soltar, memoria, rompecabezas, etc.)
-- necesita una forma de datos distinta (rondas, pares, piezas...),
-- así que en vez de forzar columnas fijas para todas, se guarda un
-- solo bloque de datos en JSON por actividad, y cada motor de juego
-- (app/views/juegos/*.php) sabe interpretar su propia forma de JSON.

CREATE TABLE IF NOT EXISTS contenido_juego (
    id_contenido_juego INT NOT NULL AUTO_INCREMENT,
    id_actividad INT NOT NULL,
    datos_json LONGTEXT NOT NULL,
    PRIMARY KEY (id_contenido_juego),
    UNIQUE KEY unico_por_actividad (id_actividad),
    CONSTRAINT fk_contenido_juego_actividad FOREIGN KEY (id_actividad) REFERENCES actividades (id_actividad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
