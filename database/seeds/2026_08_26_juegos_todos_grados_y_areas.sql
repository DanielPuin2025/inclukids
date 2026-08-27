-- 60 juegos (3 por DBA) para completar Lenguaje grado 2-3 y Ciencias
-- Naturales grado 1-3 (Lenguaje grado 1 ya se cargó en
-- 2026_08_26_juegos_grado1_lengua_castellana.sql). Igual que esa carga,
-- es solo el registro de datos (nombre, DBA, objetivo, especificación
-- de la mecánica) — no todos tienen todavía motor de juego construido
-- (ver database/seeds/2026_08_26_contenido_juegos_arrastrar_memoria.sql
-- y app/views/juegos/ para los que sí).

INSERT INTO actividades (id_dba, id_tipo, nombre, descripcion, instrucciones)
SELECT d.id_dba, t.id_tipo, x.nombre, x.descripcion, x.instrucciones
FROM (
    SELECT 'Mis Cinco Sentidos' AS nombre, 2 AS id_tipo, 'CN1-P1' AS dba_codigo, 'Relacionar cada sentido con la característica del entorno que permite percibir.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta un sentido (ej. oído). 2) El niño lo relaciona con lo que permite percibir (sonidos). 3) Confirmación con ejemplo visual.
Adaptaciones TEA: Máximo 3 sentidos por ronda; pictogramas grandes y claros.
Tiempo estimado: 5-8 minutos
Recompensa: Insignia \'Explorador sensorial\'' AS instrucciones
    UNION ALL
    SELECT 'Caliente o Frío' AS nombre, 4 AS id_tipo, 'CN1-P1' AS dba_codigo, 'Clasificar objetos según la característica que se percibe con los sentidos (temperatura, textura, sabor).' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se muestra un objeto. 2) El niño elige la característica que mejor lo describe. 3) Retroalimentación inmediata.
Adaptaciones TEA: Objetos cotidianos y conocidos; máximo 2 opciones en nivel fácil.
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Detective de los sentidos\'' AS instrucciones
    UNION ALL
    SELECT 'El Detective de Texturas' AS nombre, 7 AS id_tipo, 'CN1-P1' AS dba_codigo, 'Clasificar objetos según su textura (suave/áspero) usando el sentido del tacto como referencia.' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Aparecen dos canastas: suave y áspero. 2) El niño arrastra cada objeto a la canasta correspondiente. 3) Confirmación visual al acertar.
Adaptaciones TEA: Máximo 2 categorías; imágenes grandes y contrastantes.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Manos expertas\'' AS instrucciones
    UNION ALL
    SELECT '¿De Qué Está Hecho?' AS nombre, 2 AS id_tipo, 'CN1-P2' AS dba_codigo, 'Relacionar objetos cotidianos con el material del que están hechos.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se muestra un objeto (ej. una silla de madera). 2) El niño lo relaciona con su material. 3) Confirmación con explicación breve.
Adaptaciones TEA: Máximo 4 materiales por ronda; objetos muy conocidos.
Tiempo estimado: 5-8 minutos
Recompensa: Sello \'Conocedor de materiales\'' AS instrucciones
    UNION ALL
    SELECT 'Fuerte o Flexible' AS nombre, 4 AS id_tipo, 'CN1-P2' AS dba_codigo, 'Reconocer características de los materiales (dureza, flexibilidad, color, textura) para distintos usos.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se presenta un material. 2) El niño elige cuál es su característica principal. 3) Retroalimentación inmediata.
Adaptaciones TEA: Comparaciones muy contrastantes en nivel fácil (ej. piedra vs. esponja).
Tiempo estimado: 5-7 minutos
Recompensa: Insignia \'Científico de materiales\'' AS instrucciones
    UNION ALL
    SELECT 'Construye con Materiales' AS nombre, 7 AS id_tipo, 'CN1-P2' AS dba_codigo, 'Relacionar un material con el uso más adecuado según sus características.' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Se presenta un uso (ej. hacer una ventana). 2) El niño arrastra el material más adecuado (ej. vidrio). 3) Confirmación con breve explicación.
Adaptaciones TEA: Máximo 3 opciones de material por ronda; imágenes claras de cada uso.
Tiempo estimado: 6-9 minutos
Recompensa: Medalla \'Constructor experto\'' AS instrucciones
    UNION ALL
    SELECT '¿Vivo o No Vivo?' AS nombre, 4 AS id_tipo, 'CN1-P3' AS dba_codigo, 'Diferenciar seres vivos de objetos inertes reconociendo características comunes de los seres vivos.' AS descripcion, 'Mecánica: Verdadero o falso
Cómo se juega: 1) Se muestra una imagen. 2) El niño indica si es un ser vivo o no. 3) Retroalimentación con la característica clave (se alimenta, respira, crece...).
Adaptaciones TEA: Ejemplos muy contrastantes en nivel fácil (planta vs. piedra).
Tiempo estimado: 5-7 minutos
Recompensa: Insignia \'Guardián de la vida\'' AS instrucciones
    UNION ALL
    SELECT 'Características de los Seres Vivos' AS nombre, 2 AS id_tipo, 'CN1-P3' AS dba_codigo, 'Relacionar cada característica común de los seres vivos (se alimenta, respira, crece, responde) con un ejemplo.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta una característica. 2) El niño la relaciona con la imagen de un ser vivo que la representa. 3) Confirmación visual.
Adaptaciones TEA: Máximo 3 características por ronda; pictogramas grandes.
Tiempo estimado: 6-9 minutos
Recompensa: Sello \'Biólogo junior\'' AS instrucciones
    UNION ALL
    SELECT 'Clasifica: Planta, Animal u Objeto' AS nombre, 7 AS id_tipo, 'CN1-P3' AS dba_codigo, 'Clasificar elementos del entorno en plantas, animales u objetos inertes.' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Aparecen tres canastas: planta, animal, objeto. 2) El niño arrastra cada elemento a su canasta. 3) Confirmación visual al acertar.
Adaptaciones TEA: Máximo 3 categorías; imágenes muy diferenciadas.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Clasificador de la naturaleza\'' AS instrucciones
    UNION ALL
    SELECT 'Yo Antes y Ahora' AS nombre, 5 AS id_tipo, 'CN1-P4' AS dba_codigo, 'Reconocer los cambios del cuerpo a lo largo del tiempo, ordenando etapas de crecimiento.' AS descripcion, 'Mecánica: Ordenar secuencias
Cómo se juega: 1) Se presentan imágenes de distintas etapas (bebé, niño, adulto). 2) El niño las ordena cronológicamente. 3) Confirmación visual al completar la secuencia.
Adaptaciones TEA: Máximo 3 etapas; imágenes de apoyo siempre visibles.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Línea de tiempo\'' AS instrucciones
    UNION ALL
    SELECT '¿A Quién Me Parezco?' AS nombre, 2 AS id_tipo, 'CN1-P4' AS dba_codigo, 'Reconocer semejanzas y diferencias físicas entre un niño y sus padres.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta una característica física. 2) El niño la relaciona con quién la comparte (mamá, papá). 3) Confirmación con explicación breve.
Adaptaciones TEA: Ejemplos sencillos y visuales (color de ojos, altura); sin juicios de valor.
Tiempo estimado: 5-8 minutos
Recompensa: Sello \'Familia y semejanzas\'' AS instrucciones
    UNION ALL
    SELECT 'Cambios en mi Cuerpo' AS nombre, 4 AS id_tipo, 'CN1-P4' AS dba_codigo, 'Identificar cambios físicos que experimenta el cuerpo con el paso del tiempo.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se presenta una imagen de un cambio corporal (ej. crecer los dientes). 2) El niño identifica de qué cambio se trata. 3) Retroalimentación inmediata.
Adaptaciones TEA: Ejemplos cotidianos y conocidos por el niño.
Tiempo estimado: 5-7 minutos
Recompensa: Insignia \'Yo crezco\'' AS instrucciones
    UNION ALL
    SELECT 'Empuja y Jala' AS nombre, 4 AS id_tipo, 'CN2-P1' AS dba_codigo, 'Identificar si una acción mecánica sobre un objeto es un empuje o un jalón (tipos de fuerza).' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se muestra una acción (ej. abrir un cajón). 2) El niño identifica si es empujar o jalar. 3) Retroalimentación inmediata.
Adaptaciones TEA: Ejemplos cotidianos con imágenes claras de la dirección del movimiento.
Tiempo estimado: 5-7 minutos
Recompensa: Insignia \'Físico junior\'' AS instrucciones
    UNION ALL
    SELECT '¿Se Dobla o Se Rompe?' AS nombre, 2 AS id_tipo, 'CN2-P1' AS dba_codigo, 'Relacionar un material con la forma en que reacciona ante una fuerza (se dobla, se rompe, resiste).' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta un material sometido a fuerza. 2) El niño lo relaciona con su reacción. 3) Confirmación con explicación breve.
Adaptaciones TEA: Ejemplos muy contrastantes en nivel fácil (goma vs. vidrio).
Tiempo estimado: 6-9 minutos
Recompensa: Sello \'Explorador de materiales\'' AS instrucciones
    UNION ALL
    SELECT 'Pon a Prueba los Materiales' AS nombre, 7 AS id_tipo, 'CN2-P1' AS dba_codigo, 'Clasificar materiales según su resistencia a la fuerza (flexible, rígido, quebradizo).' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Aparecen canastas por tipo de resistencia. 2) El niño arrastra cada material a su categoría. 3) Confirmación visual.
Adaptaciones TEA: Máximo 3 categorías; ejemplos conocidos del aula.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Laboratorio de materiales\'' AS instrucciones
    UNION ALL
    SELECT 'Sólido, Líquido o Gas' AS nombre, 7 AS id_tipo, 'CN2-P2' AS dba_codigo, 'Clasificar sustancias cotidianas según su estado (sólido, líquido, gaseoso).' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Aparecen tres canastas: sólido, líquido, gas. 2) El niño arrastra cada sustancia a su estado. 3) Confirmación visual al acertar.
Adaptaciones TEA: Máximo 3 categorías; ejemplos muy conocidos (agua, aire, piedra).
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Científico de estados\'' AS instrucciones
    UNION ALL
    SELECT '¿Qué Estado Es?' AS nombre, 4 AS id_tipo, 'CN2-P2' AS dba_codigo, 'Reconocer el estado de la materia (sólido, líquido, gaseoso) de una sustancia mostrada.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se muestra una sustancia. 2) El niño elige su estado. 3) Retroalimentación inmediata con explicación breve.
Adaptaciones TEA: Ejemplos con imagen y sin ambigüedad en nivel fácil.
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Detective de estados\'' AS instrucciones
    UNION ALL
    SELECT 'El Ciclo del Agua' AS nombre, 5 AS id_tipo, 'CN2-P2' AS dba_codigo, 'Reconocer la secuencia de cambios de estado del agua en su ciclo natural.' AS descripcion, 'Mecánica: Ordenar secuencias
Cómo se juega: 1) Se presentan imágenes del ciclo del agua desordenadas. 2) El niño las ordena. 3) Se anima el ciclo completo al terminar.
Adaptaciones TEA: Máximo 3-4 pasos; imágenes de apoyo siempre visibles.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Guardián del agua\'' AS instrucciones
    UNION ALL
    SELECT '¿Dónde Vivo?' AS nombre, 2 AS id_tipo, 'CN2-P3' AS dba_codigo, 'Relacionar una planta o animal con el ambiente donde vive según sus necesidades básicas.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta un ser vivo. 2) El niño lo relaciona con su hábitat. 3) Confirmación con explicación breve.
Adaptaciones TEA: Máximo 3 hábitats por ronda; imágenes grandes y reconocibles.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Explorador de hábitats\'' AS instrucciones
    UNION ALL
    SELECT 'Lo Que Necesito para Vivir' AS nombre, 4 AS id_tipo, 'CN2-P3' AS dba_codigo, 'Reconocer las necesidades básicas (luz, agua, aire, suelo) de plantas y animales.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se presenta un ser vivo. 2) El niño elige qué necesita para vivir. 3) Retroalimentación inmediata.
Adaptaciones TEA: Ejemplos cotidianos; máximo 2-3 opciones en nivel fácil.
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Cuidador de la naturaleza\'' AS instrucciones
    UNION ALL
    SELECT 'Arma el Hábitat' AS nombre, 7 AS id_tipo, 'CN2-P3' AS dba_codigo, 'Construir un hábitat adecuado combinando los elementos que un ser vivo necesita para sobrevivir.' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Se presenta un ser vivo y un espacio vacío. 2) El niño arrastra los elementos que necesita (agua, luz, alimento). 3) Al completar el hábitat, el ser vivo \'se instala\' con una animación.
Adaptaciones TEA: Máximo 3 elementos por hábitat; imágenes de apoyo siempre visibles.
Tiempo estimado: 6-9 minutos
Recompensa: Medalla \'Constructor de hábitats\'' AS instrucciones
    UNION ALL
    SELECT 'El Ciclo de Vida' AS nombre, 5 AS id_tipo, 'CN2-P4' AS dba_codigo, 'Reconocer la secuencia de cambios físicos en el ciclo de vida de una planta o animal.' AS descripcion, 'Mecánica: Ordenar secuencias
Cómo se juega: 1) Se presentan las etapas del ciclo de vida desordenadas (ej. semilla, planta pequeña, planta adulta). 2) El niño las ordena. 3) Se anima el ciclo completo.
Adaptaciones TEA: Máximo 3-4 etapas; imágenes de apoyo siempre visibles.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Guardián del ciclo de vida\'' AS instrucciones
    UNION ALL
    SELECT '¿Qué Cambió?' AS nombre, 4 AS id_tipo, 'CN2-P4' AS dba_codigo, 'Identificar el cambio físico ocurrido entre dos etapas del ciclo de vida de un ser vivo.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se muestran dos imágenes de etapas distintas. 2) El niño identifica qué cambió. 3) Retroalimentación inmediata.
Adaptaciones TEA: Comparaciones muy visibles en nivel fácil (huevo vs. pollito).
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Observador de cambios\'' AS instrucciones
    UNION ALL
    SELECT 'Rompecabezas del Ciclo' AS nombre, 3 AS id_tipo, 'CN2-P4' AS dba_codigo, 'Reconstruir visualmente las etapas del ciclo de vida de una planta o animal.' AS descripcion, 'Mecánica: Rompecabezas
Cómo se juega: 1) Se presentan piezas con las etapas del ciclo desordenadas. 2) El niño arma el rompecabezas en el orden correcto. 3) Al completarlo, se revela el ciclo completo.
Adaptaciones TEA: Máximo 4 piezas; bordes de encaje evidente.
Tiempo estimado: 6-9 minutos
Recompensa: Pieza dorada de colección' AS instrucciones
    UNION ALL
    SELECT 'Deja Pasar la Luz' AS nombre, 2 AS id_tipo, 'CN3-P1' AS dba_codigo, 'Relacionar un material con su comportamiento frente a la luz (opaco, transparente, translúcido, reflectivo).' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta un material (ej. espejo, vidrio, cartón). 2) El niño lo relaciona con su comportamiento ante la luz. 3) Confirmación con explicación breve.
Adaptaciones TEA: Máximo 3 categorías por ronda; ejemplos muy conocidos del aula.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Explorador de la luz\'' AS instrucciones
    UNION ALL
    SELECT '¿Refleja o Absorbe?' AS nombre, 4 AS id_tipo, 'CN3-P1' AS dba_codigo, 'Reconocer si un material refleja, absorbe o deja pasar la luz.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se presenta un objeto. 2) El niño elige qué le pasa a la luz al chocar con él. 3) Retroalimentación inmediata.
Adaptaciones TEA: Ejemplos con imagen clara y contraste alto en nivel fácil.
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Científico de la luz\'' AS instrucciones
    UNION ALL
    SELECT 'Clasifica los Materiales' AS nombre, 7 AS id_tipo, 'CN3-P1' AS dba_codigo, 'Clasificar materiales según cómo se comportan frente a la luz (opaco, transparente, translúcido).' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Aparecen tres canastas por tipo de material. 2) El niño arrastra cada objeto a su categoría. 3) Confirmación visual al acertar.
Adaptaciones TEA: Máximo 3 categorías; imágenes conocidas del entorno escolar.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Laboratorio de la luz\'' AS instrucciones
    UNION ALL
    SELECT 'Grande o Pequeña' AS nombre, 4 AS id_tipo, 'CN3-P2' AS dba_codigo, 'Predecir si una sombra será grande o pequeña según la distancia entre la luz, el objeto y la superficie.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se muestra una escena con luz, objeto y superficie a cierta distancia. 2) El niño predice el tamaño de la sombra. 3) Se revela el resultado para comparar.
Adaptaciones TEA: Comparaciones muy contrastantes en nivel fácil (muy cerca vs. muy lejos).
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Detective de sombras\'' AS instrucciones
    UNION ALL
    SELECT 'Arma la Escena de Sombra' AS nombre, 5 AS id_tipo, 'CN3-P2' AS dba_codigo, 'Reconocer el orden correcto de los elementos que producen una sombra: fuente de luz, objeto y superficie.' AS descripcion, 'Mecánica: Ordenar secuencias
Cómo se juega: 1) Se presentan los tres elementos desordenados. 2) El niño los ordena de forma correcta. 3) Se anima la formación de la sombra.
Adaptaciones TEA: Solo 3 elementos; imágenes de apoyo siempre visibles.
Tiempo estimado: 6-9 minutos
Recompensa: Sello \'Arquitecto de sombras\'' AS instrucciones
    UNION ALL
    SELECT '¿Qué Pasa con la Sombra?' AS nombre, 4 AS id_tipo, 'CN3-P2' AS dba_codigo, 'Reconocer cómo cambia una sombra al modificar la distancia entre la fuente de luz y el objeto.' AS descripcion, 'Mecánica: Verdadero o falso
Cómo se juega: 1) Se presenta una afirmación sobre el cambio de una sombra. 2) El niño responde si es verdadera o falsa. 3) Retroalimentación con explicación breve.
Adaptaciones TEA: Afirmaciones cortas y directas; ejemplo visual de apoyo siempre disponible.
Tiempo estimado: 5-7 minutos
Recompensa: Insignia \'Físico de la luz\'' AS instrucciones
    UNION ALL
    SELECT 'Sonidos que Vibran' AS nombre, 2 AS id_tipo, 'CN3-P3' AS dba_codigo, 'Relacionar una fuente de sonido con la vibración que lo produce.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta una fuente de sonido (ej. una guitarra). 2) El niño la relaciona con la parte que vibra para producir el sonido. 3) Confirmación con explicación breve.
Adaptaciones TEA: Máximo 3 fuentes por ronda; apoyo de audio si es posible.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Explorador del sonido\'' AS instrucciones
    UNION ALL
    SELECT 'Agudo o Grave' AS nombre, 4 AS id_tipo, 'CN3-P3' AS dba_codigo, 'Clasificar sonidos según su altura (agudo o grave).' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se describe o reproduce un sonido. 2) El niño identifica si es agudo o grave. 3) Retroalimentación inmediata.
Adaptaciones TEA: Ejemplos muy contrastantes en nivel fácil (pito vs. tambor grande).
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Oído musical\'' AS instrucciones
    UNION ALL
    SELECT '¿Por Dónde Viaja el Sonido?' AS nombre, 7 AS id_tipo, 'CN3-P3' AS dba_codigo, 'Clasificar medios (sólido, líquido, gaseoso) según su capacidad de propagar el sonido.' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Aparecen tres canastas por tipo de medio. 2) El niño arrastra ejemplos (agua, aire, madera) a su categoría. 3) Confirmación visual.
Adaptaciones TEA: Máximo 3 categorías; ejemplos cotidianos del entorno.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Científico del sonido\'' AS instrucciones
    UNION ALL
    SELECT 'El Agua Cambia' AS nombre, 5 AS id_tipo, 'CN3-P4' AS dba_codigo, 'Reconocer la secuencia de cambios de estado del agua según la temperatura (hielo, agua, vapor).' AS descripcion, 'Mecánica: Ordenar secuencias
Cómo se juega: 1) Se presentan las tres formas del agua desordenadas. 2) El niño las ordena de menor a mayor temperatura. 3) Se anima la transformación completa.
Adaptaciones TEA: Solo 3 pasos; imágenes de apoyo siempre visibles.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Guardián del agua\'' AS instrucciones
    UNION ALL
    SELECT 'Sube o Baja la Temperatura' AS nombre, 4 AS id_tipo, 'CN3-P4' AS dba_codigo, 'Reconocer si un cambio de estado del agua ocurre al subir o bajar la temperatura.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se presenta un cambio de estado (ej. agua a hielo). 2) El niño identifica si la temperatura sube o baja. 3) Retroalimentación inmediata.
Adaptaciones TEA: Ejemplos muy conocidos (congelar, hervir); máximo 2 opciones en nivel fácil.
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Termómetro experto\'' AS instrucciones
    UNION ALL
    SELECT 'Experimento del Agua' AS nombre, 6 AS id_tipo, 'CN3-P4' AS dba_codigo, 'Completar la conclusión de un pequeño experimento sobre los cambios de estado del agua.' AS descripcion, 'Mecánica: Completar frases
Cómo se juega: 1) Se describe un experimento sencillo (ej. hielo al sol). 2) El niño completa la frase que explica qué pasó. 3) Retroalimentación con la explicación completa.
Adaptaciones TEA: Banco de palabras disponible como apoyo; sin corrección negativa.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Científico experimental\'' AS instrucciones
    UNION ALL
    SELECT 'Señales de mi Ciudad' AS nombre, 2 AS id_tipo, 'L2-P1' AS dba_codigo, 'Reconocer el significado de señales y símbolos comunes del entorno (tránsito, seguridad, información).' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Aparece una señal (ej. semáforo en rojo). 2) El niño la relaciona con su significado entre varias opciones. 3) Confirmación inmediata con explicación breve.
Adaptaciones TEA: Máximo 4 señales por ronda; iconos grandes y de alto contraste; significado siempre acompañado de texto corto.
Tiempo estimado: 5-7 minutos
Recompensa: Insignia \'Guía de la ciudad\' por ronda completa' AS instrucciones
    UNION ALL
    SELECT '¿Qué Significa?' AS nombre, 4 AS id_tipo, 'L2-P1' AS dba_codigo, 'Identificar la función (informar, prohibir, advertir) de distintos símbolos del entorno.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se muestra un símbolo. 2) El niño elige entre 2-4 opciones cuál es su función. 3) Retroalimentación inmediata.
Adaptaciones TEA: Empieza con 2 opciones muy distintas; se puede repetir la pregunta las veces que se necesite.
Tiempo estimado: 5-7 minutos
Recompensa: Estrellas acumulables' AS instrucciones
    UNION ALL
    SELECT 'Clasifica las Señales' AS nombre, 7 AS id_tipo, 'L2-P1' AS dba_codigo, 'Agrupar señales según su categoría: seguridad, información o prohibición.' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Aparecen 3 canastas rotuladas (seguridad/información/prohibición). 2) El niño arrastra cada señal a su canasta. 3) Señal correcta = la canasta brilla.
Adaptaciones TEA: Máximo 3 categorías visibles; pictograma grande en cada canasta; sin límite de tiempo.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Clasificador experto\'' AS instrucciones
    UNION ALL
    SELECT '¿Quién es Quién?' AS nombre, 2 AS id_tipo, 'L2-P2' AS dba_codigo, 'Identificar personajes de un cuento y relacionarlos con su descripción.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se leen/escuchan descripciones cortas de personajes. 2) El niño relaciona cada descripción con su imagen. 3) Confirmación visual.
Adaptaciones TEA: Máximo 4 personajes por ronda; descripción disponible en audio repetible.
Tiempo estimado: 5-8 minutos
Recompensa: Cinta de \'Conocedor de personajes\'' AS instrucciones
    UNION ALL
    SELECT 'El Escenario del Cuento' AS nombre, 4 AS id_tipo, 'L2-P2' AS dba_codigo, 'Reconocer el espacio (lugar) donde ocurre la historia narrada.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se narra un fragmento corto. 2) El niño elige entre varias imágenes cuál es el lugar de la historia. 3) Retroalimentación inmediata.
Adaptaciones TEA: Imágenes muy diferenciadas entre sí en nivel fácil; audio repetible antes de responder.
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Explorador de escenarios\'' AS instrucciones
    UNION ALL
    SELECT 'Arma la Escena' AS nombre, 7 AS id_tipo, 'L2-P2' AS dba_codigo, 'Combinar personaje, espacio y acción para construir una escena coherente de un cuento.' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Se presentan tres columnas: personajes, espacios, acciones. 2) El niño arrastra un elemento de cada columna a la escena central. 3) Al formar una combinación válida, la escena cobra vida con una animación breve.
Adaptaciones TEA: Máximo 3 opciones por columna; combinaciones siempre válidas en nivel fácil.
Tiempo estimado: 6-9 minutos
Recompensa: Medalla \'Director de escena\'' AS instrucciones
    UNION ALL
    SELECT 'Cuento y Vivencia' AS nombre, 4 AS id_tipo, 'L2-P3' AS dba_codigo, 'Comprender un texto literario corto respondiendo preguntas relacionadas con su propia experiencia.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) El niño escucha/lee un cuento corto. 2) Responde preguntas de comprensión y de relación con su propia vida. 3) Retroalimentación positiva en cada respuesta.
Adaptaciones TEA: Cuentos con vocabulario cotidiano y cercano; máximo 2-3 preguntas por cuento.
Tiempo estimado: 8-10 minutos
Recompensa: Insignia \'Lector reflexivo\'' AS instrucciones
    UNION ALL
    SELECT '¿Te ha Pasado?' AS nombre, 4 AS id_tipo, 'L2-P3' AS dba_codigo, 'Relacionar situaciones de un cuento con experiencias personales, identificando semejanzas y diferencias.' AS descripcion, 'Mecánica: Verdadero o falso
Cómo se juega: 1) Se presenta una afirmación sobre el cuento comparada con la vida cotidiana. 2) El niño responde si es verdadera o falsa para él. 3) No hay respuesta \'incorrecta\' en las preguntas de opinión, solo se registra.
Adaptaciones TEA: Preguntas cortas y directas; sin presión de tiempo.
Tiempo estimado: 5-7 minutos
Recompensa: Corazón de \'Comparte tu experiencia\'' AS instrucciones
    UNION ALL
    SELECT 'Ordena la Historia' AS nombre, 5 AS id_tipo, 'L2-P3' AS dba_codigo, 'Reconstruir la secuencia de los eventos principales de un cuento corto.' AS descripcion, 'Mecánica: Ordenar secuencias
Cómo se juega: 1) Se presentan 3-4 escenas del cuento desordenadas. 2) El niño las arrastra al orden correcto. 3) Al terminar, se reproduce el cuento completo en el orden armado.
Adaptaciones TEA: Máximo 3 escenas en nivel fácil; imágenes de apoyo siempre visibles.
Tiempo estimado: 6-9 minutos
Recompensa: Boleto de \'Narrador estrella\'' AS instrucciones
    UNION ALL
    SELECT '¿De Qué Tratará?' AS nombre, 4 AS id_tipo, 'L2-P4' AS dba_codigo, 'Predecir el contenido de un texto a partir de su título e imagen de portada.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se muestra el título e imagen de un texto. 2) El niño elige de qué cree que tratará entre varias opciones. 3) Se revela el texto real para comparar.
Adaptaciones TEA: Opciones con imágenes claramente distintas; sin penalizar la predicción equivocada.
Tiempo estimado: 5-7 minutos
Recompensa: Insignia \'Detective de portadas\'' AS instrucciones
    UNION ALL
    SELECT 'Tipo de Texto' AS nombre, 2 AS id_tipo, 'L2-P4' AS dba_codigo, 'Reconocer distintos tipos de texto (cuento, receta, carta, noticia) según su estructura típica.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta un fragmento de texto. 2) El niño lo relaciona con su tipo (cuento, receta, carta...). 3) Confirmación con explicación breve de las pistas.
Adaptaciones TEA: Máximo 3 tipos de texto por ronda; fragmentos cortos y con formato visual distintivo.
Tiempo estimado: 6-9 minutos
Recompensa: Sello \'Coleccionista de textos\'' AS instrucciones
    UNION ALL
    SELECT 'Adivina el Final' AS nombre, 4 AS id_tipo, 'L2-P4' AS dba_codigo, 'Anticipar el posible final de una historia a partir de conocimientos previos y pistas del texto.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se narra una historia hasta un punto clave. 2) El niño elige el final más probable entre varias opciones. 3) Se revela el final real y se compara.
Adaptaciones TEA: Máximo 2 opciones en nivel fácil; pistas visuales de apoyo.
Tiempo estimado: 6-9 minutos
Recompensa: Estrella de \'Adivino de historias\'' AS instrucciones
    UNION ALL
    SELECT 'Texto, Sonido e Imagen' AS nombre, 2 AS id_tipo, 'L3-P1' AS dba_codigo, 'Reconocer que una obra puede combinar texto, sonido e imagen, identificando cada elemento.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta un ejemplo (afiche, video corto, canción). 2) El niño identifica qué elementos lo componen (texto/sonido/imagen). 3) Retroalimentación con ejemplos.
Adaptaciones TEA: Ejemplos cortos y conocidos; iconos grandes para cada tipo de elemento.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Explorador multimedia\'' AS instrucciones
    UNION ALL
    SELECT '¿Qué Lenguaje Usa?' AS nombre, 4 AS id_tipo, 'L3-P1' AS dba_codigo, 'Identificar si una manifestación artística usa principalmente texto, imagen, sonido o una combinación.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se muestra un ejemplo breve. 2) El niño elige qué lenguaje(s) usa. 3) Retroalimentación inmediata.
Adaptaciones TEA: Ejemplos con un solo lenguaje predominante en nivel fácil.
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Crítico de arte junior\'' AS instrucciones
    UNION ALL
    SELECT 'Arma el Afiche' AS nombre, 7 AS id_tipo, 'L3-P1' AS dba_codigo, 'Combinar texto, imagen y sonido para construir una pieza comunicativa sencilla.' AS descripcion, 'Mecánica: Arrastrar y soltar
Cómo se juega: 1) Se presenta un afiche vacío y piezas (título, imagen, ícono de sonido). 2) El niño arrastra cada pieza a su lugar. 3) Al completarlo, el afiche se anima.
Adaptaciones TEA: Máximo 3 piezas por afiche; casillas grandes y claramente marcadas.
Tiempo estimado: 6-9 minutos
Recompensa: Medalla \'Creador multimedia\'' AS instrucciones
    UNION ALL
    SELECT '¿Quién Cuenta la Historia?' AS nombre, 4 AS id_tipo, 'L3-P2' AS dba_codigo, 'Identificar al narrador de un texto narrativo y distinguirlo de los personajes.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se lee un fragmento narrado. 2) El niño identifica quién narra la historia. 3) Retroalimentación con explicación.
Adaptaciones TEA: Fragmentos cortos con un narrador claramente identificable.
Tiempo estimado: 6-9 minutos
Recompensa: Insignia \'Detective del narrador\'' AS instrucciones
    UNION ALL
    SELECT 'Inicio, Nudo y Desenlace' AS nombre, 5 AS id_tipo, 'L3-P2' AS dba_codigo, 'Reconocer la estructura narrativa básica (inicio, nudo, desenlace) de un cuento.' AS descripcion, 'Mecánica: Ordenar secuencias
Cómo se juega: 1) Se presentan 3 fragmentos del cuento. 2) El niño los ordena según la estructura narrativa. 3) Se revela el cuento completo ordenado.
Adaptaciones TEA: Máximo 3 fragmentos; imagen de apoyo para cada parte.
Tiempo estimado: 6-9 minutos
Recompensa: Boleto de \'Estructura perfecta\'' AS instrucciones
    UNION ALL
    SELECT 'El Detective del Cuento' AS nombre, 3 AS id_tipo, 'L3-P2' AS dba_codigo, 'Reconstruir visualmente los elementos de la estructura narrativa de una historia.' AS descripcion, 'Mecánica: Rompecabezas
Cómo se juega: 1) Se presentan piezas con el inicio, nudo y desenlace desordenados. 2) El niño arma el rompecabezas en el orden correcto. 3) Al completarlo, se revela la historia.
Adaptaciones TEA: Máximo 3 piezas; bordes de encaje evidente.
Tiempo estimado: 6-9 minutos
Recompensa: Pieza dorada de colección' AS instrucciones
    UNION ALL
    SELECT '¿Quién Habla y Quién Escucha?' AS nombre, 2 AS id_tipo, 'L3-P3' AS dba_codigo, 'Identificar el emisor y el receptor en una situación comunicativa específica.' AS descripcion, 'Mecánica: Emparejar
Cómo se juega: 1) Se presenta una situación (ej. una carta, una llamada). 2) El niño identifica quién es el emisor y quién el receptor. 3) Confirmación con explicación.
Adaptaciones TEA: Situaciones cotidianas y conocidas; máximo 2 roles por ronda.
Tiempo estimado: 5-8 minutos
Recompensa: Insignia \'Comunicador claro\'' AS instrucciones
    UNION ALL
    SELECT '¿Para Qué lo Dice?' AS nombre, 4 AS id_tipo, 'L3-P3' AS dba_codigo, 'Reconocer el propósito comunicativo (informar, pedir, agradecer, invitar) de un mensaje.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se presenta un mensaje corto. 2) El niño elige su propósito comunicativo. 3) Retroalimentación inmediata.
Adaptaciones TEA: Mensajes cortos con un propósito claramente predominante.
Tiempo estimado: 5-7 minutos
Recompensa: Sello \'Propósito claro\'' AS instrucciones
    UNION ALL
    SELECT 'Arma la Conversación' AS nombre, 5 AS id_tipo, 'L3-P3' AS dba_codigo, 'Ordenar los turnos de una conversación corta respetando la secuencia comunicativa.' AS descripcion, 'Mecánica: Ordenar secuencias
Cómo se juega: 1) Se presentan 3-4 turnos de diálogo desordenados. 2) El niño los ordena. 3) Se reproduce la conversación completa.
Adaptaciones TEA: Máximo 3 turnos en nivel fácil; íconos de emisor/receptor visibles.
Tiempo estimado: 6-9 minutos
Recompensa: Cinta de \'Buen conversador\'' AS instrucciones
    UNION ALL
    SELECT 'Lectura entre Líneas' AS nombre, 4 AS id_tipo, 'L3-P4' AS dba_codigo, 'Responder preguntas inferenciales sobre un texto, deduciendo información que no está escrita literalmente.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se lee un texto corto. 2) El niño responde preguntas que requieren deducir, no solo recordar. 3) Retroalimentación con la pista del texto que ayuda a inferir.
Adaptaciones TEA: Preguntas inferenciales sencillas en nivel fácil, con una pista visual de apoyo.
Tiempo estimado: 8-10 minutos
Recompensa: Insignia \'Lector entre líneas\'' AS instrucciones
    UNION ALL
    SELECT '¿Qué Opinas?' AS nombre, 4 AS id_tipo, 'L3-P4' AS dba_codigo, 'Responder preguntas críticas sobre un texto, justificando una postura sencilla.' AS descripcion, 'Mecánica: Selección múltiple
Cómo se juega: 1) Se presenta una situación del texto. 2) El niño elige la opción que mejor representa su opinión razonada. 3) Se muestra una reflexión breve sobre la respuesta.
Adaptaciones TEA: Sin respuesta única \'correcta\' en preguntas de opinión; se valora la justificación, no penaliza.
Tiempo estimado: 6-9 minutos
Recompensa: Sello \'Pensador crítico\'' AS instrucciones
    UNION ALL
    SELECT 'Completa la Idea' AS nombre, 6 AS id_tipo, 'L3-P4' AS dba_codigo, 'Completar una conclusión o idea inferida a partir de la información de un texto.' AS descripcion, 'Mecánica: Completar frases
Cómo se juega: 1) Se presenta un texto corto y una frase incompleta con la conclusión. 2) El niño elige o escribe la palabra que completa la idea. 3) Retroalimentación con la explicación de la inferencia.
Adaptaciones TEA: Banco de palabras disponible como apoyo; sin corrección negativa.
Tiempo estimado: 6-9 minutos
Recompensa: Sello \'Constructor de ideas\'' AS instrucciones
) x
JOIN dba d ON d.codigo = x.dba_codigo
JOIN tipoactividad t ON t.id_tipo = x.id_tipo
WHERE NOT EXISTS (SELECT 1 FROM actividades a2 WHERE a2.nombre = x.nombre);
