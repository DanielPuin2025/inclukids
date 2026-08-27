-- 12 juegos (actividades) para Grado 1 - Lengua Castellana, 3 por cada
-- periodo/DBA (L1-P1..L1-P4), tomados de IncluKids_Juegos_Grado1_LenguaCastellana.xlsx.
-- Son solo el registro de datos (nombre, DBA, objetivo, especificacion de la
-- mecanica); el motor de juego para reproducirlos todavia no esta construido
-- salvo para 'Sonido y Figura' (tipo Seleccionar), que ya usa el mismo formato
-- de preguntas de opcion multiple que el resto de la app.

INSERT INTO actividades (id_dba, id_tipo, nombre, descripcion, instrucciones)
SELECT d.id_dba, t.id_tipo, x.nombre, x.descripcion, x.instrucciones
FROM (
    SELECT 'El Jardín de las Vocales' AS nombre, 7 AS id_tipo, 'L1-P1' AS dba_codigo, 'Reconocer visual y auditivamente las cinco vocales asociándolas a imágenes de su entorno.' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) El pictograma de la vocal aparece en el centro. 2) El niño escucha el sonido de la vocal (botón de audio repetible). 3) Debe arrastrar la imagen cuyo nombre empieza con esa vocal hasta la flor correspondiente. 4) Al soltar correctamente, la flor florece con animación suave.
Adaptaciones TEA: Una sola instrucción por pantalla, leída en voz alta y con pictograma; máximo 5 elementos visibles a la vez; sin límite de tiempo en nivel fácil.
Niveles: Fácil / Medio / Difícil (aumenta cantidad de vocales por ronda y quita el apoyo sonoro)
Tiempo estimado: 5-8 minutos
Recompensa: Estrellas + insignia \'Jardinero de vocales\' al completar las 5 vocales' AS instrucciones
    UNION ALL
    SELECT 'Sonido y Figura' AS nombre, 4 AS id_tipo, 'L1-P1' AS dba_codigo, 'Identificar el sonido inicial de una palabra y relacionarlo con la imagen correcta.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se reproduce una palabra. 2) Aparecen imágenes con nombres de sonido inicial distinto. 3) El niño toca la imagen cuyo nombre comienza con el mismo sonido. 4) Confirmación inmediata.
Adaptaciones TEA: Solo 3 opciones visibles; ícono de repetición de audio siempre disponible; sin distractores sonoros de fondo.
Niveles: Fácil (2 opciones) / Medio (3 opciones) / Difícil (4 opciones con sonidos iniciales parecidos)
Tiempo estimado: 5-7 minutos
Recompensa: Sellos de colores acumulables por ronda correcta' AS instrucciones
    UNION ALL
    SELECT 'Memo-Vocales' AS nombre, 1 AS id_tipo, 'L1-P1' AS dba_codigo, 'Asociar el grafema de cada vocal (mayúscula y minúscula) con su imagen representativa mediante un juego de memoria.' AS descripcion, 'Mecánica: Memoria (parejas)
Cómo se juega: 1) El niño toca una carta y se voltea. 2) Toca una segunda carta. 3) Si coinciden (vocal-imagen), permanecen visibles con animación de brillo. 4) Si no coinciden, se voltean de nuevo tras 2 segundos.
Adaptaciones TEA: Cuadrícula pequeña (máximo 12 cartas); resaltado visual de las cartas ya reveladas; sin límite de tiempo.
Niveles: Fácil (6 pares, 2 volteos simultáneos permitidos) / Medio (8 pares) / Difícil (10 pares con mayúscula-minúscula mezcladas)
Tiempo estimado: 6-10 minutos
Recompensa: Trofeo \'Memoria de oro\' al completar el tablero sin ayuda' AS instrucciones
    UNION ALL
    SELECT 'Caza-Consonantes' AS nombre, 2 AS id_tipo, 'L1-P2' AS dba_codigo, 'Identificar consonantes sencillas (m, p, l, s) dentro de imágenes y palabras cortas.' AS descripcion, 'Mecánica: Clasificar palabras / clasificación por categorías
Cómo se juega: 1) Aparece una tarjeta con imagen y nombre. 2) El niño arrastra la tarjeta hasta la cesta de la consonante inicial correspondiente. 3) La cesta emite una señal visual si la clasificación es correcta.
Adaptaciones TEA: Máximo 4 categorías simultáneas; pictograma grande de la consonante sobre cada cesta; retroalimentación inmediata por tarjeta.
Niveles: Fácil (2 consonantes) / Medio (3 consonantes) / Difícil (4 consonantes con distractores fonéticos)
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Cazador de letras\' por cesta completada correctamente' AS instrucciones
    UNION ALL
    SELECT 'Constructor de Sílabas' AS nombre, 7 AS id_tipo, 'L1-P2' AS dba_codigo, 'Combinar una consonante y una vocal para formar sílabas directas (ma, pa, la, etc.).' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Se presentan dos bloques (consonante y vocal). 2) El niño los arrastra y los junta en la base. 3) Al unirse correctamente, se escucha el sonido de la sílaba y se añade un ladrillo a la torre.
Adaptaciones TEA: Solo dos piezas por ronda; combinación resaltada con color al acercarse; sin límite de tiempo en nivel fácil.
Niveles: Fácil (sílabas con m, p, l) / Medio (se suman s, t, n) / Difícil (sílabas inversas y trabadas simples)
Tiempo estimado: 6-9 minutos
Recompensa: Ladrillo dorado por cada sílaba correctamente formada' AS instrucciones
    UNION ALL
    SELECT 'Rompecabezas Silábico' AS nombre, 3 AS id_tipo, 'L1-P2' AS dba_codigo, 'Armar una palabra completa uniendo piezas de rompecabezas, cada una con una sílaba.' AS descripcion, 'Mecánica: Rompecabezas
Cómo se juega: 1) Se muestran las piezas desordenadas. 2) El niño arrastra cada pieza hasta su lugar. 3) Al encajar todas, se revela la imagen y se lee la palabra en voz alta.
Adaptaciones TEA: Piezas con bordes de encaje evidente; máximo 3 piezas por palabra; imagen de referencia siempre visible.
Niveles: Fácil (2 piezas) / Medio (3 piezas) / Difícil (4 piezas con sílabas menos frecuentes)
Tiempo estimado: 6-9 minutos
Recompensa: Pieza dorada de colección por cada rompecabezas completado' AS instrucciones
    UNION ALL
    SELECT 'Une la Palabra' AS nombre, 2 AS id_tipo, 'L1-P3' AS dba_codigo, 'Relacionar una palabra escrita con la imagen que representa su significado.' AS descripcion, 'Mecánica: Unir palabra con imagen
Cómo se juega: 1) Se presentan palabras e imágenes en dos columnas. 2) El niño traza una línea o toca la palabra y luego la imagen correspondiente. 3) La conexión correcta se resalta en verde suave.
Adaptaciones TEA: Máximo 4 pares por ronda; tipografía grande y espaciada; sin distractores visuales adicionales.
Niveles: Fácil (3 pares) / Medio (4 pares) / Difícil (5 pares con palabras visualmente parecidas)
Tiempo estimado: 5-8 minutos
Recompensa: Cinta de color por cada ronda completada sin errores' AS instrucciones
    UNION ALL
    SELECT 'Lector Estrella' AS nombre, 4 AS id_tipo, 'L1-P3' AS dba_codigo, 'Leer palabras sencillas en voz alta o de forma guiada, apoyándose en imágenes y en la lectura modelada.' AS descripcion, 'Mecánica: Lectura interactiva
Cómo se juega: 1) Aparece la palabra con su imagen. 2) El niño puede tocar \'escuchar\' para oír el modelo de lectura. 3) Toca \'leer\' cuando esté listo y confirma que la leyó. 4) Se enciende una estrella en su constelación.
Adaptaciones TEA: El estudiante puede escuchar el modelo de lectura antes de intentarlo; sin evaluación de pronunciación estricta, solo acompañamiento.
Niveles: Fácil (palabras de 2 sílabas con apoyo total) / Medio (palabras de 3 sílabas) / Difícil (palabras sin apoyo sonoro previo)
Tiempo estimado: 6-9 minutos
Recompensa: Estrella dorada acumulable en la \'constelación lectora\' del estudiante' AS instrucciones
    UNION ALL
    SELECT 'Busca la Palabra Correcta' AS nombre, 4 AS id_tipo, 'L1-P3' AS dba_codigo, 'Discriminar visualmente una palabra correcta entre otras visualmente parecidas, apoyándose en una imagen.' AS descripcion, 'Mecánica: Verdadero o falso
Cómo se juega: 1) Se muestra una imagen. 2) Aparecen 2 o 3 palabras parecidas entre sí. 3) El niño toca la palabra que corresponde a la imagen. 4) Si acierta, la lupa ilumina la palabra correcta; si falla, se resalta la diferencia entre las palabras.
Adaptaciones TEA: Máximo 2 opciones en nivel fácil; diferencia entre palabras resaltada con color al fallar; sin tiempo límite.
Niveles: Fácil (2 opciones muy distintas) / Medio (2 opciones parecidas) / Difícil (3 opciones parecidas)
Tiempo estimado: 5-7 minutos
Recompensa: Lupa dorada de \'Detective de palabras\' por ronda completada sin errores' AS instrucciones
    UNION ALL
    SELECT 'Escribo mi Palabra' AS nombre, 6 AS id_tipo, 'L1-P4' AS dba_codigo, 'Escribir palabras sencillas apoyándose en una imagen y en el modelo de la palabra completa.' AS descripcion, 'Mecánica: Completar frases / dictado con imágenes
Cómo se juega: 1) Aparece la imagen del objeto. 2) El niño elige letras del banco o las escribe en las casillas. 3) Puede activar el modelo de apoyo si lo necesita. 4) Al completar la palabra, se añade al diccionario ilustrado.
Adaptaciones TEA: Modelo de la palabra visible como apoyo opcional; casillas grandes; sin corrección negativa, solo guía.
Niveles: Fácil (con modelo completo visible) / Medio (con modelo parcial) / Difícil (sin modelo, solo imagen)
Tiempo estimado: 6-9 minutos
Recompensa: Sello \'Escritor en progreso\' por cada palabra escrita correctamente' AS instrucciones
    UNION ALL
    SELECT 'Mi Primera Oración' AS nombre, 5 AS id_tipo, 'L1-P4' AS dba_codigo, 'Construir una oración sencilla de sujeto-verbo-complemento apoyándose en imágenes.' AS descripcion, 'Mecánica: Formar oraciones
Cómo se juega: 1) Se presentan los bloques de imagen-palabra. 2) El niño los arrastra a la línea base en el orden que forma una oración con sentido. 3) Al completar, la oración se lee en voz alta y se añade a la historia de Cuentín.
Adaptaciones TEA: Máximo 3 bloques por oración; orden sugerido con color; opción de escuchar la oración antes de armarla.
Niveles: Fácil (bloques en orden correcto, solo unir) / Medio (bloques desordenados) / Difícil (elegir bloques entre varias opciones)
Tiempo estimado: 6-9 minutos
Recompensa: Cinta de \'Escritor de historias\' por cada oración completada' AS instrucciones
    UNION ALL
    SELECT 'Cuento Corto, Gran Aventura' AS nombre, 4 AS id_tipo, 'L1-P4' AS dba_codigo, 'Comprender un texto narrativo corto acompañado de imágenes, respondiendo preguntas sencillas sobre lo leído.' AS descripcion, 'Mecánica: Preguntas de comprensión
Cómo se juega: 1) El niño observa cada viñeta y puede escuchar la narración. 2) Avanza viñeta por viñeta a su ritmo. 3) Al finalizar, responde 2 o 3 preguntas de comprensión con apoyo de imagen. 4) Recibe retroalimentación por cada respuesta.
Adaptaciones TEA: Cuento con vocabulario sencillo y estructura predecible; posibilidad de repetir la lectura de cada viñeta; preguntas con máximo 2 opciones en nivel fácil.
Niveles: Fácil (preguntas literales, 2 opciones) / Medio (preguntas literales, 3 opciones) / Difícil (una pregunta inferencial sencilla)
Tiempo estimado: 8-10 minutos
Recompensa: Insignia \'Aventurero lector\' al terminar el cuento y responder correctamente' AS instrucciones
) x
JOIN dba d ON d.codigo = x.dba_codigo
JOIN tipoactividad t ON t.id_tipo = x.id_tipo
WHERE NOT EXISTS (SELECT 1 FROM actividades a2 WHERE a2.nombre = x.nombre);
