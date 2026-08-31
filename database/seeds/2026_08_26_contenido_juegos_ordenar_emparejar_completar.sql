-- Contenido jugable para probar los 4 tipos de juego restantes:
-- Ordenar, Rompecabezas (ambos usan app/views/juegos/ordenar.php) y
-- Emparejar, Completar (ambos reusan app/views/juegos/arrastrar.php,
-- porque mecánicamente son "arrastra la pieza a su zona correcta").

INSERT INTO contenido_juego (id_actividad, datos_json)
SELECT a.id_actividad, x.datos_json
FROM (
    SELECT 'Mi Primera Oración' AS nombre, '{"rondas": [{"instruccion": "Ordena las palabras para armar la oración", "orden_correcto": ["el_gato", "come", "pescado"], "piezas": [{"id": "el_gato", "emoji": "🐱", "etiqueta": "El gato"}, {"id": "come", "emoji": "🍽️", "etiqueta": "come"}, {"id": "pescado", "emoji": "🐟", "etiqueta": "pescado"}]}, {"instruccion": "Ordena las palabras para armar la oración", "orden_correcto": ["la_nina", "pinta", "un_sol"], "piezas": [{"id": "la_nina", "emoji": "👧", "etiqueta": "La niña"}, {"id": "pinta", "emoji": "🖌️", "etiqueta": "pinta"}, {"id": "un_sol", "emoji": "☀️", "etiqueta": "un sol"}]}, {"instruccion": "Ordena las palabras para armar la oración", "orden_correcto": ["el_perro", "salta", "la_cuerda"], "piezas": [{"id": "el_perro", "emoji": "🐶", "etiqueta": "El perro"}, {"id": "salta", "emoji": "🤸", "etiqueta": "salta"}, {"id": "la_cuerda", "emoji": "🪢", "etiqueta": "la cuerda"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Rompecabezas Silábico' AS nombre, '{"rondas": [{"instruccion": "Ordena las sílabas para formar la palabra MESA", "orden_correcto": ["me", "sa"], "piezas": [{"id": "me", "etiqueta": "ME"}, {"id": "sa", "etiqueta": "SA"}]}, {"instruccion": "Ordena las sílabas para formar la palabra PELOTA", "orden_correcto": ["pe", "lo", "ta"], "piezas": [{"id": "pe", "etiqueta": "PE"}, {"id": "lo", "etiqueta": "LO"}, {"id": "ta", "etiqueta": "TA"}]}, {"instruccion": "Ordena las sílabas para formar la palabra COMETA", "orden_correcto": ["co", "me", "ta"], "piezas": [{"id": "co", "etiqueta": "CO"}, {"id": "me", "etiqueta": "ME"}, {"id": "ta", "etiqueta": "TA"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Une la Palabra' AS nombre, '{"rondas": [{"instruccion": "Arrastra cada imagen hasta su palabra", "zonas": [{"id": "sol", "etiqueta": "SOL"}, {"id": "luna", "etiqueta": "LUNA"}, {"id": "flor", "etiqueta": "FLOR"}], "piezas": [{"id": "p1", "emoji": "☀️", "etiqueta": "sol", "zona": "sol"}, {"id": "p2", "emoji": "🌙", "etiqueta": "luna", "zona": "luna"}, {"id": "p3", "emoji": "🌸", "etiqueta": "flor", "zona": "flor"}]}, {"instruccion": "Arrastra cada imagen hasta su palabra", "zonas": [{"id": "gato", "etiqueta": "GATO"}, {"id": "pez", "etiqueta": "PEZ"}, {"id": "casa", "etiqueta": "CASA"}], "piezas": [{"id": "p1", "emoji": "🐱", "etiqueta": "gato", "zona": "gato"}, {"id": "p2", "emoji": "🐟", "etiqueta": "pez", "zona": "pez"}, {"id": "p3", "emoji": "🏠", "etiqueta": "casa", "zona": "casa"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Escribo mi Palabra' AS nombre, '{"rondas": [{"instruccion": "☀️ ¿Cuál es la palabra de la imagen?", "zonas": [{"id": "blank", "etiqueta": "?", "emoji": "⬜"}], "piezas": [{"id": "sol", "etiqueta": "SOL", "zona": "blank"}, {"id": "luna", "etiqueta": "LUNA", "zona": null}, {"id": "flor", "etiqueta": "FLOR", "zona": null}]}, {"instruccion": "🐘 ¿Cuál es la palabra de la imagen?", "zonas": [{"id": "blank", "etiqueta": "?", "emoji": "⬜"}], "piezas": [{"id": "elefante", "etiqueta": "ELEFANTE", "zona": "blank"}, {"id": "oso", "etiqueta": "OSO", "zona": null}, {"id": "uva", "etiqueta": "UVA", "zona": null}]}]}' AS datos_json
) x
JOIN actividades a ON a.nombre = x.nombre
ON DUPLICATE KEY UPDATE datos_json = VALUES(datos_json);
