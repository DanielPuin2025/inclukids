<?php
/**
 * Segunda tanda de contenido: 2 rondas nuevas por cada juego de tipo
 * Emparejar / Arrastrar y soltar / Ordenar / Rompecabezas / Completar
 * (43 actividades), 2 preguntas nuevas por cada Seleccionar (28
 * actividades) y más pares para el único juego de Memoria.
 *
 * Mismo patrón que _build_mas_rondas.php: autoría en arrays PHP,
 * volcado a SQL con json_encode()/prepared statements para que el
 * resultado sea siempre válido y seguro de re-ejecutar.
 *
 * Uso:  php database/seeds/_build_mas_rondas_v2.php
 * Genera: database/seeds/2026_08_31_mas_rondas_v2.sql
 */

require __DIR__ . '/../../app/config/database.php';

$pdo = (new Database())->conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* ------------------------------------------------------------------ *
 *  Helpers de autoría (idénticos a la primera tanda)
 * ------------------------------------------------------------------ */
function z($id, $et, $emoji = null) {
    $x = ['id' => $id, 'etiqueta' => $et];
    if ($emoji !== null) $x['emoji'] = $emoji;
    return $x;
}
function p($id, $emoji, $et, $zona) {
    $x = ['id' => $id];
    if ($emoji !== null) $x['emoji'] = $emoji;
    $x['etiqueta'] = $et;
    $x['zona'] = $zona;
    return $x;
}
function o($id, $emoji, $et) {
    $x = ['id' => $id];
    if ($emoji !== null) $x['emoji'] = $emoji;
    $x['etiqueta'] = $et;
    return $x;
}
function rZona($instruccion, $zonas, $piezas) {
    return ['instruccion' => $instruccion, 'zonas' => $zonas, 'piezas' => $piezas];
}
function rOrden($instruccion, $ordenCorrecto, $piezas) {
    return ['instruccion' => $instruccion, 'orden_correcto' => $ordenCorrecto, 'piezas' => $piezas];
}
function rBlank($instruccion, $correcta, $distractores) {
    $piezas = [['id' => $correcta[0], 'etiqueta' => $correcta[1], 'zona' => 'blank']];
    foreach ($distractores as $d) {
        $piezas[] = ['id' => $d[0], 'etiqueta' => $d[1], 'zona' => null];
    }
    return [
        'instruccion' => $instruccion,
        'zonas' => [['id' => 'blank', 'etiqueta' => '?', 'emoji' => '⬜']],
        'piezas' => $piezas,
    ];
}
// Pregunta de Seleccionar: $opciones = ['A' => 'texto', 'B' => 'texto', ...], $correcta = 'A'
function q($pregunta, $opciones, $correcta) {
    return [
        'pregunta' => $pregunta,
        'opcion_a' => $opciones['A'] ?? null,
        'opcion_b' => $opciones['B'] ?? null,
        'opcion_c' => $opciones['C'] ?? null,
        'opcion_d' => $opciones['D'] ?? null,
        'correcta' => $correcta,
    ];
}

/* ================================================================== *
 *  RONDAS NUEVAS  (2 por actividad, se añaden al final)
 * ================================================================== */
$RONDAS = [];

/* ---------------------- GRADO 1 · Lenguaje ------------------------ */

// 3  El Jardín de las Vocales — imagen -> vocal inicial
$zv = [z('a', 'A'), z('e', 'E'), z('i', 'I'), z('o', 'O'), z('u', 'U')];
$RONDAS[3] = [
    rZona('Arrastra cada imagen a su vocal inicial', $zv, [
        p('p1', '🐜', 'arbol', 'a'), p('p2', '🐘', 'elefante', 'e'), p('p3', '🐊', 'iguana', 'i'),
        p('p4', '🐻', 'oso', 'o'), p('p5', '🍇', 'uva', 'u')]),
    rZona('Arrastra cada imagen a su vocal inicial', $zv, [
        p('p1', '⚓', 'ancla', 'a'), p('p2', '⭐', 'estrella', 'e'), p('p3', '🏝️', 'isla', 'i'),
        p('p4', '👂', 'oreja', 'o'), p('p5', '🦄', 'unicornio', 'u')]),
];

// 6  Caza-Consonantes — imagen -> consonante inicial M / P / L
$zc = [z('m', 'M'), z('p', 'P'), z('l', 'L')];
$RONDAS[6] = [
    rZona('Arrastra cada imagen a su consonante inicial', $zc, [
        p('p1', '🐒', 'mono', 'm'), p('p2', '🐷', 'puerco', 'p'), p('p3', '🦁', 'leon', 'l')]),
    rZona('Arrastra cada imagen a su consonante inicial', $zc, [
        p('p1', '🥭', 'mango', 'm'), p('p2', '🥔', 'papa', 'p'), p('p3', '🐆', 'leopardo', 'l')]),
];

// 7  Constructor de Sílabas — arma la sílaba (zona = consonante fija,
// piezas = vocales; la correcta trae zona, las distractoras zona null)
$RONDAS[7] = [
    rZona('Arma la sílaba pi', [z('cons', 'P', '🧱')], [
        p('I', null, 'I', 'cons'), p('O', null, 'O', null), p('U', null, 'U', null)]),
    rZona('Arma la sílaba mu', [z('cons', 'M', '🧱')], [
        p('U', null, 'U', 'cons'), p('A', null, 'A', null), p('E', null, 'E', null)]),
];

// 8  Rompecabezas Silábico — ordena sílabas
$RONDAS[8] = [
    rOrden('Ordena las sílabas para formar la palabra LUNA', ['a', 'b'], [
        o('a', null, 'LU'), o('b', null, 'NA')]),
    rOrden('Ordena las sílabas para formar la palabra PALOMA', ['a', 'b', 'c'], [
        o('a', null, 'PA'), o('b', null, 'LO'), o('c', null, 'MA')]),
];

// 9  Une la Palabra — imagen -> palabra
$RONDAS[9] = [
    rZona('Arrastra cada imagen hasta su palabra',
        [z('sol', 'SOL'), z('sapo', 'SAPO'), z('taza', 'TAZA')], [
        p('p1', '☀️', 'sol', 'sol'), p('p2', '🐸', 'sapo', 'sapo'), p('p3', '☕', 'taza', 'taza')]),
    rZona('Arrastra cada imagen hasta su palabra',
        [z('nube', 'NUBE'), z('flor', 'FLOR'), z('pez', 'PEZ')], [
        p('p1', '☁️', 'nube', 'nube'), p('p2', '🌸', 'flor', 'flor'), p('p3', '🐟', 'pez', 'pez')]),
];

// 12  Escribo mi Palabra — completar con imagen
$RONDAS[12] = [
    rBlank('🌳 ¿Cuál es la palabra de la imagen?', ['arbol', 'ARBOL'], [['casa', 'CASA'], ['pato', 'PATO']]),
    rBlank('🖐️ ¿Cuál es la palabra de la imagen?', ['mano', 'MANO'], [['sol', 'SOL'], ['oso', 'OSO']]),
];

// 13  Mi Primera Oración — ordena palabras
$RONDAS[13] = [
    rOrden('Ordena las palabras para armar la oración', ['a', 'b', 'c'], [
        o('a', null, 'El'), o('b', null, 'gato'), o('c', null, 'duerme')]),
    rOrden('Ordena las palabras para armar la oración', ['a', 'b', 'c', 'd'], [
        o('a', null, 'Mi'), o('b', null, 'mama'), o('c', null, 'me'), o('d', null, 'abraza')]),
];

/* --------------------- GRADO 1 · Ciencias Naturales ---------------- */

// 39  Mis Cinco Sentidos — percepción -> sentido
$zs5 = [z('vista', 'VISTA', '👀'), z('oido', 'OÍDO', '👂'), z('olfato', 'OLFATO', '👃'), z('gusto', 'GUSTO', '👅'), z('tacto', 'TACTO', '✋')];
$RONDAS[39] = [
    rZona('Arrastra lo que percibes con cada sentido', $zs5, [
        p('p1', '🌈', 'los colores', 'vista'), p('p2', '🔔', 'una campana', 'oido'),
        p('p3', '🌸', 'una flor', 'olfato'), p('p4', '🍋', 'lo agrio', 'gusto'), p('p5', '🧊', 'lo frio', 'tacto')]),
    rZona('Arrastra lo que percibes con cada sentido', $zs5, [
        p('p1', '📺', 'la television', 'vista'), p('p2', '🐕', 'un ladrido', 'oido'),
        p('p3', '☕', 'el cafe', 'olfato'), p('p4', '🍬', 'lo dulce', 'gusto'), p('p5', '🧸', 'lo suave', 'tacto')]),
];

// 41  El Detective de Texturas — objeto -> textura
$zt = [z('suave', 'SUAVE'), z('aspero', 'ÁSPERO'), z('duro', 'DURO')];
$RONDAS[41] = [
    rZona('Arrastra el objeto según su textura', $zt, [
        p('p1', '🧸', 'peluche', 'suave'), p('p2', '🌵', 'cactus', 'aspero'), p('p3', '🪨', 'piedra', 'duro')]),
    rZona('Arrastra el objeto según su textura', $zt, [
        p('p1', '☁️', 'algodon', 'suave'), p('p2', '🌳', 'corteza de arbol', 'aspero'), p('p3', '🔨', 'martillo', 'duro')]),
];

// 42  ¿De Qué Está Hecho? — objeto -> material
$zm = [z('madera', 'MADERA'), z('metal', 'METAL'), z('tela', 'TELA')];
$RONDAS[42] = [
    rZona('Arrastra cada objeto hasta su material', $zm, [
        p('p1', '🪑', 'silla', 'madera'), p('p2', '🥄', 'cuchara', 'metal'), p('p3', '👕', 'camiseta', 'tela')]),
    rZona('Arrastra cada objeto hasta su material', $zm, [
        p('p1', '🚪', 'puerta', 'madera'), p('p2', '🔑', 'llave', 'metal'), p('p3', '🧣', 'bufanda', 'tela')]),
];

// 44  Construye con Materiales — material -> uso adecuado
$zu = [z('impermeable', 'IMPERMEABLE'), z('resistente', 'RESISTENTE'), z('flexible', 'FLEXIBLE')];
$RONDAS[44] = [
    rZona('Arrastra el material más adecuado para cada uso', $zu, [
        p('p1', '☂️', 'paraguas de plastico', 'impermeable'), p('p2', '🧱', 'ladrillo para una pared', 'resistente'), p('p3', '🎈', 'globo de caucho', 'flexible')]),
    rZona('Arrastra el material más adecuado para cada uso', $zu, [
        p('p1', '🥾', 'bota para la lluvia', 'impermeable'), p('p2', '⛓️', 'cadena de metal', 'resistente'), p('p3', '🧦', 'media elastica', 'flexible')]),
];

// 46  Características de los Seres Vivos — ejemplo -> característica
$zsv = [z('alimenta', 'SE ALIMENTA'), z('respira', 'RESPIRA'), z('crece', 'CRECE Y CAMBIA')];
$RONDAS[46] = [
    rZona('Arrastra el ejemplo hasta su característica', $zsv, [
        p('p1', '🍃', 'la vaca come pasto', 'alimenta'), p('p2', '🐟', 'el pez respira por branquias', 'respira'), p('p3', '🌱', 'la semilla se vuelve planta', 'crece')]),
    rZona('Arrastra el ejemplo hasta su característica', $zsv, [
        p('p1', '🐝', 'la abeja toma nectar', 'alimenta'), p('p2', '🐶', 'el perro jadea al respirar', 'respira'), p('p3', '🐣', 'el pollito se vuelve gallina', 'crece')]),
];

// 47  Clasifica: Planta, Animal u Objeto
$zg = [z('planta', 'PLANTA', '🌱'), z('animal', 'ANIMAL', '🐾'), z('objeto', 'OBJETO', '📦')];
$RONDAS[47] = [
    rZona('Arrastra cada elemento a su grupo', $zg, [
        p('p1', '🌻', 'girasol', 'planta'), p('p2', '🐄', 'vaca', 'animal'), p('p3', '📱', 'celular', 'objeto')]),
    rZona('Arrastra cada elemento a su grupo', $zg, [
        p('p1', '🌴', 'palmera', 'planta'), p('p2', '🐢', 'tortuga', 'animal'), p('p3', '🪑', 'silla', 'objeto')]),
];

// 48  Yo Antes y Ahora — ordena de menor a mayor edad
$RONDAS[48] = [
    rOrden('Ordena de menor a mayor edad', ['a', 'b', 'c'], [
        o('a', '👶', 'bebe'), o('b', '🧒', 'nino'), o('c', '🧑', 'adulto')]),
    rOrden('Ordena de menor a mayor edad', ['a', 'b', 'c'], [
        o('a', '🍼', 'recien nacido'), o('b', '🎒', 'nino de escuela'), o('c', '👴', 'abuelo')]),
];

// 49  ¿A Quién Me Parezco? — característica -> quién la comparte
$zf = [z('mama', 'MAMÁ'), z('papa', 'PAPÁ'), z('yo', 'YO')];
$RONDAS[49] = [
    rZona('Arrastra la característica hasta quién la comparte', $zf, [
        p('p1', '👁️', 'ojos color miel como mama', 'mama'), p('p2', '📏', 'estatura alta como papa', 'papa'), p('p3', '😊', 'la sonrisa que es solo mia', 'yo')]),
    rZona('Arrastra la característica hasta quién la comparte', $zf, [
        p('p1', '💇', 'pelo rizado como mama', 'mama'), p('p2', '👃', 'nariz parecida a papa', 'papa'), p('p3', '🎨', 'mis propios gustos', 'yo')]),
];

/* --------------------- GRADO 2 · Lenguaje --------------------------- */

// 15  Señales de mi Ciudad
$zsn = [z('alto', 'DETENTE', '🛑'), z('hospital', 'HOSPITAL', '🏥'), z('prohibido', 'PROHIBIDO', '🚫')];
$RONDAS[15] = [
    rZona('Arrastra la señal hasta su significado', $zsn, [
        p('p1', '✋', 'pare de trafico', 'alto'), p('p2', '🏨', 'centro de salud', 'hospital'), p('p3', '🚫', 'no entrar', 'prohibido')]),
    rZona('Arrastra la señal hasta su significado', $zsn, [
        p('p1', '🚦', 'semaforo detenido', 'alto'), p('p2', '🚨', 'urgencias', 'hospital'), p('p3', '🚱', 'agua no potable', 'prohibido')]),
];

// 17  Clasifica las Señales
$zcs = [z('prevencion', 'PREVENCIÓN', '⚠️'), z('obligacion', 'OBLIGACIÓN', '➡️'), z('informacion', 'INFORMACIÓN', 'ℹ️')];
$RONDAS[17] = [
    rZona('Arrastra la señal a su categoría', $zcs, [
        p('p1', '⚠️', 'curva peligrosa', 'prevencion'), p('p2', '↩️', 'gire a la derecha', 'obligacion'), p('p3', '🅿️', 'zona de parqueo', 'informacion')]),
    rZona('Arrastra la señal a su categoría', $zcs, [
        p('p1', '🚸', 'cruce escolar', 'prevencion'), p('p2', '🦺', 'use casco', 'obligacion'), p('p3', '🚻', 'baños', 'informacion')]),
];

// 18  ¿Quién es Quién?
$zq2 = [z('principe', 'VALIENTE', '🤴'), z('bruja', 'MALVADA', '🧙')];
$RONDAS[18] = [
    rZona('Arrastra la acción hasta el personaje', $zq2, [
        p('p1', '⚔️', 'rescata al pueblo', 'principe'), p('p2', '🍎', 'ofrece la manzana envenenada', 'bruja')]),
    rZona('Arrastra la acción hasta el personaje', $zq2, [
        p('p1', '🛡️', 'protege a sus amigos', 'principe'), p('p2', '😈', 'lanza un hechizo malo', 'bruja')]),
];

// 20  Arma la Escena
$ze = [z('cielo', 'CIELO'), z('tierra', 'TIERRA')];
$RONDAS[20] = [
    rZona('Arrastra cada elemento a su lugar en la escena', $ze, [
        p('p1', '☀️', 'sol', 'cielo'), p('p2', '🌳', 'arbol', 'tierra')]),
    rZona('Arrastra cada elemento a su lugar en la escena', $ze, [
        p('p1', '🌙', 'luna', 'cielo'), p('p2', '🏠', 'casa', 'tierra')]),
];

// 23  Ordena la Historia
$RONDAS[23] = [
    rOrden('Ordena las escenas del cuento', ['inicio', 'problema', 'final'], [
        o('inicio', '🐣', 'el pollito nacio'), o('problema', '🦊', 'un zorro lo persiguio'), o('final', '🐔', 'su mama lo protegio')]),
    rOrden('Ordena las escenas del cuento', ['a', 'b', 'c'], [
        o('a', '🌰', 'la ardilla junto bellotas'), o('b', '❄️', 'llego el invierno'), o('c', '😋', 'tuvo comida guardada')]),
];

// 25  Tipo de Texto — fragmento -> tipo
$ztt = [z('cuento', 'CUENTO', '📖'), z('receta', 'RECETA', '🍳'), z('noticia', 'NOTICIA', '📰')];
$RONDAS[25] = [
    rZona('Arrastra el fragmento hasta su tipo de texto', $ztt, [
        p('p1', '🐉', 'Habia una vez un dragon...', 'cuento'), p('p2', '🥚', 'Bata dos huevos y agregue harina...', 'receta'), p('p3', '📢', 'Ayer se inauguro el nuevo parque...', 'noticia')]),
    rZona('Arrastra el fragmento hasta su tipo de texto', $ztt, [
        p('p1', '🧚', 'La hada concedio tres deseos...', 'cuento'), p('p2', '🍰', 'Mezcle el azucar con la mantequilla...', 'receta'), p('p3', '🌦️', 'Hoy se espera lluvia en la tarde...', 'noticia')]),
];

/* --------------------- GRADO 2 · Ciencias Naturales ------------------ */

// 52  ¿Se Dobla o Se Rompe?
$zf2 = [z('dobla', 'SE DOBLA'), z('rompe', 'SE ROMPE'), z('estira', 'SE ESTIRA')];
$RONDAS[52] = [
    rZona('Arrastra el material hasta cómo reacciona a la fuerza', $zf2, [
        p('p1', '🥤', 'pitillo de plastico', 'dobla'), p('p2', '🥚', 'cascara de huevo', 'rompe'), p('p3', '🎈', 'globo de caucho', 'estira')]),
    rZona('Arrastra el material hasta cómo reacciona a la fuerza', $zf2, [
        p('p1', '📄', 'hoja de papel', 'dobla'), p('p2', '🍪', 'galleta', 'rompe'), p('p3', '🩳', 'resorte de la ropa', 'estira')]),
];

// 53  Pon a Prueba los Materiales
$zr = [z('fuerte', 'FUERTE'), z('debil', 'DÉBIL')];
$RONDAS[53] = [
    rZona('Arrastra el material según su resistencia', $zr, [
        p('p1', '🧱', 'ladrillo', 'fuerte'), p('p2', '🧻', 'papel higienico', 'debil')]),
    rZona('Arrastra el material según su resistencia', $zr, [
        p('p1', '⛓️', 'cadena de metal', 'fuerte'), p('p2', '🫧', 'burbuja de jabon', 'debil')]),
];

// 54  Sólido, Líquido o Gas
$zes = [z('solido', 'SÓLIDO', '🧊'), z('liquido', 'LÍQUIDO', '💧'), z('gas', 'GAS', '💨')];
$RONDAS[54] = [
    rZona('Arrastra cada sustancia a su estado', $zes, [
        p('p1', '🪨', 'piedra', 'solido'), p('p2', '🥛', 'leche', 'liquido'), p('p3', '🎈', 'aire del globo', 'gas')]),
    rZona('Arrastra cada sustancia a su estado', $zes, [
        p('p1', '🍫', 'chocolate solido', 'solido'), p('p2', '🧃', 'jugo', 'liquido'), p('p3', '☁️', 'vapor', 'gas')]),
];

// 56  El Ciclo del Agua
$RONDAS[56] = [
    rOrden('Ordena las etapas del ciclo del agua', ['a', 'b', 'c'], [
        o('a', '💧', 'evaporacion'), o('b', '☁️', 'condensacion'), o('c', '🌧️', 'precipitacion')]),
    rOrden('Ordena qué pasa cuando llueve mucho', ['a', 'b', 'c'], [
        o('a', '🌧️', 'llueve fuerte'), o('b', '🏞️', 'se llenan los rios'), o('c', '🌊', 'el agua llega al mar')]),
];

// 57  ¿Dónde Vivo?
$zh = [z('agua', 'AGUA', '🌊'), z('tierra', 'TIERRA', '🌳'), z('aire', 'AIRE', '☁️')];
$RONDAS[57] = [
    rZona('Arrastra el animal hasta su hábitat', $zh, [
        p('p1', '🐬', 'delfin', 'agua'), p('p2', '🦁', 'leon', 'tierra'), p('p3', '🦅', 'aguila', 'aire')]),
    rZona('Arrastra el animal hasta su hábitat', $zh, [
        p('p1', '🐙', 'pulpo', 'agua'), p('p2', '🐘', 'elefante', 'tierra'), p('p3', '🦋', 'mariposa', 'aire')]),
];

// 59  Arma el Hábitat
$RONDAS[59] = [
    rZona('Arrastra lo que necesita el ave para vivir', [z('nido', 'NIDO', '🪺'), z('comida', 'COMIDA', '🐛')], [
        p('p1', '🌳', 'rama para el nido', 'nido'), p('p2', '🐛', 'gusano', 'comida')]),
    rZona('Arrastra lo que necesita la planta para vivir', [z('agua', 'AGUA', '💧'), z('luz', 'LUZ', '☀️')], [
        p('p1', '💧', 'agua de lluvia', 'agua'), p('p2', '☀️', 'luz del sol', 'luz')]),
];

// 60  El Ciclo de Vida
$RONDAS[60] = [
    rOrden('Ordena el ciclo de vida de la rana', ['a', 'b', 'c'], [
        o('a', '🥚', 'huevo'), o('b', '🐸', 'renacuajo'), o('c', '🐸', 'rana adulta')]),
    rOrden('Ordena el ciclo de vida de la gallina', ['a', 'b', 'c'], [
        o('a', '🥚', 'huevo'), o('b', '🐣', 'pollito'), o('c', '🐔', 'gallina')]),
];

// 62  Rompecabezas del Ciclo
$RONDAS[62] = [
    rOrden('Ordena el ciclo de vida del frijol', ['a', 'b', 'c'], [
        o('a', '🌰', 'semilla'), o('b', '🌱', 'brote'), o('c', '🌿', 'planta')]),
    rOrden('Ordena el ciclo de vida del pez', ['a', 'b', 'c'], [
        o('a', '🥚', 'huevo'), o('b', '🐠', 'pez pequeño'), o('c', '🐟', 'pez adulto')]),
];

/* --------------------- GRADO 3 · Lenguaje --------------------------- */

// 27  Texto, Sonido e Imagen
$zl = [z('verbal', 'LENGUAJE VERBAL', '💬'), z('noverbal', 'NO VERBAL', '🤚'), z('visual', 'VISUAL', '🖼️')];
$RONDAS[27] = [
    rZona('Arrastra el ejemplo hasta el tipo de lenguaje que usa', $zl, [
        p('p1', '📖', 'una carta escrita', 'verbal'), p('p2', '👋', 'un saludo con la mano', 'noverbal'), p('p3', '🚦', 'un semaforo', 'visual')]),
    rZona('Arrastra el ejemplo hasta el tipo de lenguaje que usa', $zl, [
        p('p1', '🎤', 'una cancion con letra', 'verbal'), p('p2', '😊', 'una sonrisa', 'noverbal'), p('p3', '🗺️', 'un mapa', 'visual')]),
];

// 29  Arma el Afiche
$za = [z('titulo', 'TÍTULO'), z('imagen', 'IMAGEN'), z('info', 'INFORMACIÓN')];
$RONDAS[29] = [
    rZona('Arrastra cada pieza a su lugar en el afiche', $za, [
        p('p1', '🔤', 'Feria del Libro', 'titulo'), p('p2', '📚', 'dibujo de libros', 'imagen'), p('p3', '📅', 'fecha y lugar', 'info')]),
    rZona('Arrastra cada pieza a su lugar en el afiche', $za, [
        p('p1', '🔤', 'Cuida el Agua', 'titulo'), p('p2', '💧', 'dibujo de una gota', 'imagen'), p('p3', '📅', 'consejos para ahorrar agua', 'info')]),
];

// 31  Inicio, Nudo y Desenlace
$RONDAS[31] = [
    rOrden('Ordena inicio, nudo y desenlace', ['a', 'b', 'c'], [
        o('a', '🏫', 'Sofia empezaba un nuevo colegio'), o('b', '😰', 'no conocia a nadie y se sintio triste'), o('c', '🤗', 'una companera la invito a jugar')]),
    rOrden('Ordena inicio, nudo y desenlace', ['a', 'b', 'c'], [
        o('a', '🐕', 'un perrito se perdio en el parque'), o('b', '😢', 'su familia lo busco todo el dia'), o('c', '🏡', 'un vecino lo encontro y lo devolvio')]),
];

// 32  El Detective del Cuento
$RONDAS[32] = [
    rOrden('Ordena el cuento de la tortuga y la liebre', ['a', 'b', 'c'], [
        o('a', '🏁', 'empezo la carrera'), o('b', '😴', 'la liebre se durmio confiada'), o('c', '🐢', 'la tortuga gano por constancia')]),
    rOrden('Ordena el cuento del patito feo', ['a', 'b', 'c'], [
        o('a', '🐣', 'nacio distinto a sus hermanos'), o('b', '😞', 'todos se burlaban de el'), o('c', '🦢', 'crecio y se convirtio en un hermoso cisne')]),
];

// 33  ¿Quién Habla y Quién Escucha?
$zeh = [z('emisor', 'EMISOR', '🗣️'), z('receptor', 'RECEPTOR', '👂')];
$RONDAS[33] = [
    rZona('Arrastra la situación hasta el rol correcto', $zeh, [
        p('p1', '👩‍🏫', 'la profesora explica la tarea', 'emisor'), p('p2', '🧑‍🎓', 'el estudiante escucha atento', 'receptor')]),
    rZona('Arrastra la situación hasta el rol correcto', $zeh, [
        p('p1', '📻', 'el locutor da la noticia', 'emisor'), p('p2', '👂', 'quien la oye por la radio', 'receptor')]),
];

// 35  Arma la Conversación
$RONDAS[35] = [
    rOrden('Ordena los turnos de la conversación', ['a', 'b'], [
        o('a', '❓', '¿Cómo te llamas?'), o('b', '🙋', 'Me llamo Valentina')]),
    rOrden('Ordena los turnos de la conversación', ['a', 'b', 'c'], [
        o('a', '👋', '¡Hola! ¿Cómo estás?'), o('b', '😊', 'Muy bien, ¿y tú?'), o('c', '🙂', 'También muy bien, gracias')]),
];

// 38  Completa la Idea
$RONDAS[38] = [
    rBlank('🏃 Carlos salió corriendo con la mochila puesta. Eso quiere decir que probablemente...',
        ['tarde', 'IBA TARDE'], [['paseo', 'IBA DE PASEO'], ['triste', 'ESTABA TRISTE']]),
    rBlank('🎣 El abuelo llevó su caña y un balde. Eso quiere decir que iba a...',
        ['pescar', 'PESCAR'], [['cocinar', 'COCINAR'], ['dormir', 'DORMIR']]),
];

/* --------------------- GRADO 3 · Ciencias Naturales ------------------ */

// 63  Deja Pasar la Luz
$zlu = [z('opaco', 'OPACO'), z('transparente', 'TRANSPARENTE'), z('translucido', 'TRANSLÚCIDO')];
$RONDAS[63] = [
    rZona('Arrastra el material hasta su comportamiento con la luz', $zlu, [
        p('p1', '🧱', 'ladrillo', 'opaco'), p('p2', '🥃', 'vaso de vidrio', 'transparente'), p('p3', '🧻', 'papel cebolla', 'translucido')]),
    rZona('Arrastra el material hasta su comportamiento con la luz', $zlu, [
        p('p1', '🪵', 'madera', 'opaco'), p('p2', '🪟', 'ventana limpia', 'transparente'), p('p3', '🥛', 'vidrio esmerilado', 'translucido')]),
];

// 65  Clasifica los Materiales
$RONDAS[65] = [
    rZona('Arrastra el material según cómo se comporta con la luz', $zlu, [
        p('p1', '🪑', 'madera de la silla', 'opaco'), p('p2', '💎', 'cristal', 'transparente'), p('p3', '🕶️', 'lente oscuro fino', 'translucido')]),
    rZona('Arrastra el material según cómo se comporta con la luz', $zlu, [
        p('p1', '🚪', 'puerta de metal', 'opaco'), p('p2', '🫙', 'frasco de vidrio', 'transparente'), p('p3', '⛺', 'tela fina de carpa', 'translucido')]),
];

// 67  Arma la Escena de Sombra
$RONDAS[67] = [
    rOrden('Ordena: fuente de luz, objeto y sombra', ['a', 'b', 'c'], [
        o('a', '💡', 'bombillo encendido'), o('b', '🧸', 'juguete'), o('c', '⬛', 'sombra proyectada')]),
    rOrden('Ordena: fuente de luz, objeto y sombra', ['a', 'b', 'c'], [
        o('a', '☀️', 'sol en el cielo'), o('b', '🌳', 'arbol'), o('c', '⬛', 'sombra del arbol')]),
];

// 69  Sonidos que Vibran
$zin = [z('cuerda', 'CUERDA'), z('aire', 'AIRE'), z('membrana', 'MEMBRANA')];
$RONDAS[69] = [
    rZona('Arrastra el instrumento hasta lo que vibra para sonar', $zin, [
        p('p1', '🎸', 'guitarra', 'cuerda'), p('p2', '🎺', 'trompeta', 'aire'), p('p3', '🥁', 'tambor', 'membrana')]),
    rZona('Arrastra el instrumento hasta lo que vibra para sonar', $zin, [
        p('p1', '🎻', 'violin', 'cuerda'), p('p2', '🎷', 'saxofon', 'aire'), p('p3', '🪘', 'timbal', 'membrana')]),
];

// 71  ¿Por Dónde Viaja el Sonido?
$zmed = [z('solido', 'SÓLIDO'), z('liquido', 'LÍQUIDO'), z('gaseoso', 'GASEOSO')];
$RONDAS[71] = [
    rZona('Arrastra el ejemplo hasta el medio por el que viaja el sonido', $zmed, [
        p('p1', '🚪', 'un golpe en la pared', 'solido'), p('p2', '🏊', 'un sonido bajo el agua', 'liquido'), p('p3', '🗣️', 'una voz en el aire', 'gaseoso')]),
    rZona('Arrastra el ejemplo hasta el medio por el que viaja el sonido', $zmed, [
        p('p1', '🚂', 'el sonido del tren en las vias', 'solido'), p('p2', '🐋', 'el canto de una ballena', 'liquido'), p('p3', '🔔', 'una campana al aire libre', 'gaseoso')]),
];

// 72  El Agua Cambia
$RONDAS[72] = [
    rOrden('Ordena de más frío a más caliente', ['a', 'b', 'c'], [
        o('a', '🧊', 'hielo'), o('b', '💧', 'agua tibia'), o('c', '♨️', 'agua hirviendo')]),
    rOrden('Ordena qué pasa al enfriar mucho el agua', ['a', 'b', 'c'], [
        o('a', '💧', 'agua liquida'), o('b', '❄️', 'agua muy fria'), o('c', '🧊', 'se congela')]),
];

// 74  Experimento del Agua
$RONDAS[74] = [
    rBlank('🍦 Un helado se quedó afuera al sol. ¿Qué le pasó?',
        ['derritio', 'SE DERRITIÓ'], [['congelo', 'SE CONGELÓ'], ['nada', 'NO PASÓ NADA']]),
    rBlank('🚗 El vidrio del carro amaneció con gotitas por fuera. ¿Qué pasó?',
        ['condenso', 'EL VAPOR DEL AIRE SE CONDENSÓ'], [['evaporo', 'EL VIDRIO SE EVAPORÓ'], ['congelo', 'EL VIDRIO SE CONGELÓ']]),
];

/* ================================================================== *
 *  PARES NUEVOS PARA MEMORIA (id 5 · Memo-Vocales)
 * ================================================================== */
$PARES_MEMORIA = [
    5 => [
        ['letra' => 'A', 'imagen_emoji' => '✈️', 'imagen_texto' => 'avion'],
        ['letra' => 'E', 'imagen_emoji' => '⭐', 'imagen_texto' => 'estrella'],
        ['letra' => 'I', 'imagen_emoji' => '🏝️', 'imagen_texto' => 'isla'],
        ['letra' => 'O', 'imagen_emoji' => '🐙', 'imagen_texto' => 'oveja'],
        ['letra' => 'U', 'imagen_emoji' => '🦄', 'imagen_texto' => 'unicornio'],
    ],
];

/* ================================================================== *
 *  PREGUNTAS NUEVAS PARA SELECCIONAR (2 por actividad, orden 7-8)
 * ================================================================== */
$PREGUNTAS = [];

// ---- Grado 1 · Lenguaje ----
$PREGUNTAS[4] = [ // Sonido y Figura
    q('🌙 ¿Qué imagen empieza con el mismo sonido que "luna"?', ['A' => '🌙 luna', 'B' => '🐘 elefante', 'C' => '🍇 uva'], 'A'),
    q('🐍 ¿Qué imagen empieza con el mismo sonido que "serpiente"?', ['A' => '🐻 oso', 'B' => '🐍 serpiente', 'C' => '🦎 iguana'], 'B'),
];
$PREGUNTAS[10] = [ // Lector Estrella
    q('📖 ¿Cuál palabra nombra a este dibujo? 🐱', ['A' => 'GATO', 'B' => 'PATO', 'C' => 'RATO'], 'A'),
    q('📖 ¿Cuál palabra nombra a este dibujo? ☀️', ['A' => 'SOL', 'B' => 'SAL', 'C' => 'SOPA'], 'A'),
];
$PREGUNTAS[11] = [ // Busca la Palabra Correcta
    q('🖍️ ¿Cuál de estas palabras está bien escrita?', ['A' => 'CAZA', 'B' => 'KASA', 'C' => 'CASA'], 'C'),
    q('🖍️ ¿Cuál de estas palabras está bien escrita?', ['A' => 'PESE', 'B' => 'PEZE', 'C' => 'PEZ'], 'C'),
];
$PREGUNTAS[14] = [ // Cuento Corto, Gran Aventura
    q('📚 En un cuento, ¿cómo suele empezar la historia?', ['A' => 'Había una vez...', 'B' => 'Colorín colorado...', 'C' => 'Fin'], 'A'),
    q('📚 ¿Qué palabra suele marcar el final de un cuento?', ['A' => 'Había una vez', 'B' => 'Colorín colorado, este cuento se ha acabado', 'C' => 'Capítulo uno'], 'B'),
];
$PREGUNTAS[16] = [ // ¿Qué Significa?
    q('🚸 ¿Qué significa esta señal? 🚸', ['A' => 'Zona escolar', 'B' => 'No fumar', 'C' => 'Silencio'], 'A'),
    q('♿ ¿Qué significa esta señal? ♿', ['A' => 'Acceso para todos', 'B' => 'Prohibido el paso', 'C' => 'Solo adultos'], 'A'),
];
$PREGUNTAS[19] = [ // El Escenario del Cuento
    q('🏰 En un cuento de princesas, ¿qué suele ser el escenario?', ['A' => 'Un castillo', 'B' => 'Una oficina', 'C' => 'Un hospital'], 'A'),
    q('🌊 En un cuento de piratas, ¿qué suele ser el escenario?', ['A' => 'El mar', 'B' => 'Una biblioteca', 'C' => 'Un salón de clase'], 'A'),
];
$PREGUNTAS[21] = [ // Cuento y Vivencia
    q('😢 Si un cuento habla de perder un juguete, ¿a qué se parece en la vida real?', ['A' => 'Perder algo que queremos mucho', 'B' => 'Ganar un premio', 'C' => 'Ir de vacaciones'], 'A'),
    q('🎂 Si el cuento habla de un cumpleaños, ¿qué se parece a tu vida?', ['A' => 'Celebrar con familia y amigos', 'B' => 'Ir al doctor', 'C' => 'Dormir todo el día'], 'A'),
];
$PREGUNTAS[22] = [ // ¿Te ha Pasado?
    q('🌧️ Si el personaje se moja en la lluvia sin paraguas, ¿te ha pasado algo así?', ['A' => 'Sí, mojarse en la lluvia', 'B' => 'No, nunca llueve', 'C' => 'Sí, volar en avión'], 'A'),
    q('🤝 Si el personaje comparte su almuerzo con un amigo, ¿se parece a algo que haces tú?', ['A' => 'Sí, compartir con amigos', 'B' => 'No, nunca comparto', 'C' => 'Ir a la luna'], 'A'),
];
$PREGUNTAS[24] = [ // ¿De Qué Tratará?
    q('🐺 Si el título es "El Lobo y las Ovejas", ¿de qué tratará?', ['A' => 'De un lobo y ovejas', 'B' => 'De un viaje al espacio', 'C' => 'De una receta de cocina'], 'A'),
    q('🌋 Si el título es "El Volcán Dormido", ¿de qué tratará?', ['A' => 'De un volcán', 'B' => 'De un cumpleaños', 'C' => 'De un zapato perdido'], 'A'),
];
$PREGUNTAS[26] = [ // Adivina el Final
    q('🐢🐇 La tortuga es constante y la liebre se confía. ¿Quién crees que gana la carrera?', ['A' => 'La tortuga', 'B' => 'La liebre', 'C' => 'Ninguna'], 'A'),
    q('🌱 Si el niño riega su planta todos los días con cariño, ¿qué crees que pasará?', ['A' => 'La planta crecerá sana', 'B' => 'La planta desaparecerá', 'C' => 'Nada cambiará'], 'A'),
];

// ---- Grado 1 · Ciencias Naturales ----
$PREGUNTAS[40] = [ // Caliente o Frío
    q('☕ Una taza de chocolate recién servida, ¿está caliente o fría?', ['A' => 'Caliente', 'B' => 'Fría'], 'A'),
    q('🍦 Un helado recién sacado del congelador, ¿está caliente o frío?', ['A' => 'Caliente', 'B' => 'Frío'], 'B'),
];
$PREGUNTAS[43] = [ // Fuerte o Flexible
    q('🪵 Una regla de madera dura, ¿es fuerte o flexible?', ['A' => 'Fuerte', 'B' => 'Flexible'], 'A'),
    q('🎗️ Una liga de caucho que se estira, ¿es fuerte o flexible?', ['A' => 'Fuerte', 'B' => 'Flexible'], 'B'),
];
$PREGUNTAS[45] = [ // ¿Vivo o No Vivo?
    q('🌵 Un cactus en una maceta, ¿es un ser vivo o no vivo?', ['A' => 'Vivo', 'B' => 'No vivo'], 'A'),
    q('🪑 Una silla de madera, ¿es un ser vivo o no vivo?', ['A' => 'Vivo', 'B' => 'No vivo'], 'B'),
];
$PREGUNTAS[50] = [ // Cambios en mi Cuerpo
    q('👶 Cuando un bebé crece, ¿qué le pasa a su cuerpo?', ['A' => 'Se hace más grande y fuerte', 'B' => 'Se hace más pequeño', 'C' => 'No cambia nunca'], 'A'),
    q('🦷 A medida que creces, ¿qué puede cambiar en tu boca?', ['A' => 'Salen dientes nuevos', 'B' => 'Desaparece la boca', 'C' => 'Nada cambia'], 'A'),
];

// ---- Grado 2 · Lenguaje ----
$PREGUNTAS[28] = [ // ¿Qué Lenguaje Usa?
    q('🎵 Una canción con letra, ¿qué tipo de lenguaje usa principalmente?', ['A' => 'Lenguaje verbal', 'B' => 'Solo gestos', 'C' => 'Solo colores'], 'A'),
    q('🚦 Un semáforo, ¿qué tipo de lenguaje usa?', ['A' => 'Lenguaje visual', 'B' => 'Lenguaje verbal escrito', 'C' => 'Ninguno'], 'A'),
];
$PREGUNTAS[30] = [ // ¿Quién Cuenta la Historia?
    q('📖 La voz que narra la historia en un cuento se llama...', ['A' => 'El narrador', 'B' => 'El ilustrador', 'C' => 'El editor'], 'A'),
    q('👤 Cuando el narrador dice "yo hice esto", ¿cómo se llama esa forma de narrar?', ['A' => 'Narrador en primera persona', 'B' => 'Narrador invisible', 'C' => 'No es narración'], 'A'),
];
$PREGUNTAS[34] = [ // ¿Para Qué lo Dice?
    q('🙏 Si alguien dice "por favor, pásame el agua", ¿para qué lo dice?', ['A' => 'Para pedir algo', 'B' => 'Para saludar', 'C' => 'Para despedirse'], 'A'),
    q('🎉 Si alguien dice "¡Feliz cumpleaños!", ¿para qué lo dice?', ['A' => 'Para felicitar', 'B' => 'Para pedir algo', 'C' => 'Para regañar'], 'A'),
];
$PREGUNTAS[36] = [ // Lectura entre Líneas
    q('🐾 Si el texto dice "las huellas mojadas llegaban hasta la cocina", ¿qué se puede deducir?', ['A' => 'Que alguien mojado pasó por ahí', 'B' => 'Que nadie entró', 'C' => 'Que llovió adentro'], 'A'),
    q('🧥 Si el texto dice "todos llegaron con paraguas y botas", ¿qué se puede deducir?', ['A' => 'Que estaba lloviendo', 'B' => 'Que hacía mucho calor', 'C' => 'Que era de noche'], 'A'),
];
$PREGUNTAS[37] = [ // ¿Qué Opinas?
    q('🤔 Si un personaje ayuda a otro sin esperar nada a cambio, ¿qué opinas de esa actitud?', ['A' => 'Es una buena actitud', 'B' => 'Es una mala actitud', 'C' => 'No importa'], 'A'),
    q('🗑️ Si un personaje bota basura en el parque, ¿qué opinas de esa actitud?', ['A' => 'No está bien', 'B' => 'Está muy bien', 'C' => 'No importa'], 'A'),
];

// ---- Grado 2 · Ciencias Naturales ----
$PREGUNTAS[51] = [ // Empuja y Jala
    q('🚪 Para abrir una puerta hacia ti, ¿qué acción haces?', ['A' => 'Jalar', 'B' => 'Empujar'], 'A'),
    q('🛒 Para mover un carrito de mercado hacia adelante, ¿qué acción haces?', ['A' => 'Empujar', 'B' => 'Jalar'], 'A'),
];
$PREGUNTAS[55] = [ // ¿Qué Estado Es?
    q('🧊 El hielo dentro de un vaso, ¿en qué estado está?', ['A' => 'Sólido', 'B' => 'Líquido', 'C' => 'Gaseoso'], 'A'),
    q('💨 El aire que soplas al inflar un globo, ¿en qué estado está?', ['A' => 'Gaseoso', 'B' => 'Sólido', 'C' => 'Líquido'], 'A'),
];
$PREGUNTAS[58] = [ // Lo Que Necesito para Vivir
    q('🐟 Para vivir, ¿qué necesita un pez?', ['A' => 'Agua', 'B' => 'Una cama', 'C' => 'Un libro'], 'A'),
    q('🌻 Para vivir, ¿qué necesita una planta?', ['A' => 'Luz del sol', 'B' => 'Una silla', 'C' => 'Zapatos'], 'A'),
];
$PREGUNTAS[61] = [ // ¿Qué Cambió?
    q('🐛🦋 De oruga a mariposa, ¿qué cambió?', ['A' => 'Su forma y su cuerpo', 'B' => 'Nada cambió', 'C' => 'Solo el color del cielo'], 'A'),
    q('🌰🌳 De semilla a árbol, ¿qué cambió?', ['A' => 'Creció y se hizo más grande', 'B' => 'Se hizo más pequeña', 'C' => 'Desapareció'], 'A'),
];

// ---- Grado 3 · Lenguaje ----
$PREGUNTAS[64] = [ // ¿Refleja o Absorbe?
    q('🪞 Un espejo frente a la luz, ¿qué hace con ella?', ['A' => 'La refleja', 'B' => 'La absorbe totalmente'], 'A'),
    q('⚫ Una tela negra frente al sol, ¿qué hace con la mayoría de la luz?', ['A' => 'La absorbe', 'B' => 'La refleja toda'], 'A'),
];
$PREGUNTAS[66] = [ // Grande o Pequeña
    q('🕯️ Si la luz está muy cerca de un objeto, ¿la sombra suele ser...?', ['A' => 'Más grande', 'B' => 'Más pequeña'], 'A'),
    q('☀️ Si la luz está muy lejos de un objeto, ¿la sombra suele ser...?', ['A' => 'Más pequeña', 'B' => 'Más grande'], 'A'),
];
$PREGUNTAS[68] = [ // ¿Qué Pasa con la Sombra?
    q('🕐 Al mediodía, cuando el sol está arriba, ¿cómo suele verse la sombra?', ['A' => 'Corta', 'B' => 'Muy larga'], 'A'),
    q('🌆 Al atardecer, cuando el sol está bajo, ¿cómo suele verse la sombra?', ['A' => 'Muy larga', 'B' => 'Muy corta'], 'A'),
];
$PREGUNTAS[70] = [ // Agudo o Grave
    q('🐦 El canto de un pajarito pequeño, ¿es agudo o grave?', ['A' => 'Agudo', 'B' => 'Grave'], 'A'),
    q('🦁 El rugido de un león, ¿es agudo o grave?', ['A' => 'Grave', 'B' => 'Agudo'], 'A'),
];
$PREGUNTAS[73] = [ // Sube o Baja la Temperatura
    q('🔥 Si acercas agua al fuego, ¿la temperatura sube o baja?', ['A' => 'Sube', 'B' => 'Baja'], 'A'),
    q('🧊 Si metes agua al congelador, ¿la temperatura sube o baja?', ['A' => 'Baja', 'B' => 'Sube'], 'A'),
];

/* ================================================================== *
 *  VOLCADO A SQL
 * ================================================================== */
$sql = "-- Segunda tanda de contenido: más rondas por juego, más\n"
     . "-- preguntas de Seleccionar, más pares de Memoria.\n"
     . "-- Generado por database/seeds/_build_mas_rondas_v2.php — no editar a mano.\n\n";

foreach ($RONDAS as $idActividad => $rondasNuevas) {
    $actual = $pdo->prepare("SELECT datos_json FROM contenido_juego WHERE id_actividad = ?");
    $actual->execute([$idActividad]);
    $fila = $actual->fetch(PDO::FETCH_ASSOC);

    if (!$fila) {
        fwrite(STDERR, "AVISO: actividad $idActividad no tiene contenido_juego, se omite.\n");
        continue;
    }

    $datos = json_decode($fila["datos_json"], true);
    $datos["rondas"] = array_merge($datos["rondas"] ?? [], $rondasNuevas);
    $jsonFinal = json_encode($datos, JSON_UNESCAPED_UNICODE);
    if (json_decode($jsonFinal) === null) {
        fwrite(STDERR, "ERROR: JSON inválido para la actividad $idActividad, se omite.\n");
        continue;
    }

    // $pdo->quote() (no un str_replace a mano) es lo que escapa bien
    // comillas Y barras invertidas para el driver real — un JSON con
    // comillas internas escapadas (\") se corrompía antes porque el
    // escapado manual solo cubría comillas simples, no la barra
    // invertida que json_encode ya había puesto ahí.
    $sql .= "UPDATE contenido_juego SET datos_json = " . $pdo->quote($jsonFinal) . " WHERE id_actividad = $idActividad;\n";
}

foreach ($PARES_MEMORIA as $idActividad => $paresNuevos) {
    $actual = $pdo->prepare("SELECT datos_json FROM contenido_juego WHERE id_actividad = ?");
    $actual->execute([$idActividad]);
    $fila = $actual->fetch(PDO::FETCH_ASSOC);
    if (!$fila) continue;

    $datos = json_decode($fila["datos_json"], true);
    $datos["pares"] = array_merge($datos["pares"] ?? [], $paresNuevos);
    $jsonFinal = json_encode($datos, JSON_UNESCAPED_UNICODE);
    if (json_decode($jsonFinal) === null) {
        fwrite(STDERR, "ERROR: JSON inválido para la actividad $idActividad, se omite.\n");
        continue;
    }

    $sql .= "UPDATE contenido_juego SET datos_json = " . $pdo->quote($jsonFinal) . " WHERE id_actividad = $idActividad;\n";
}

$sql .= "\n";

foreach ($PREGUNTAS as $idActividad => $preguntasNuevas) {
    $ordenActual = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) AS m FROM contenidoactividad WHERE id_actividad = ?");
    $ordenActual->execute([$idActividad]);
    $orden = (int) $ordenActual->fetch(PDO::FETCH_ASSOC)["m"];

    foreach ($preguntasNuevas as $preg) {
        $orden++;
        $vals = [
            $idActividad,
            $pdo->quote($preg["pregunta"]),
            $pdo->quote($preg["opcion_a"]),
            $preg["opcion_b"] !== null ? $pdo->quote($preg["opcion_b"]) : "NULL",
            $preg["opcion_c"] !== null ? $pdo->quote($preg["opcion_c"]) : "NULL",
            $preg["opcion_d"] !== null ? $pdo->quote($preg["opcion_d"]) : "NULL",
            $pdo->quote($preg["correcta"]),
            $orden,
        ];
        $sql .= "INSERT INTO contenidoactividad (id_actividad, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, orden)\n"
             . "  SELECT {$vals[0]}, {$vals[1]}, {$vals[2]}, {$vals[3]}, {$vals[4]}, {$vals[5]}, {$vals[6]}, {$vals[7]}\n"
             . "  FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM contenidoactividad WHERE id_actividad = {$vals[0]} AND orden = {$vals[7]});\n";
    }
}

$archivoSalida = __DIR__ . '/2026_08_31_mas_rondas_v2.sql';
file_put_contents($archivoSalida, $sql);

echo "Listo. Actividades con rondas nuevas: " . count($RONDAS) . "\n";
echo "Actividades con preguntas nuevas: " . count($PREGUNTAS) . "\n";
echo "Archivo generado: $archivoSalida\n";
