-- Contenido jugable real para los 65 juegos que faltaban (de los 72
-- cargados en 2026_08_26_juegos_grado1_lengua_castellana.sql y
-- 2026_08_26_juegos_todos_grados_y_areas.sql): preguntas de opción
-- múltiple para los de tipo Seleccionar, y rondas (JSON) para Emparejar,
-- Arrastrar y soltar, Completar, Ordenar y Rompecabezas. Con esto los
-- 72 juegos de la app son jugables de principio a fin.

INSERT INTO contenidoactividad (id_actividad, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, orden)
SELECT a.id_actividad, x.pregunta, x.opcion_a, x.opcion_b, x.opcion_c, x.opcion_d, x.respuesta_correcta, x.orden
FROM (
    SELECT 'Adivina el Final' AS nombre, '🐸 Si el sapo estudió mucho para el examen, lo más probable es que...' AS pregunta, 'Le fue bien' AS opcion_a, 'No presentó el examen' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Adivina el Final' AS nombre, '🌧️ Si empezó a llover fuerte durante el paseo, lo más probable es que...' AS pregunta, 'Buscaron refugio' AS opcion_a, 'Siguieron como si nada' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Agudo o Grave' AS nombre, '🎻 El sonido de un violín pequeño es...' AS pregunta, 'Agudo' AS opcion_a, 'Grave' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Agudo o Grave' AS nombre, '🥁 El sonido de un tambor grande es...' AS pregunta, 'Grave' AS opcion_a, 'Agudo' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Busca la Palabra Correcta' AS nombre, '🐱 ¿Cuál es la palabra correcta para esta imagen?' AS pregunta, 'GATO' AS opcion_a, 'PATO' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Busca la Palabra Correcta' AS nombre, '🏠 ¿Cuál es la palabra correcta para esta imagen?' AS pregunta, 'CASA' AS opcion_a, 'CAZA' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Caliente o Frío' AS nombre, '☕ Una taza de café recién servido está...' AS pregunta, 'Caliente' AS opcion_a, 'Frío' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Caliente o Frío' AS nombre, '🍦 Un helado recién sacado del congelador está...' AS pregunta, 'Caliente' AS opcion_a, 'Frío' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'B' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Cambios en mi Cuerpo' AS nombre, '🦷 Cuando a un niño se le caen los dientes de leche, es un cambio...' AS pregunta, 'Normal del crecimiento' AS opcion_a, 'De otro planeta' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Cambios en mi Cuerpo' AS nombre, '📏 Con el paso de los años, la estatura de un niño...' AS pregunta, 'Aumenta' AS opcion_a, 'Siempre es igual' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Cuento Corto, Gran Aventura' AS nombre, '🐢 En el cuento, la tortuga era...' AS pregunta, 'Lenta pero constante' AS opcion_a, 'La más rápida de todas' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Cuento Corto, Gran Aventura' AS nombre, '🏁 ¿Quién ganó la carrera del cuento?' AS pregunta, 'La tortuga' AS opcion_a, 'La liebre' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Cuento y Vivencia' AS nombre, '📖 ¿Qué le pasó primero al personaje del cuento?' AS pregunta, 'Se perdió en el bosque' AS opcion_a, 'Encontró a su familia' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Cuento y Vivencia' AS nombre, '📖 ¿Cómo se sintió el personaje al final del cuento?' AS pregunta, 'Feliz y tranquilo' AS opcion_a, 'Más perdido que nunca' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'El Escenario del Cuento' AS nombre, '🏰 Si un cuento dice \'había una vez un castillo\', el lugar de la historia es...' AS pregunta, 'Un castillo' AS opcion_a, 'Una playa' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'El Escenario del Cuento' AS nombre, '🌊 Si el cuento habla de peces y olas, el lugar de la historia probablemente es...' AS pregunta, 'El mar' AS opcion_a, 'La montaña' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Empuja y Jala' AS nombre, '🚪 Abrir un cajón hacia ti es una acción de...' AS pregunta, 'Jalar' AS opcion_a, 'Empujar' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Empuja y Jala' AS nombre, '🛒 Mover un carrito de mercado hacia adelante es...' AS pregunta, 'Empujar' AS opcion_a, 'Jalar' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Fuerte o Flexible' AS nombre, '🪨 Una piedra es un material...' AS pregunta, 'Duro' AS opcion_a, 'Flexible' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Fuerte o Flexible' AS nombre, '🎈 Un globo es un material...' AS pregunta, 'Duro' AS opcion_a, 'Flexible' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'B' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Grande o Pequeña' AS nombre, '☀️ Al mediodía, cuando el sol está muy alto, las sombras son...' AS pregunta, 'Pequeñas' AS opcion_a, 'Muy grandes' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Grande o Pequeña' AS nombre, '🌇 Al atardecer, cuando el sol está bajo, las sombras son...' AS pregunta, 'Muy grandes' AS opcion_a, 'Pequeñas' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Lector Estrella' AS nombre, '☀️ ¿Cuál de estas palabras dice \'SOL\'?' AS pregunta, 'SOL' AS opcion_a, 'SAL' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Lector Estrella' AS nombre, '🌙 ¿Cuál de estas palabras dice \'LUNA\'?' AS pregunta, 'LUNA' AS opcion_a, 'LUPA' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Lectura entre Líneas' AS nombre, '🌂 Si el texto dice \'María salió con paraguas y botas\', ¿qué se puede deducir?' AS pregunta, 'Que probablemente iba a llover' AS opcion_a, 'Que hacía mucho calor' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Lectura entre Líneas' AS nombre, '😴 Si el texto dice \'Pedro bostezó todo el día en clase\', ¿qué se puede deducir?' AS pregunta, 'Que durmió poco la noche anterior' AS opcion_a, 'Que estaba muy emocionado' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Lo Que Necesito para Vivir' AS nombre, '🌱 Para crecer, una planta necesita principalmente...' AS pregunta, 'Luz y agua' AS opcion_a, 'Ruido' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Lo Que Necesito para Vivir' AS nombre, '🐟 Un pez necesita vivir en...' AS pregunta, 'Agua' AS opcion_a, 'Arena seca' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Sonido y Figura' AS nombre, '🕷️ ¿Qué imagen empieza con el mismo sonido que \'araña\'?' AS pregunta, '🕷️ araña' AS opcion_a, '🐘 elefante' AS opcion_b, '🐻 oso' AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Sonido y Figura' AS nombre, '🐘 ¿Qué imagen empieza con el mismo sonido que \'elefante\'?' AS pregunta, '🕷️ araña' AS opcion_a, '🐘 elefante' AS opcion_b, '🍇 uva' AS opcion_c, NULL AS opcion_d, 'B' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT 'Sube o Baja la Temperatura' AS nombre, '🧊 Para que el agua se convierta en hielo, la temperatura debe...' AS pregunta, 'Bajar' AS opcion_a, 'Subir' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT 'Sube o Baja la Temperatura' AS nombre, '💨 Para que el agua se convierta en vapor, la temperatura debe...' AS pregunta, 'Subir' AS opcion_a, 'Bajar' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿De Qué Tratará?' AS nombre, '📚 Un libro titulado \'El Gran Viaje del Barco\' probablemente trata de...' AS pregunta, 'Una aventura en el mar' AS opcion_a, 'Una receta de cocina' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿De Qué Tratará?' AS nombre, '📚 Un libro titulado \'Mi Huerta en Casa\' probablemente trata de...' AS pregunta, 'Plantas y cultivos' AS opcion_a, 'Carreras de autos' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Para Qué lo Dice?' AS nombre, '🎂 Si alguien dice \'¡Feliz cumpleaños!\', su propósito es...' AS pregunta, 'Felicitar' AS opcion_a, 'Pedir algo' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Para Qué lo Dice?' AS nombre, '🙏 Si alguien dice \'Por favor, pásame el lápiz\', su propósito es...' AS pregunta, 'Pedir algo' AS opcion_a, 'Agradecer' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Qué Cambió?' AS nombre, '🥚 Entre el huevo y el pollito, ¿qué cambió?' AS pregunta, 'El animal nació y creció' AS opcion_a, 'Nada cambió' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Qué Cambió?' AS nombre, '🌱 Entre la semilla y la planta, ¿qué cambió?' AS pregunta, 'La semilla germinó y creció' AS opcion_a, 'Nada cambió' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Qué Estado Es?' AS nombre, '💧 El agua que sale del grifo está en estado...' AS pregunta, 'Líquido' AS opcion_a, 'Sólido' AS opcion_b, 'Gaseoso' AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Qué Estado Es?' AS nombre, '🧊 Un hielo está en estado...' AS pregunta, 'Líquido' AS opcion_a, 'Sólido' AS opcion_b, 'Gaseoso' AS opcion_c, NULL AS opcion_d, 'B' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Qué Lenguaje Usa?' AS nombre, '🎵 Una canción usa principalmente...' AS pregunta, 'Sonido y palabras' AS opcion_a, 'Solo imágenes fijas' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Qué Lenguaje Usa?' AS nombre, '🖼️ Una pintura usa principalmente...' AS pregunta, 'Imágenes' AS opcion_a, 'Solo sonido' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Qué Opinas?' AS nombre, '🤔 Si un personaje del cuento mintió a su amigo, ¿crees que actuó bien?' AS pregunta, 'No, mentir no está bien' AS opcion_a, 'Sí, siempre está bien mentir' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Qué Opinas?' AS nombre, '🤝 Si un personaje ayudó a otro sin que se lo pidieran, ¿crees que actuó bien?' AS pregunta, 'Sí, ayudar es bueno' AS opcion_a, 'No, nunca hay que ayudar' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Qué Pasa con la Sombra?' AS nombre, '🔦 Si acercas la linterna a un objeto, su sombra se vuelve...' AS pregunta, 'Más grande' AS opcion_a, 'Más pequeña' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Qué Pasa con la Sombra?' AS nombre, '🔦 Si alejas la linterna de un objeto, su sombra se vuelve...' AS pregunta, 'Más pequeña' AS opcion_a, 'Más grande' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Qué Significa?' AS nombre, '🚦 Una luz roja de semáforo significa...' AS pregunta, 'Detente' AS opcion_a, 'Sigue' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Qué Significa?' AS nombre, '🚫 Un círculo con una línea cruzada significa...' AS pregunta, 'Prohibido' AS opcion_a, 'Permitido' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Quién Cuenta la Historia?' AS nombre, '📖 Si el cuento dice \'Yo caminé por el bosque\', ¿quién cuenta la historia?' AS pregunta, 'Un personaje (en primera persona)' AS opcion_a, 'Alguien de afuera que no participa' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Quién Cuenta la Historia?' AS nombre, '📖 Si el cuento dice \'Ana caminó por el bosque\', ¿quién cuenta la historia?' AS pregunta, 'Alguien de afuera (narrador)' AS opcion_a, 'Ana misma' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Refleja o Absorbe?' AS nombre, '🪞 Un espejo frente a la luz principalmente...' AS pregunta, 'Refleja la luz' AS opcion_a, 'Absorbe toda la luz' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Refleja o Absorbe?' AS nombre, '⚫ Una tela negra frente a la luz principalmente...' AS pregunta, 'Absorbe la luz' AS opcion_a, 'Refleja toda la luz' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Te ha Pasado?' AS nombre, '😊 ¿Alguna vez te has sentido feliz como el personaje del cuento?' AS pregunta, 'Sí, me ha pasado' AS opcion_a, 'Nunca' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Te ha Pasado?' AS nombre, '😢 ¿Alguna vez te has sentido triste como el personaje del cuento?' AS pregunta, 'Puede ser' AS opcion_a, 'Nunca podría pasar' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 2 AS orden
    UNION ALL
    SELECT '¿Vivo o No Vivo?' AS nombre, '🌳 Un árbol es un ser...' AS pregunta, 'Vivo' AS opcion_a, 'No vivo' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'A' AS respuesta_correcta, 1 AS orden
    UNION ALL
    SELECT '¿Vivo o No Vivo?' AS nombre, '🪑 Una silla es un ser...' AS pregunta, 'Vivo' AS opcion_a, 'No vivo' AS opcion_b, NULL AS opcion_c, NULL AS opcion_d, 'B' AS respuesta_correcta, 2 AS orden
) x
JOIN actividades a ON a.nombre = x.nombre
WHERE NOT EXISTS (
    SELECT 1 FROM contenidoactividad ca2
    WHERE ca2.id_actividad = a.id_actividad AND ca2.orden = x.orden
);

INSERT INTO contenido_juego (id_actividad, datos_json)
SELECT a.id_actividad, x.datos_json
FROM (
    SELECT 'Mis Cinco Sentidos' AS nombre, '{"rondas": [{"instruccion": "Arrastra lo que percibes con cada sentido", "zonas": [{"id": "ojo", "etiqueta": "VISTA", "emoji": "👁️"}, {"id": "oreja", "etiqueta": "OÍDO", "emoji": "👂"}, {"id": "lengua", "etiqueta": "GUSTO", "emoji": "👅"}], "piezas": [{"id": "p1", "emoji": "🌈", "etiqueta": "colores", "zona": "ojo"}, {"id": "p2", "emoji": "🔔", "etiqueta": "sonidos", "zona": "oreja"}, {"id": "p3", "emoji": "🍬", "etiqueta": "sabores", "zona": "lengua"}]}]}' AS datos_json
    UNION ALL
    SELECT '¿De Qué Está Hecho?' AS nombre, '{"rondas": [{"instruccion": "Arrastra cada objeto hasta su material", "zonas": [{"id": "madera", "etiqueta": "MADERA", "emoji": "🪵"}, {"id": "vidrio", "etiqueta": "VIDRIO", "emoji": "🔷"}, {"id": "tela", "etiqueta": "TELA", "emoji": "🧵"}], "piezas": [{"id": "p1", "emoji": "🪑", "etiqueta": "silla", "zona": "madera"}, {"id": "p2", "emoji": "🪟", "etiqueta": "ventana", "zona": "vidrio"}, {"id": "p3", "emoji": "👕", "etiqueta": "camisa", "zona": "tela"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Características de los Seres Vivos' AS nombre, '{"rondas": [{"instruccion": "Arrastra el ejemplo hasta su característica", "zonas": [{"id": "alimenta", "etiqueta": "SE ALIMENTA", "emoji": "🍽️"}, {"id": "respira", "etiqueta": "RESPIRA", "emoji": "💨"}, {"id": "crece", "etiqueta": "CRECE", "emoji": "📏"}], "piezas": [{"id": "p1", "emoji": "🐄", "etiqueta": "la vaca come pasto", "zona": "alimenta"}, {"id": "p2", "emoji": "🐬", "etiqueta": "el delfín respira aire", "zona": "respira"}, {"id": "p3", "emoji": "🌱", "etiqueta": "la planta crece", "zona": "crece"}]}]}' AS datos_json
    UNION ALL
    SELECT '¿A Quién Me Parezco?' AS nombre, '{"rondas": [{"instruccion": "Arrastra la característica hasta quién la comparte", "zonas": [{"id": "mama", "etiqueta": "MAMÁ", "emoji": "👩"}, {"id": "papa", "etiqueta": "PAPÁ", "emoji": "👨"}], "piezas": [{"id": "p1", "emoji": "👁️", "etiqueta": "ojos color café", "zona": "mama"}, {"id": "p2", "emoji": "📏", "etiqueta": "estatura alta", "zona": "papa"}]}]}' AS datos_json
    UNION ALL
    SELECT '¿Se Dobla o Se Rompe?' AS nombre, '{"rondas": [{"instruccion": "Arrastra el material hasta cómo reacciona a la fuerza", "zonas": [{"id": "dobla", "etiqueta": "SE DOBLA", "emoji": "〰️"}, {"id": "rompe", "etiqueta": "SE ROMPE", "emoji": "💥"}], "piezas": [{"id": "p1", "emoji": "🧦", "etiqueta": "una goma elástica", "zona": "dobla"}, {"id": "p2", "emoji": "🍶", "etiqueta": "un plato de vidrio", "zona": "rompe"}]}]}' AS datos_json
    UNION ALL
    SELECT '¿Dónde Vivo?' AS nombre, '{"rondas": [{"instruccion": "Arrastra el animal hasta su hábitat", "zonas": [{"id": "mar", "etiqueta": "EL MAR", "emoji": "🌊"}, {"id": "selva", "etiqueta": "LA SELVA", "emoji": "🌳"}, {"id": "desierto", "etiqueta": "EL DESIERTO", "emoji": "🏜️"}], "piezas": [{"id": "p1", "emoji": "🐬", "etiqueta": "delfín", "zona": "mar"}, {"id": "p2", "emoji": "🐒", "etiqueta": "mono", "zona": "selva"}, {"id": "p3", "emoji": "🐫", "etiqueta": "camello", "zona": "desierto"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Deja Pasar la Luz' AS nombre, '{"rondas": [{"instruccion": "Arrastra el material hasta su comportamiento con la luz", "zonas": [{"id": "transparente", "etiqueta": "TRANSPARENTE", "emoji": "🔍"}, {"id": "opaco", "etiqueta": "OPACO", "emoji": "⬛"}, {"id": "reflectivo", "etiqueta": "REFLECTIVO", "emoji": "✨"}], "piezas": [{"id": "p1", "emoji": "🪟", "etiqueta": "vidrio", "zona": "transparente"}, {"id": "p2", "emoji": "🧱", "etiqueta": "ladrillo", "zona": "opaco"}, {"id": "p3", "emoji": "🪞", "etiqueta": "espejo", "zona": "reflectivo"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Sonidos que Vibran' AS nombre, '{"rondas": [{"instruccion": "Arrastra el instrumento hasta lo que vibra para sonar", "zonas": [{"id": "cuerda", "etiqueta": "CUERDA", "emoji": "🎻"}, {"id": "piel", "etiqueta": "MEMBRANA", "emoji": "🥁"}], "piezas": [{"id": "p1", "emoji": "🎸", "etiqueta": "guitarra", "zona": "cuerda"}, {"id": "p2", "emoji": "🥁", "etiqueta": "tambor", "zona": "piel"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Caza-Consonantes' AS nombre, '{"rondas": [{"instruccion": "Arrastra cada imagen a su consonante inicial", "zonas": [{"id": "m", "etiqueta": "M"}, {"id": "p", "etiqueta": "P"}, {"id": "l", "etiqueta": "L"}], "piezas": [{"id": "p1", "emoji": "🌙", "etiqueta": "mano", "zona": "m"}, {"id": "p2", "emoji": "🐟", "etiqueta": "pez", "zona": "p"}, {"id": "p3", "emoji": "🍋", "etiqueta": "limón", "zona": "l"}]}, {"instruccion": "Arrastra cada imagen a su consonante inicial", "zonas": [{"id": "m", "etiqueta": "M"}, {"id": "p", "etiqueta": "P"}, {"id": "l", "etiqueta": "L"}], "piezas": [{"id": "p1", "emoji": "🐒", "etiqueta": "mono", "zona": "m"}, {"id": "p2", "emoji": "🐧", "etiqueta": "pingüino", "zona": "p"}, {"id": "p3", "emoji": "🦁", "etiqueta": "león", "zona": "l"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Señales de mi Ciudad' AS nombre, '{"rondas": [{"instruccion": "Arrastra la señal hasta su significado", "zonas": [{"id": "alto", "etiqueta": "DETENTE", "emoji": "🛑"}, {"id": "hospital", "etiqueta": "HOSPITAL", "emoji": "🏥"}, {"id": "prohibido", "etiqueta": "PROHIBIDO", "emoji": "🚫"}], "piezas": [{"id": "p1", "emoji": "🛑", "etiqueta": "señal de pare", "zona": "alto"}, {"id": "p2", "emoji": "➕", "etiqueta": "cruz roja", "zona": "hospital"}, {"id": "p3", "emoji": "🚭", "etiqueta": "no fumar", "zona": "prohibido"}]}]}' AS datos_json
    UNION ALL
    SELECT '¿Quién es Quién?' AS nombre, '{"rondas": [{"instruccion": "Arrastra la descripción hasta el personaje", "zonas": [{"id": "principe", "etiqueta": "VALIENTE", "emoji": "🤴"}, {"id": "bruja", "etiqueta": "MALVADA", "emoji": "🧙"}], "piezas": [{"id": "p1", "emoji": "⚔️", "etiqueta": "rescató al pueblo", "zona": "principe"}, {"id": "p2", "emoji": "🍎", "etiqueta": "envenenó la manzana", "zona": "bruja"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Texto, Sonido e Imagen' AS nombre, '{"rondas": [{"instruccion": "Arrastra el ejemplo hasta el tipo de lenguaje que usa", "zonas": [{"id": "texto", "etiqueta": "TEXTO", "emoji": "📝"}, {"id": "sonido", "etiqueta": "SONIDO", "emoji": "🔊"}, {"id": "imagen", "etiqueta": "IMAGEN", "emoji": "🖼️"}], "piezas": [{"id": "p1", "emoji": "📰", "etiqueta": "una noticia escrita", "zona": "texto"}, {"id": "p2", "emoji": "🎵", "etiqueta": "una canción", "zona": "sonido"}, {"id": "p3", "emoji": "🎨", "etiqueta": "una pintura", "zona": "imagen"}]}]}' AS datos_json
    UNION ALL
    SELECT '¿Quién Habla y Quién Escucha?' AS nombre, '{"rondas": [{"instruccion": "Arrastra la situación hasta el rol correcto", "zonas": [{"id": "emisor", "etiqueta": "EMISOR", "emoji": "🗣️"}, {"id": "receptor", "etiqueta": "RECEPTOR", "emoji": "👂"}], "piezas": [{"id": "p1", "emoji": "✉️", "etiqueta": "quien escribe la carta", "zona": "emisor"}, {"id": "p2", "emoji": "📬", "etiqueta": "quien la lee", "zona": "receptor"}]}]}' AS datos_json
    UNION ALL
    SELECT 'El Detective de Texturas' AS nombre, '{"rondas": [{"instruccion": "Arrastra el objeto según su textura", "zonas": [{"id": "suave", "etiqueta": "SUAVE", "emoji": "☁️"}, {"id": "aspero", "etiqueta": "ÁSPERO", "emoji": "🪨"}], "piezas": [{"id": "p1", "emoji": "🧸", "etiqueta": "peluche", "zona": "suave"}, {"id": "p2", "emoji": "🌵", "etiqueta": "cactus", "zona": "aspero"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Construye con Materiales' AS nombre, '{"rondas": [{"instruccion": "Arrastra el material más adecuado para cada uso", "zonas": [{"id": "ventana", "etiqueta": "VENTANA", "emoji": "🪟"}, {"id": "abrigo", "etiqueta": "ABRIGO", "emoji": "🧥"}], "piezas": [{"id": "p1", "emoji": "🔷", "etiqueta": "vidrio", "zona": "ventana"}, {"id": "p2", "emoji": "🧶", "etiqueta": "lana", "zona": "abrigo"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Clasifica: Planta, Animal u Objeto' AS nombre, '{"rondas": [{"instruccion": "Arrastra cada elemento a su grupo", "zonas": [{"id": "planta", "etiqueta": "PLANTA", "emoji": "🌿"}, {"id": "animal", "etiqueta": "ANIMAL", "emoji": "🐾"}, {"id": "objeto", "etiqueta": "OBJETO", "emoji": "📦"}], "piezas": [{"id": "p1", "emoji": "🌻", "etiqueta": "girasol", "zona": "planta"}, {"id": "p2", "emoji": "🐕", "etiqueta": "perro", "zona": "animal"}, {"id": "p3", "emoji": "🪑", "etiqueta": "silla", "zona": "objeto"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Pon a Prueba los Materiales' AS nombre, '{"rondas": [{"instruccion": "Arrastra el material según su resistencia", "zonas": [{"id": "flexible", "etiqueta": "FLEXIBLE", "emoji": "〰️"}, {"id": "rigido", "etiqueta": "RÍGIDO", "emoji": "🧱"}], "piezas": [{"id": "p1", "emoji": "🧦", "etiqueta": "goma", "zona": "flexible"}, {"id": "p2", "emoji": "🪨", "etiqueta": "piedra", "zona": "rigido"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Sólido, Líquido o Gas' AS nombre, '{"rondas": [{"instruccion": "Arrastra cada sustancia a su estado", "zonas": [{"id": "solido", "etiqueta": "SÓLIDO", "emoji": "🧊"}, {"id": "liquido", "etiqueta": "LÍQUIDO", "emoji": "💧"}, {"id": "gas", "etiqueta": "GAS", "emoji": "💨"}], "piezas": [{"id": "p1", "emoji": "🪨", "etiqueta": "piedra", "zona": "solido"}, {"id": "p2", "emoji": "🥛", "etiqueta": "leche", "zona": "liquido"}, {"id": "p3", "emoji": "🎈", "etiqueta": "aire en el globo", "zona": "gas"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Arma el Hábitat' AS nombre, '{"rondas": [{"instruccion": "Arrastra lo que necesita el pez para vivir", "zonas": [{"id": "agua", "etiqueta": "AGUA", "emoji": "💧"}], "piezas": [{"id": "p1", "emoji": "💧", "etiqueta": "agua", "zona": "agua"}, {"id": "p2", "emoji": "🔥", "etiqueta": "fuego", "zona": null}, {"id": "p3", "emoji": "🏖️", "etiqueta": "arena seca", "zona": null}]}]}' AS datos_json
    UNION ALL
    SELECT 'Clasifica los Materiales' AS nombre, '{"rondas": [{"instruccion": "Arrastra el material según cómo se comporta con la luz", "zonas": [{"id": "opaco", "etiqueta": "OPACO", "emoji": "⬛"}, {"id": "translucido", "etiqueta": "TRANSLÚCIDO", "emoji": "📄"}], "piezas": [{"id": "p1", "emoji": "🧱", "etiqueta": "ladrillo", "zona": "opaco"}, {"id": "p2", "emoji": "📄", "etiqueta": "papel", "zona": "translucido"}]}]}' AS datos_json
    UNION ALL
    SELECT '¿Por Dónde Viaja el Sonido?' AS nombre, '{"rondas": [{"instruccion": "Arrastra el ejemplo hasta el medio por el que viaja el sonido", "zonas": [{"id": "solido", "etiqueta": "SÓLIDO", "emoji": "🧱"}, {"id": "liquido", "etiqueta": "LÍQUIDO", "emoji": "💧"}, {"id": "gas", "etiqueta": "GASEOSO", "emoji": "💨"}], "piezas": [{"id": "p1", "emoji": "🚪", "etiqueta": "tocar una puerta de madera", "zona": "solido"}, {"id": "p2", "emoji": "🏊", "etiqueta": "escuchar bajo el agua", "zona": "liquido"}, {"id": "p3", "emoji": "🗣️", "etiqueta": "escuchar hablar a alguien", "zona": "gas"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Clasifica las Señales' AS nombre, '{"rondas": [{"instruccion": "Arrastra la señal a su categoría", "zonas": [{"id": "seguridad", "etiqueta": "SEGURIDAD", "emoji": "🦺"}, {"id": "informacion", "etiqueta": "INFORMACIÓN", "emoji": "ℹ️"}, {"id": "prohibicion", "etiqueta": "PROHIBICIÓN", "emoji": "🚫"}], "piezas": [{"id": "p1", "emoji": "🧯", "etiqueta": "extintor", "zona": "seguridad"}, {"id": "p2", "emoji": "🚻", "etiqueta": "baños", "zona": "informacion"}, {"id": "p3", "emoji": "🚭", "etiqueta": "no fumar", "zona": "prohibicion"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Arma la Escena' AS nombre, '{"rondas": [{"instruccion": "Arrastra cada elemento a su lugar en la escena", "zonas": [{"id": "personaje", "etiqueta": "PERSONAJE", "emoji": "🧑"}, {"id": "espacio", "etiqueta": "ESPACIO", "emoji": "🏞️"}, {"id": "accion", "etiqueta": "ACCIÓN", "emoji": "🏃"}], "piezas": [{"id": "p1", "emoji": "👧", "etiqueta": "una niña", "zona": "personaje"}, {"id": "p2", "emoji": "🏞️", "etiqueta": "el parque", "zona": "espacio"}, {"id": "p3", "emoji": "🏃", "etiqueta": "corre", "zona": "accion"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Arma el Afiche' AS nombre, '{"rondas": [{"instruccion": "Arrastra cada pieza a su lugar en el afiche", "zonas": [{"id": "titulo", "etiqueta": "TÍTULO", "emoji": "🔤"}, {"id": "imagen", "etiqueta": "IMAGEN", "emoji": "🖼️"}, {"id": "sonido", "etiqueta": "SONIDO", "emoji": "🔊"}], "piezas": [{"id": "p1", "emoji": "🔤", "etiqueta": "texto del título", "zona": "titulo"}, {"id": "p2", "emoji": "🖼️", "etiqueta": "foto", "zona": "imagen"}, {"id": "p3", "emoji": "🔊", "etiqueta": "ícono de audio", "zona": "sonido"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Tipo de Texto' AS nombre, '{"rondas": [{"instruccion": "Arrastra el fragmento hasta su tipo de texto", "zonas": [{"id": "receta", "etiqueta": "RECETA", "emoji": "🍳"}, {"id": "carta", "etiqueta": "CARTA", "emoji": "✉️"}], "piezas": [{"id": "p1", "emoji": "🥣", "etiqueta": "mezcle los ingredientes", "zona": "receta"}, {"id": "p2", "emoji": "💌", "etiqueta": "querido amigo, te escribo para...", "zona": "carta"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Experimento del Agua' AS nombre, '{"rondas": [{"instruccion": "🧊☀️ El hielo se dejó al sol. ¿Qué pasó?", "zonas": [{"id": "blank", "etiqueta": "?", "emoji": "⬜"}], "piezas": [{"id": "derritio", "etiqueta": "SE DERRITIÓ", "zona": "blank"}, {"id": "crecio", "etiqueta": "CRECIÓ", "zona": null}, {"id": "voló", "etiqueta": "VOLÓ", "zona": null}]}]}' AS datos_json
    UNION ALL
    SELECT 'Completa la Idea' AS nombre, '{"rondas": [{"instruccion": "🌂 María salió con paraguas y botas. Eso quiere decir que probablemente...", "zonas": [{"id": "blank", "etiqueta": "?", "emoji": "⬜"}], "piezas": [{"id": "llovera", "etiqueta": "IBA A LLOVER", "zona": "blank"}, {"id": "calor", "etiqueta": "HACÍA CALOR", "zona": null}, {"id": "fiesta", "etiqueta": "HABÍA UNA FIESTA", "zona": null}]}]}' AS datos_json
    UNION ALL
    SELECT 'Yo Antes y Ahora' AS nombre, '{"rondas": [{"instruccion": "Ordena las etapas de menor a mayor edad", "orden_correcto": ["bebe", "nino", "adulto"], "piezas": [{"id": "bebe", "emoji": "👶", "etiqueta": "bebé"}, {"id": "nino", "emoji": "🧒", "etiqueta": "niño"}, {"id": "adulto", "emoji": "🧑", "etiqueta": "adulto"}]}]}' AS datos_json
    UNION ALL
    SELECT 'El Ciclo del Agua' AS nombre, '{"rondas": [{"instruccion": "Ordena las etapas del ciclo del agua", "orden_correcto": ["mar", "vapor", "nube", "lluvia"], "piezas": [{"id": "mar", "emoji": "🌊", "etiqueta": "mar"}, {"id": "vapor", "emoji": "💨", "etiqueta": "se evapora"}, {"id": "nube", "emoji": "☁️", "etiqueta": "forma nubes"}, {"id": "lluvia", "emoji": "🌧️", "etiqueta": "cae como lluvia"}]}]}' AS datos_json
    UNION ALL
    SELECT 'El Ciclo de Vida' AS nombre, '{"rondas": [{"instruccion": "Ordena las etapas de vida de la planta", "orden_correcto": ["semilla", "brote", "planta"], "piezas": [{"id": "semilla", "emoji": "🌰", "etiqueta": "semilla"}, {"id": "brote", "emoji": "🌱", "etiqueta": "brote"}, {"id": "planta", "emoji": "🌳", "etiqueta": "planta adulta"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Arma la Escena de Sombra' AS nombre, '{"rondas": [{"instruccion": "Ordena de izquierda a derecha: fuente de luz, objeto, sombra", "orden_correcto": ["luz", "objeto", "sombra"], "piezas": [{"id": "luz", "emoji": "🔦", "etiqueta": "luz"}, {"id": "objeto", "emoji": "🧍", "etiqueta": "objeto"}, {"id": "sombra", "emoji": "⬛", "etiqueta": "sombra"}]}]}' AS datos_json
    UNION ALL
    SELECT 'El Agua Cambia' AS nombre, '{"rondas": [{"instruccion": "Ordena de menor a mayor temperatura", "orden_correcto": ["hielo", "agua", "vapor"], "piezas": [{"id": "hielo", "emoji": "🧊", "etiqueta": "hielo"}, {"id": "agua", "emoji": "💧", "etiqueta": "agua"}, {"id": "vapor", "emoji": "💨", "etiqueta": "vapor"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Ordena la Historia' AS nombre, '{"rondas": [{"instruccion": "Ordena las escenas del cuento", "orden_correcto": ["inicio", "problema", "final"], "piezas": [{"id": "inicio", "emoji": "🏡", "etiqueta": "vivía feliz en su casa"}, {"id": "problema", "emoji": "⛈️", "etiqueta": "llegó una tormenta"}, {"id": "final", "emoji": "🌈", "etiqueta": "todo se solucionó"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Inicio, Nudo y Desenlace' AS nombre, '{"rondas": [{"instruccion": "Ordena la estructura del cuento", "orden_correcto": ["inicio", "nudo", "desenlace"], "piezas": [{"id": "inicio", "emoji": "🌅", "etiqueta": "Inicio"}, {"id": "nudo", "emoji": "⚡", "etiqueta": "Nudo"}, {"id": "desenlace", "emoji": "🏁", "etiqueta": "Desenlace"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Arma la Conversación' AS nombre, '{"rondas": [{"instruccion": "Ordena los turnos de la conversación", "orden_correcto": ["saludo", "pregunta", "respuesta"], "piezas": [{"id": "saludo", "emoji": "👋", "etiqueta": "¡Hola!"}, {"id": "pregunta", "emoji": "❓", "etiqueta": "¿Cómo estás?"}, {"id": "respuesta", "emoji": "😊", "etiqueta": "Muy bien, ¿y tú?"}]}]}' AS datos_json
    UNION ALL
    SELECT 'Rompecabezas del Ciclo' AS nombre, '{"rondas": [{"instruccion": "Ordena las etapas del ciclo de vida de la mariposa", "orden_correcto": ["huevo", "oruga", "crisalida", "mariposa"], "piezas": [{"id": "huevo", "emoji": "🥚", "etiqueta": "huevo"}, {"id": "oruga", "emoji": "🐛", "etiqueta": "oruga"}, {"id": "crisalida", "emoji": "🛖", "etiqueta": "crisálida"}, {"id": "mariposa", "emoji": "🦋", "etiqueta": "mariposa"}]}]}' AS datos_json
    UNION ALL
    SELECT 'El Detective del Cuento' AS nombre, '{"rondas": [{"instruccion": "Ordena la estructura narrativa del cuento", "orden_correcto": ["inicio", "nudo", "desenlace"], "piezas": [{"id": "inicio", "emoji": "📖", "etiqueta": "Inicio"}, {"id": "nudo", "emoji": "🌀", "etiqueta": "Nudo"}, {"id": "desenlace", "emoji": "🎉", "etiqueta": "Desenlace"}]}]}' AS datos_json
) x
JOIN actividades a ON a.nombre = x.nombre
WHERE NOT EXISTS (SELECT 1 FROM contenido_juego cj2 WHERE cj2.id_actividad = a.id_actividad);
