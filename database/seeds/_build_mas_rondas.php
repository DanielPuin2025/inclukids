<?php
/**
 * Generador del seed "más rondas en los juegos".
 *
 * Autoría del contenido nuevo en arrays PHP (emoji-safe) y volcado a
 *   database/seeds/2026_08_26_mas_rondas_juegos.sql
 * usando json_encode(), de modo que el JSON resultante SIEMPRE es válido.
 *
 * Uso:  php database/seeds/_build_mas_rondas.php
 * Luego cargar el .sql generado en la base de datos.
 *
 * Este archivo _build no forma parte del esquema: es solo la herramienta
 * que produjo el seed. Se puede borrar tras generar.
 */

require __DIR__ . '/../../app/config/database.php';

$pdo = (new Database())->conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* ------------------------------------------------------------------ *
 *  Helpers de autoría
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
// Ronda de clasificar/emparejar/arrastrar (zonas + piezas)
function rZona($instruccion, $zonas, $piezas) {
    return ['instruccion' => $instruccion, 'zonas' => $zonas, 'piezas' => $piezas];
}
// Ronda de ordenar/secuencia (orden_correcto + piezas)
function rOrden($instruccion, $ordenCorrecto, $piezas) {
    return ['instruccion' => $instruccion, 'orden_correcto' => $ordenCorrecto, 'piezas' => $piezas];
}
// Ronda de completar (hueco + banco)
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

/* ================================================================== *
 *  CONTENIDO NUEVO POR JUEGO  (contenido_juego.datos_json)
 *  clave = id_actividad ;  valor = rondas nuevas a AÑADIR al final.
 *  Para id 5 (memoria) el valor son "pares" nuevos.
 * ================================================================== */

$JUEGOS = [];

/* ---------- tipo 2 : Emparejar (imagen -> zona) ------------------- */

// 6  Caza-Consonantes  (L1-P2)  zonas M / P / L
$zc = [z('m', 'M'), z('p', 'P'), z('l', 'L')];
$JUEGOS[6] = [
    rZona('Arrastra cada imagen a su consonante inicial', $zc, [
        p('p1', '🍯', 'miel', 'm'), p('p2', '🦆', 'pato', 'p'), p('p3', '🌙', 'luna', 'l')]),
    rZona('Arrastra cada imagen a su consonante inicial', $zc, [
        p('p1', '🦋', 'mariposa', 'm'), p('p2', '🍐', 'pera', 'p'), p('p3', '🐺', 'lobo', 'l')]),
    rZona('Arrastra cada imagen a su consonante inicial', $zc, [
        p('p1', '🌽', 'maiz', 'm'), p('p2', '🍍', 'pina', 'p'), p('p3', '✏️', 'lapiz', 'l')]),
    rZona('Arrastra cada imagen a su consonante inicial', $zc, [
        p('p1', '🗺️', 'mapa', 'm'), p('p2', '🐧', 'pinguino', 'p'), p('p3', '🍋', 'limon', 'l')]),
];

// 9  Une la Palabra (L1-P3)  imagen -> su palabra (zonas cambian por ronda)
$JUEGOS[9] = [
    rZona('Arrastra cada imagen hasta su palabra',
        [z('perro', 'PERRO'), z('pato', 'PATO'), z('mano', 'MANO')], [
        p('p1', '🐶', 'perro', 'perro'), p('p2', '🦆', 'pato', 'pato'), p('p3', '✋', 'mano', 'mano')]),
    rZona('Arrastra cada imagen hasta su palabra',
        [z('oso', 'OSO'), z('pan', 'PAN'), z('pie', 'PIE')], [
        p('p1', '🐻', 'oso', 'oso'), p('p2', '🍞', 'pan', 'pan'), p('p3', '🦶', 'pie', 'pie')]),
    rZona('Arrastra cada imagen hasta su palabra',
        [z('rana', 'RANA'), z('ojo', 'OJO'), z('luz', 'LUZ')], [
        p('p1', '🐸', 'rana', 'rana'), p('p2', '👁️', 'ojo', 'ojo'), p('p3', '💡', 'luz', 'luz')]),
    rZona('Arrastra cada imagen hasta su palabra',
        [z('cama', 'CAMA'), z('vela', 'VELA'), z('rosa', 'ROSA')], [
        p('p1', '🛏️', 'cama', 'cama'), p('p2', '🕯️', 'vela', 'vela'), p('p3', '🌹', 'rosa', 'rosa')]),
];

// 15  Senales de mi Ciudad (L2-P1)  zonas DETENTE / HOSPITAL / PROHIBIDO
$zs = [z('alto', 'DETENTE', '🛑'), z('hospital', 'HOSPITAL', '🏥'), z('prohibido', 'PROHIBIDO', '🚫')];
$JUEGOS[15] = [
    rZona('Arrastra la senal hasta su significado', $zs, [
        p('p1', '✋', 'mano de pare', 'alto'), p('p2', '🚑', 'ambulancia', 'hospital'), p('p3', '🚷', 'no pasar', 'prohibido')]),
    rZona('Arrastra la senal hasta su significado', $zs, [
        p('p1', '🚦', 'semaforo en rojo', 'alto'), p('p2', '💊', 'farmacia', 'hospital'), p('p3', '📵', 'sin celular', 'prohibido')]),
    rZona('Arrastra la senal hasta su significado', $zs, [
        p('p1', '🔴', 'luz roja', 'alto'), p('p2', '🩺', 'atencion medica', 'hospital'), p('p3', '⛔', 'entrada prohibida', 'prohibido')]),
    rZona('Arrastra la senal hasta su significado', $zs, [
        p('p1', '🚸', 'ninos: detente', 'alto'), p('p2', '➕', 'cruz de salud', 'hospital'), p('p3', '🚭', 'no fumar', 'prohibido')]),
    rZona('Arrastra la senal hasta su significado', $zs, [
        p('p1', '🛑', 'senal de pare', 'alto'), p('p2', '🏥', 'edificio con cruz', 'hospital'), p('p3', '🚯', 'no botar basura', 'prohibido')]),
];

// 18  Quien es Quien (L2-P2)  VALIENTE / MALVADA
$zq = [z('principe', 'VALIENTE', '🤴'), z('bruja', 'MALVADA', '🧙')];
$JUEGOS[18] = [
    rZona('Arrastra la accion hasta el personaje', $zq, [
        p('p1', '🛡️', 'protegio a su pueblo', 'principe'), p('p2', '🕸️', 'atrapo a los ninos', 'bruja')]),
    rZona('Arrastra la accion hasta el personaje', $zq, [
        p('p1', '🐉', 'enfrento al dragon', 'principe'), p('p2', '🧪', 'preparo un veneno', 'bruja')]),
    rZona('Arrastra la accion hasta el personaje', $zq, [
        p('p1', '🤝', 'ayudo a los demas', 'principe'), p('p2', '😈', 'enganno a la princesa', 'bruja')]),
    rZona('Arrastra la accion hasta el personaje', $zq, [
        p('p1', '⚔️', 'lucho con valentia', 'principe'), p('p2', '🌑', 'lanzo un hechizo oscuro', 'bruja')]),
    rZona('Arrastra la accion hasta el personaje', $zq, [
        p('p1', '❤️', 'salvo a su amigo', 'principe'), p('p2', '🔒', 'encerro al rey', 'bruja')]),
];

// 25  Tipo de Texto (L2-P4)  RECETA / CARTA  (+ NOTICIA en algunas)
$JUEGOS[25] = [
    rZona('Arrastra el fragmento hasta su tipo de texto',
        [z('receta', 'RECETA', '🍳'), z('carta', 'CARTA', '✉️')], [
        p('p1', '🧂', 'agregue una pizca de sal', 'receta'), p('p2', '💌', 'espero verte pronto, con carino', 'carta')]),
    rZona('Arrastra el fragmento hasta su tipo de texto',
        [z('receta', 'RECETA', '🍳'), z('noticia', 'NOTICIA', '📰')], [
        p('p1', '🔥', 'hornee durante 20 minutos', 'receta'), p('p2', '🏙️', 'ayer se inauguro el nuevo parque', 'noticia')]),
    rZona('Arrastra el fragmento hasta su tipo de texto',
        [z('carta', 'CARTA', '✉️'), z('noticia', 'NOTICIA', '📰')], [
        p('p1', '✍️', 'querida abuela, te cuento que...', 'carta'), p('p2', '⚽', 'el equipo local gano el partido', 'noticia')]),
    rZona('Arrastra el fragmento hasta su tipo de texto',
        [z('receta', 'RECETA', '🍳'), z('cuento', 'CUENTO', '📖')], [
        p('p1', '🥚', 'bata los huevos en un tazon', 'receta'), p('p2', '🏰', 'habia una vez un reino lejano', 'cuento')]),
    rZona('Arrastra el fragmento hasta su tipo de texto',
        [z('carta', 'CARTA', '✉️'), z('cuento', 'CUENTO', '📖')], [
        p('p1', '📮', 'te escribo para invitarte a mi casa', 'carta'), p('p2', '🐉', 'el dragon vivia en la montana', 'cuento')]),
];

// 27  Texto, Sonido e Imagen (L3-P1)  TEXTO / SONIDO / IMAGEN
$zt = [z('texto', 'TEXTO', '📝'), z('sonido', 'SONIDO', '🔊'), z('imagen', 'IMAGEN', '🖼️')];
$JUEGOS[27] = [
    rZona('Arrastra el ejemplo hasta el tipo de lenguaje que usa', $zt, [
        p('p1', '📖', 'un cuento en un libro', 'texto'), p('p2', '📻', 'un programa de radio', 'sonido'), p('p3', '📷', 'una fotografia', 'imagen')]),
    rZona('Arrastra el ejemplo hasta el tipo de lenguaje que usa', $zt, [
        p('p1', '✉️', 'una carta escrita', 'texto'), p('p2', '🎧', 'una cancion', 'sonido'), p('p3', '🗺️', 'un mapa dibujado', 'imagen')]),
    rZona('Arrastra el ejemplo hasta el tipo de lenguaje que usa', $zt, [
        p('p1', '📃', 'una lista de tareas', 'texto'), p('p2', '🔔', 'el timbre del colegio', 'sonido'), p('p3', '🎨', 'una pintura', 'imagen')]),
    rZona('Arrastra el ejemplo hasta el tipo de lenguaje que usa', $zt, [
        p('p1', '📰', 'un titular de periodico', 'texto'), p('p2', '🐦', 'el canto de un pajaro', 'sonido'), p('p3', '🚸', 'una senal de trafico', 'imagen')]),
    rZona('Arrastra el ejemplo hasta el tipo de lenguaje que usa', $zt, [
        p('p1', '💬', 'un mensaje escrito', 'texto'), p('p2', '👏', 'un aplauso', 'sonido'), p('p3', '📊', 'un dibujo con colores', 'imagen')]),
];

// 33  Quien Habla y Quien Escucha (L3-P3)  EMISOR / RECEPTOR
$ze = [z('emisor', 'EMISOR', '🗣️'), z('receptor', 'RECEPTOR', '👂')];
$JUEGOS[33] = [
    rZona('Arrastra a quien habla y a quien escucha', $ze, [
        p('p1', '👩‍🏫', 'la maestra explica', 'emisor'), p('p2', '🧑‍🎓', 'el estudiante atiende', 'receptor')]),
    rZona('Arrastra a quien habla y a quien escucha', $ze, [
        p('p1', '📢', 'quien da un aviso', 'emisor'), p('p2', '👥', 'quien lo oye', 'receptor')]),
    rZona('Arrastra a quien habla y a quien escucha', $ze, [
        p('p1', '🎤', 'el cantante', 'emisor'), p('p2', '🙋', 'el publico', 'receptor')]),
    rZona('Arrastra a quien habla y a quien escucha', $ze, [
        p('p1', '📱', 'quien envia el mensaje', 'emisor'), p('p2', '📩', 'quien lo recibe', 'receptor')]),
    rZona('Arrastra a quien habla y a quien escucha', $ze, [
        p('p1', '📖', 'quien lee un cuento en voz alta', 'emisor'), p('p2', '🧒', 'quien lo escucha', 'receptor')]),
];

// 39  Mis Cinco Sentidos (CN1-P1)  VISTA / OIDO / GUSTO  (base) -> mantengo esas 3 zonas
$zsent = [z('ojo', 'VISTA', '👁️'), z('oreja', 'OIDO', '👂'), z('lengua', 'GUSTO', '👅')];
$JUEGOS[39] = [
    rZona('Arrastra lo que percibes con cada sentido', $zsent, [
        p('p1', '🖼️', 'un cuadro bonito', 'ojo'), p('p2', '🎶', 'una melodia', 'oreja'), p('p3', '🍋', 'el sabor acido', 'lengua')]),
    rZona('Arrastra lo que percibes con cada sentido', $zsent, [
        p('p1', '🌇', 'un atardecer', 'ojo'), p('p2', '🌊', 'las olas del mar', 'oreja'), p('p3', '🍫', 'el chocolate dulce', 'lengua')]),
    rZona('Arrastra lo que percibes con cada sentido', $zsent, [
        p('p1', '⭐', 'las estrellas', 'ojo'), p('p2', '🚨', 'una sirena', 'oreja'), p('p3', '🌶️', 'el picante', 'lengua')]),
    rZona('Arrastra lo que percibes con cada sentido', $zsent, [
        p('p1', '🌈', 'un arcoiris', 'ojo'), p('p2', '🐕', 'un perro ladrando', 'oreja'), p('p3', '🧂', 'la comida salada', 'lengua')]),
    rZona('Arrastra lo que percibes con cada sentido', $zsent, [
        p('p1', '📺', 'la television', 'ojo'), p('p2', '🎸', 'una guitarra', 'oreja'), p('p3', '🍬', 'un caramelo', 'lengua')]),
];

// 42  De Que Esta Hecho (CN1-P2)  MADERA / VIDRIO / TELA
$zm = [z('madera', 'MADERA', '🪵'), z('vidrio', 'VIDRIO', '🔷'), z('tela', 'TELA', '🧵')];
$JUEGOS[42] = [
    rZona('Arrastra cada objeto hasta su material', $zm, [
        p('p1', '🚪', 'una puerta', 'madera'), p('p2', '🍶', 'un frasco', 'vidrio'), p('p3', '🧣', 'una bufanda', 'tela')]),
    rZona('Arrastra cada objeto hasta su material', $zm, [
        p('p1', '🥄', 'una cuchara de palo', 'madera'), p('p2', '🥛', 'un vaso', 'vidrio'), p('p3', '👗', 'un vestido', 'tela')]),
    rZona('Arrastra cada objeto hasta su material', $zm, [
        p('p1', '🪑', 'un banco', 'madera'), p('p2', '🔮', 'una bola de cristal', 'vidrio'), p('p3', '🧦', 'unas medias', 'tela')]),
    rZona('Arrastra cada objeto hasta su material', $zm, [
        p('p1', '✏️', 'un lapiz', 'madera'), p('p2', '🪞', 'un espejo', 'vidrio'), p('p3', '🎒', 'una mochila', 'tela')]),
    rZona('Arrastra cada objeto hasta su material', $zm, [
        p('p1', '🛶', 'una canoa', 'madera'), p('p2', '💡', 'un bombillo', 'vidrio'), p('p3', '🏳️', 'una bandera', 'tela')]),
];

// 46  Caracteristicas de los Seres Vivos (CN1-P3)  SE ALIMENTA / RESPIRA / CRECE
$zv = [z('alimenta', 'SE ALIMENTA', '🍽️'), z('respira', 'RESPIRA', '💨'), z('crece', 'CRECE', '📏')];
$JUEGOS[46] = [
    rZona('Arrastra el ejemplo hasta su caracteristica', $zv, [
        p('p1', '🐰', 'el conejo come zanahoria', 'alimenta'), p('p2', '🐠', 'el pez toma oxigeno del agua', 'respira'), p('p3', '🐶', 'el cachorro se hace grande', 'crece')]),
    rZona('Arrastra el ejemplo hasta su caracteristica', $zv, [
        p('p1', '🐝', 'la abeja toma nectar', 'alimenta'), p('p2', '🌳', 'el arbol respira por las hojas', 'respira'), p('p3', '👶', 'el bebe crece cada ano', 'crece')]),
    rZona('Arrastra el ejemplo hasta su caracteristica', $zv, [
        p('p1', '🦁', 'el leon caza su comida', 'alimenta'), p('p2', '🐋', 'la ballena sube a respirar', 'respira'), p('p3', '🌻', 'la semilla crece y florece', 'crece')]),
    rZona('Arrastra el ejemplo hasta su caracteristica', $zv, [
        p('p1', '🐜', 'la hormiga junta alimento', 'alimenta'), p('p2', '🐸', 'la rana respira aire', 'respira'), p('p3', '🐛', 'la oruga crece y cambia', 'crece')]),
    rZona('Arrastra el ejemplo hasta su caracteristica', $zv, [
        p('p1', '🐿️', 'la ardilla come nueces', 'alimenta'), p('p2', '🌱', 'la planta toma aire', 'respira'), p('p3', '🐣', 'el pollito crece rapido', 'crece')]),
];

// 49  A Quien Me Parezco (CN1-P4)  MAMA / PAPA
$zp = [z('mama', 'MAMA', '👩'), z('papa', 'PAPA', '👨')];
$JUEGOS[49] = [
    rZona('Arrastra la caracteristica hasta quien la comparte', $zp, [
        p('p1', '👩‍🦱', 'cabello rizado', 'mama'), p('p2', '🧔', 'barba oscura', 'papa')]),
    rZona('Arrastra la caracteristica hasta quien la comparte', $zp, [
        p('p1', '😊', 'la misma sonrisa', 'mama'), p('p2', '👣', 'pies grandes', 'papa')]),
    rZona('Arrastra la caracteristica hasta quien la comparte', $zp, [
        p('p1', '👂', 'orejas pequenas', 'mama'), p('p2', '💪', 'brazos fuertes', 'papa')]),
    rZona('Arrastra la caracteristica hasta quien la comparte', $zp, [
        p('p1', '🎨', 'le gusta pintar', 'mama'), p('p2', '⚽', 'le gusta el futbol', 'papa')]),
    rZona('Arrastra la caracteristica hasta quien la comparte', $zp, [
        p('p1', '🟤', 'ojos color cafe', 'mama'), p('p2', '📏', 'estatura alta', 'papa')]),
];

// 52  Se Dobla o Se Rompe (CN2-P1)  SE DOBLA / SE ROMPE
$zd = [z('dobla', 'SE DOBLA', '〰️'), z('rompe', 'SE ROMPE', '💥')];
$JUEGOS[52] = [
    rZona('Arrastra el material segun como reacciona a la fuerza', $zd, [
        p('p1', '📄', 'una hoja de papel', 'dobla'), p('p2', '🥚', 'un huevo', 'rompe')]),
    rZona('Arrastra el material segun como reacciona a la fuerza', $zd, [
        p('p1', '🧵', 'un hilo', 'dobla'), p('p2', '🍪', 'una galleta', 'rompe')]),
    rZona('Arrastra el material segun como reacciona a la fuerza', $zd, [
        p('p1', '🌿', 'una rama verde', 'dobla'), p('p2', '🪵', 'una rama seca', 'rompe')]),
    rZona('Arrastra el material segun como reacciona a la fuerza', $zd, [
        p('p1', '🧽', 'una esponja', 'dobla'), p('p2', '🪟', 'un vidrio', 'rompe')]),
    rZona('Arrastra el material segun como reacciona a la fuerza', $zd, [
        p('p1', '🪱', 'un alambre delgado', 'dobla'), p('p2', '🏺', 'una vasija de barro', 'rompe')]),
];

// 57  Donde Vivo (CN2-P3)  EL MAR / LA SELVA / EL DESIERTO
$zh = [z('mar', 'EL MAR', '🌊'), z('selva', 'LA SELVA', '🌳'), z('desierto', 'EL DESIERTO', '🏜️')];
$JUEGOS[57] = [
    rZona('Arrastra el animal hasta su habitat', $zh, [
        p('p1', '🐙', 'pulpo', 'mar'), p('p2', '🦜', 'loro', 'selva'), p('p3', '🦂', 'escorpion', 'desierto')]),
    rZona('Arrastra el animal hasta su habitat', $zh, [
        p('p1', '🐠', 'pez de colores', 'mar'), p('p2', '🐍', 'serpiente', 'selva'), p('p3', '🦎', 'lagartija', 'desierto')]),
    rZona('Arrastra el animal hasta su habitat', $zh, [
        p('p1', '🦈', 'tiburon', 'mar'), p('p2', '🐆', 'jaguar', 'selva'), p('p3', '🐪', 'dromedario', 'desierto')]),
    rZona('Arrastra el animal hasta su habitat', $zh, [
        p('p1', '🐳', 'ballena', 'mar'), p('p2', '🦋', 'mariposa de selva', 'selva'), p('p3', '🦖', 'lagarto del desierto', 'desierto')]),
    rZona('Arrastra el animal hasta su habitat', $zh, [
        p('p1', '🦀', 'cangrejo', 'mar'), p('p2', '🦧', 'orangutan', 'selva'), p('p3', '🐜', 'hormiga del desierto', 'desierto')]),
];

// 63  Deja Pasar la Luz (CN3-P1)  TRANSPARENTE / OPACO / REFLECTIVO
$zl = [z('transparente', 'TRANSPARENTE', '🔍'), z('opaco', 'OPACO', '⬛'), z('reflectivo', 'REFLECTIVO', '✨')];
$JUEGOS[63] = [
    rZona('Arrastra el material hasta su comportamiento con la luz', $zl, [
        p('p1', '💧', 'agua limpia', 'transparente'), p('p2', '📕', 'un libro cerrado', 'opaco'), p('p3', '🥄', 'una cuchara de metal', 'reflectivo')]),
    rZona('Arrastra el material hasta su comportamiento con la luz', $zl, [
        p('p1', '🫧', 'una burbuja', 'transparente'), p('p2', '🚪', 'una puerta de madera', 'opaco'), p('p3', '💿', 'un disco brillante', 'reflectivo')]),
    rZona('Arrastra el material hasta su comportamiento con la luz', $zl, [
        p('p1', '🥛', 'un vaso de vidrio', 'transparente'), p('p2', '🪨', 'una piedra', 'opaco'), p('p3', '🪙', 'una moneda pulida', 'reflectivo')]),
    rZona('Arrastra el material hasta su comportamiento con la luz', $zl, [
        p('p1', '🧊', 'un cubo de hielo claro', 'transparente'), p('p2', '👕', 'una camiseta gruesa', 'opaco'), p('p3', '🔦', 'metal de una linterna', 'reflectivo')]),
    rZona('Arrastra el material hasta su comportamiento con la luz', $zl, [
        p('p1', '🪟', 'una ventana limpia', 'transparente'), p('p2', '🧱', 'un muro', 'opaco'), p('p3', '🪞', 'un espejo', 'reflectivo')]),
];

// 69  Sonidos que Vibran (CN3-P3)  CUERDA / MEMBRANA  (+ VIENTO)
$JUEGOS[69] = [
    rZona('Arrastra el instrumento hasta lo que vibra para sonar',
        [z('cuerda', 'CUERDA', '🎻'), z('piel', 'MEMBRANA', '🥁')], [
        p('p1', '🎸', 'guitarra', 'cuerda'), p('p2', '🪘', 'tambor de mano', 'piel')]),
    rZona('Arrastra el instrumento hasta lo que vibra para sonar',
        [z('cuerda', 'CUERDA', '🎻'), z('viento', 'AIRE', '💨')], [
        p('p1', '🎻', 'violin', 'cuerda'), p('p2', '🎺', 'trompeta', 'viento')]),
    rZona('Arrastra el instrumento hasta lo que vibra para sonar',
        [z('piel', 'MEMBRANA', '🥁'), z('viento', 'AIRE', '💨')], [
        p('p1', '🥁', 'bateria', 'piel'), p('p2', '🎷', 'saxofon', 'viento')]),
    rZona('Arrastra el instrumento hasta lo que vibra para sonar',
        [z('cuerda', 'CUERDA', '🎻'), z('piel', 'MEMBRANA', '🥁')], [
        p('p1', '🪕', 'banjo', 'cuerda'), p('p2', '👏', 'palmas en un cuero', 'piel')]),
    rZona('Arrastra el instrumento hasta lo que vibra para sonar',
        [z('cuerda', 'CUERDA', '🎻'), z('viento', 'AIRE', '💨')], [
        p('p1', '🎹', 'piano (cuerdas por dentro)', 'cuerda'), p('p2', '🪈', 'flauta', 'viento')]),
];

/* ---------- tipo 3 : Rompecabezas / secuencia -------------------- */

// 8  Rompecabezas Silabico (L1-P2)  ya tiene 3 -> +3
$JUEGOS[8] = [
    rOrden('Ordena las silabas para formar la palabra CASA', ['ca', 'sa'], [
        o('ca', null, 'CA'), o('sa', null, 'SA')]),
    rOrden('Ordena las silabas para formar la palabra ZAPATO', ['za', 'pa', 'to'], [
        o('za', null, 'ZA'), o('pa', null, 'PA'), o('to', null, 'TO')]),
    rOrden('Ordena las silabas para formar la palabra TOMATE', ['to', 'ma', 'te'], [
        o('to', null, 'TO'), o('ma', null, 'MA'), o('te', null, 'TE')]),
];

// 32  El Detective del Cuento (L3-P2)  estructura narrativa -> +5 variantes con historias
$JUEGOS[32] = [
    rOrden('Ordena el cuento del perrito perdido', ['a', 'b', 'c'], [
        o('a', '🐶', 'El perrito salio a pasear'), o('b', '😟', 'Se perdio en la ciudad'), o('c', '🏠', 'Su familia lo encontro')]),
    rOrden('Ordena el cuento de la semilla', ['a', 'b', 'c'], [
        o('a', '🌰', 'Un nino sembro una semilla'), o('b', '💧', 'La rego cada dia'), o('c', '🌳', 'Crecio un arbol grande')]),
    rOrden('Ordena el cuento del cumpleanos', ['a', 'b', 'c'], [
        o('a', '📩', 'Llego la invitacion'), o('b', '🎈', 'Prepararon la fiesta'), o('c', '🎂', 'Cantaron el feliz cumpleanos')]),
    rOrden('Ordena el cuento de la lluvia', ['a', 'b', 'c'], [
        o('a', '☀️', 'El dia estaba soleado'), o('b', '🌧️', 'Empezo a llover fuerte'), o('c', '🌈', 'Salio un arcoiris')]),
    rOrden('Ordena el cuento del barco', ['a', 'b', 'c'], [
        o('a', '⛵', 'El barco zarpo del puerto'), o('b', '🌊', 'Una tormenta lo sacudio'), o('c', '🏝️', 'Llego a una isla segura')]),
];

// 62  Rompecabezas del Ciclo (CN2-P4)  ciclo mariposa -> +5 ciclos
$JUEGOS[62] = [
    rOrden('Ordena el ciclo de vida de la rana', ['a', 'b', 'c', 'd'], [
        o('a', '🥚', 'huevo'), o('b', '🐟', 'renacuajo'), o('c', '🐸', 'rana joven'), o('d', '🐸', 'rana adulta')]),
    rOrden('Ordena el ciclo de vida de la gallina', ['a', 'b', 'c'], [
        o('a', '🥚', 'huevo'), o('b', '🐣', 'pollito'), o('c', '🐔', 'gallina')]),
    rOrden('Ordena el crecimiento del girasol', ['a', 'b', 'c', 'd'], [
        o('a', '🌰', 'semilla'), o('b', '🌱', 'brote'), o('c', '🌿', 'tallo con hojas'), o('d', '🌻', 'flor')]),
    rOrden('Ordena el ciclo de vida del perro', ['a', 'b', 'c'], [
        o('a', '🍼', 'cachorro recien nacido'), o('b', '🐕', 'perro joven'), o('c', '🐕‍🦺', 'perro adulto')]),
    rOrden('Ordena el crecimiento del arbol de manzanas', ['a', 'b', 'c', 'd'], [
        o('a', '🍎', 'semilla de la manzana'), o('b', '🌱', 'planta pequena'), o('c', '🌳', 'arbol'), o('d', '🍏', 'da manzanas')]),
];

/* ---------- tipo 5 : Ordenar / secuencia ------------------------- */

// 13  Mi Primera Oracion (L1-P4)  ya tiene 3 -> +3
$JUEGOS[13] = [
    rOrden('Ordena las palabras para armar la oracion', ['a', 'b', 'c'], [
        o('a', '👦', 'El nino'), o('b', '📖', 'lee'), o('c', '📕', 'un cuento')]),
    rOrden('Ordena las palabras para armar la oracion', ['a', 'b', 'c'], [
        o('a', '🐦', 'El pajaro'), o('b', '🕊️', 'vuela'), o('c', '☁️', 'muy alto')]),
    rOrden('Ordena las palabras para armar la oracion', ['a', 'b', 'c'], [
        o('a', '👧', 'La nina'), o('b', '🥤', 'toma'), o('c', '🧃', 'jugo')]),
];

// 23  Ordena la Historia (L2-P3)  inicio/problema/final -> +5 historias
$JUEGOS[23] = [
    rOrden('Ordena las escenas del cuento', ['a', 'b', 'c'], [
        o('a', '🎒', 'salio para el colegio'), o('b', '🌧️', 'lo sorprendio la lluvia'), o('c', '☂️', 'un amigo le presto un paraguas')]),
    rOrden('Ordena las escenas del cuento', ['a', 'b', 'c'], [
        o('a', '🐈', 'el gato jugaba en el jardin'), o('b', '🌳', 'se subio a un arbol y no bajaba'), o('c', '🪜', 'los bomberos lo bajaron')]),
    rOrden('Ordena las escenas del cuento', ['a', 'b', 'c'], [
        o('a', '🎂', 'era el cumpleanos de Ana'), o('b', '😢', 'se le cayo el pastel'), o('c', '🧁', 'sus amigos trajeron mas ponques')]),
    rOrden('Ordena las escenas del cuento', ['a', 'b', 'c'], [
        o('a', '⚽', 'empezo el partido'), o('b', '🤕', 'un jugador se lastimo'), o('c', '🏥', 'lo curaron y todo termino bien')]),
    rOrden('Ordena las escenas del cuento', ['a', 'b', 'c'], [
        o('a', '🚲', 'Pedro aprendia a montar en bici'), o('b', '💢', 'se cayo varias veces'), o('c', '🏆', 'al final logro andar solo')]),
];

// 31  Inicio, Nudo y Desenlace (L3-P2)  estructura -> +5 cuentos concretos
$JUEGOS[31] = [
    rOrden('Ordena inicio, nudo y desenlace', ['a', 'b', 'c'], [
        o('a', '🏠', 'una familia vivia tranquila'), o('b', '🔥', 'hubo un problema en el pueblo'), o('c', '🤝', 'todos ayudaron a resolverlo')]),
    rOrden('Ordena inicio, nudo y desenlace', ['a', 'b', 'c'], [
        o('a', '🧒', 'un nino tenia una mascota'), o('b', '🚪', 'la mascota se escapo'), o('c', '😊', 'la encontro sana y salva')]),
    rOrden('Ordena inicio, nudo y desenlace', ['a', 'b', 'c'], [
        o('a', '👵', 'la abuela horneaba pan'), o('b', '🐀', 'un raton se comio la masa'), o('c', '🍞', 'preparo otra y quedo rico')]),
    rOrden('Ordena inicio, nudo y desenlace', ['a', 'b', 'c'], [
        o('a', '🌊', 'unos amigos fueron a la playa'), o('b', '🪁', 'el viento se llevo la cometa'), o('c', '🏖️', 'la rescataron entre todos')]),
    rOrden('Ordena inicio, nudo y desenlace', ['a', 'b', 'c'], [
        o('a', '🎪', 'llego el circo al pueblo'), o('b', '🤡', 'un payaso perdio su nariz'), o('c', '🔴', 'un nino se la devolvio')]),
];

// 35  Arma la Conversacion (L3-P3)  saludo/pregunta/respuesta -> +5
$JUEGOS[35] = [
    rOrden('Ordena los turnos de la conversacion', ['a', 'b', 'c'], [
        o('a', '🙋', 'Buenos dias, senora'), o('b', '❓', 'Tiene pan fresco?'), o('c', '🥖', 'Si, acabo de hornearlo')]),
    rOrden('Ordena los turnos de la conversacion', ['a', 'b', 'c'], [
        o('a', '📞', 'Hola, con quien hablo?'), o('b', '🗣️', 'Soy Ana, esta Luis?'), o('c', '👦', 'Si, ya te lo paso')]),
    rOrden('Ordena los turnos de la conversacion', ['a', 'b', 'c'], [
        o('a', '👋', 'Hola, como te llamas?'), o('b', '😊', 'Me llamo Sofia'), o('c', '🤝', 'Mucho gusto, Sofia')]),
    rOrden('Ordena los turnos de la conversacion', ['a', 'b', 'c'], [
        o('a', '🙇', 'Disculpe, que hora es?'), o('b', '⏰', 'Son las tres'), o('c', '🙏', 'Gracias')]),
    rOrden('Ordena los turnos de la conversacion', ['a', 'b', 'c'], [
        o('a', '🚪', 'Toc toc, se puede?'), o('b', '✅', 'Claro, sigue'), o('c', '😀', 'Gracias, vengo por mi cuaderno')]),
];

// 48  Yo Antes y Ahora (CN1-P4)  edades -> +5
$JUEGOS[48] = [
    rOrden('Ordena de menor a mayor edad', ['a', 'b', 'c'], [
        o('a', '🍼', 'tomaba tetero'), o('b', '🧒', 'aprendio a caminar'), o('c', '🚴', 'anda en bicicleta')]),
    rOrden('Ordena de menor a mayor edad', ['a', 'b', 'c'], [
        o('a', '👶', 'gateaba'), o('b', '🧒', 'empezo el colegio'), o('c', '🧑', 'es adulto y trabaja')]),
    rOrden('Ordena de menor a mayor edad', ['a', 'b', 'c'], [
        o('a', '🦷', 'le salieron los primeros dientes'), o('b', '😬', 'se le cayo un diente de leche'), o('c', '😁', 'le salio el diente definitivo')]),
    rOrden('Ordena de menor a mayor edad', ['a', 'b', 'c'], [
        o('a', '📏', 'medi un metro'), o('b', '📐', 'creci diez centimetros'), o('c', '🧍', 'ahora soy mas alto')]),
    rOrden('Ordena de menor a mayor edad', ['a', 'b', 'c'], [
        o('a', '🗣️', 'decia solo palabras sueltas'), o('b', '💬', 'armaba frases cortas'), o('c', '📖', 'ahora leo cuentos')]),
];

// 56  El Ciclo del Agua (CN2-P2)  -> +5 (variantes y estados)
$JUEGOS[56] = [
    rOrden('Ordena el ciclo del agua', ['a', 'b', 'c', 'd'], [
        o('a', '🏞️', 'agua en el rio'), o('b', '☀️', 'el sol la calienta'), o('c', '☁️', 'sube y forma nubes'), o('d', '⛰️', 'la lluvia vuelve al rio')]),
    rOrden('Ordena que le pasa a un charco con sol', ['a', 'b', 'c'], [
        o('a', '💦', 'hay un charco'), o('b', '🌤️', 'el sol lo calienta'), o('c', '💨', 'el agua se evapora')]),
    rOrden('Ordena como se forma la lluvia', ['a', 'b', 'c'], [
        o('a', '💨', 'vapor de agua en el aire'), o('b', '☁️', 'se junta en una nube'), o('c', '🌧️', 'cae como lluvia')]),
    rOrden('Ordena de solido a gas', ['a', 'b', 'c'], [
        o('a', '🧊', 'hielo'), o('b', '💧', 'agua liquida'), o('c', '💨', 'vapor')]),
    rOrden('Ordena que pasa con la ropa mojada al sol', ['a', 'b', 'c'], [
        o('a', '👕', 'ropa mojada en la cuerda'), o('b', '☀️', 'el sol y el viento la calientan'), o('c', '✨', 'queda seca')]),
];

// 60  El Ciclo de Vida (CN2-P4)  planta -> +5
$JUEGOS[60] = [
    rOrden('Ordena el crecimiento del frijol', ['a', 'b', 'c', 'd'], [
        o('a', '🫘', 'semilla de frijol'), o('b', '💧', 'la semilla se remoja'), o('c', '🌱', 'sale la raiz y el brote'), o('d', '🌿', 'crece la planta')]),
    rOrden('Ordena el ciclo del tomate', ['a', 'b', 'c'], [
        o('a', '🌱', 'brote'), o('b', '🌼', 'flor'), o('c', '🍅', 'tomate')]),
    rOrden('Ordena la vida de la mariposa', ['a', 'b', 'c', 'd'], [
        o('a', '🥚', 'huevo'), o('b', '🐛', 'oruga'), o('c', '🛖', 'crisalida'), o('d', '🦋', 'mariposa')]),
    rOrden('Ordena el ciclo del maiz', ['a', 'b', 'c'], [
        o('a', '🌰', 'semilla'), o('b', '🌾', 'planta alta'), o('c', '🌽', 'mazorca')]),
    rOrden('Ordena la vida de un arbol', ['a', 'b', 'c', 'd'], [
        o('a', '🌰', 'semilla'), o('b', '🌱', 'plantula'), o('c', '🌳', 'arbol joven'), o('d', '🌲', 'arbol grande')]),
];

// 67  Arma la Escena de Sombra (CN3-P2)  luz/objeto/sombra -> +5
$JUEGOS[67] = [
    rOrden('Ordena: fuente de luz, objeto y sombra', ['a', 'b', 'c'], [
        o('a', '☀️', 'el sol'), o('b', '🌳', 'un arbol'), o('c', '⬛', 'la sombra en el suelo')]),
    rOrden('Ordena: fuente de luz, objeto y sombra', ['a', 'b', 'c'], [
        o('a', '💡', 'una lampara'), o('b', '🖐️', 'tu mano'), o('c', '⬛', 'la sombra en la pared')]),
    rOrden('Ordena: fuente de luz, objeto y sombra', ['a', 'b', 'c'], [
        o('a', '🔦', 'una linterna'), o('b', '🧸', 'un peluche'), o('c', '⬛', 'su sombra')]),
    rOrden('Ordena: fuente de luz, objeto y sombra', ['a', 'b', 'c'], [
        o('a', '🕯️', 'una vela'), o('b', '📚', 'un libro parado'), o('c', '⬛', 'la sombra detras')]),
    rOrden('Ordena: fuente de luz, objeto y sombra', ['a', 'b', 'c'], [
        o('a', '🌕', 'la luna brillante'), o('b', '🧍', 'una persona'), o('c', '⬛', 'la sombra larga')]),
];

// 72  El Agua Cambia (CN3-P4)  temperatura -> +5
$JUEGOS[72] = [
    rOrden('Ordena de mas frio a mas caliente', ['a', 'b', 'c'], [
        o('a', '🧊', 'agua congelada'), o('b', '🥤', 'agua fresca'), o('c', '♨️', 'agua muy caliente')]),
    rOrden('Ordena que pasa al calentar agua', ['a', 'b', 'c'], [
        o('a', '💧', 'agua en la olla'), o('b', '🔥', 'se calienta en el fuego'), o('c', '💨', 'sale vapor')]),
    rOrden('Ordena que pasa al enfriar agua', ['a', 'b', 'c'], [
        o('a', '💧', 'agua en un vaso'), o('b', '❄️', 'va al congelador'), o('c', '🧊', 'se vuelve hielo')]),
    rOrden('Ordena de solido a gas por el calor', ['a', 'b', 'c'], [
        o('a', '🧊', 'hielo'), o('b', '💧', 'se derrite'), o('c', '💨', 'se evapora')]),
    rOrden('Ordena que pasa con un helado afuera', ['a', 'b', 'c'], [
        o('a', '🍦', 'helado firme'), o('b', '🌡️', 'hace calor'), o('c', '💧', 'se derrite')]),
];

/* ---------- tipo 6 : Completar (hueco + banco) ------------------- */

// 12  Escribo mi Palabra (L1-P4)  ya tiene 2 -> +4
$JUEGOS[12] = [
    rBlank('🌙 Cual es la palabra de la imagen?', ['luna', 'LUNA'], [['sol', 'SOL'], ['mar', 'MAR']]),
    rBlank('🐟 Cual es la palabra de la imagen?', ['pez', 'PEZ'], [['pan', 'PAN'], ['pie', 'PIE']]),
    rBlank('🏠 Cual es la palabra de la imagen?', ['casa', 'CASA'], [['taza', 'TAZA'], ['masa', 'MASA']]),
    rBlank('🐱 Cual es la palabra de la imagen?', ['gato', 'GATO'], [['pato', 'PATO'], ['rato', 'RATO']]),
];

// 38  Completa la Idea (L3-P4)  inferencia -> +5
$JUEGOS[38] = [
    rBlank('😴 Pedro bostezo toda la clase. Eso quiere decir que probablemente...',
        ['durmio', 'DURMIO POCO'], [['comio', 'COMIO MUCHO'], ['corrio', 'CORRIO MUCHO']]),
    rBlank('🎒 Ana llego con la ropa mojada y un paraguas. Eso quiere decir que...',
        ['lluvia', 'ESTABA LLOVIENDO'], [['sol', 'HACIA SOL'], ['nieve', 'NEVABA']]),
    rBlank('🍽️ Luis se comio dos platos y pidio mas. Eso quiere decir que...',
        ['hambre', 'TENIA HAMBRE'], [['lleno', 'ESTABA LLENO'], ['enfermo', 'ESTABA ENFERMO']]),
    rBlank('🎁 Todos traian regalos y habia globos. Eso quiere decir que era...',
        ['fiesta', 'UNA FIESTA'], [['examen', 'UN EXAMEN'], ['clase', 'UNA CLASE NORMAL']]),
    rBlank('🥶 Juan se puso guantes, gorro y bufanda. Eso quiere decir que afuera...',
        ['frio', 'HACIA FRIO'], [['calor', 'HACIA CALOR'], ['viento', 'SOLO HABIA POLVO']]),
];

// 74  Experimento del Agua (CN3-P4)  cambios de estado -> +5
$JUEGOS[74] = [
    rBlank('💧❄️ El agua se metio al congelador. Que paso?',
        ['congelo', 'SE CONGELO'], [['evaporo', 'SE EVAPORO'], ['desaparecio', 'DESAPARECIO']]),
    rBlank('♨️ El agua hirvio en la olla. Que salio de ella?',
        ['vapor', 'VAPOR'], [['hielo', 'HIELO'], ['humo negro', 'HUMO NEGRO']]),
    rBlank('🧊☀️ Dejamos un cubo de hielo al sol. En que se convirtio?',
        ['agua', 'EN AGUA'], [['piedra', 'EN PIEDRA'], ['aire', 'EN NADA']]),
    rBlank('🪟 En la ventana fria aparecieron gotitas. El vapor del aire se...',
        ['condenso', 'VOLVIO LIQUIDO'], [['congelo', 'VOLVIO HIELO'], ['calento', 'CALENTO MAS']]),
    rBlank('🍫 Un chocolate se dejo al sol. Que le paso?',
        ['derritio', 'SE DERRITIO'], [['endurecio', 'SE PUSO MAS DURO'], ['crecio', 'CRECIO']]),
];

/* ---------- tipo 7 : Arrastrar / clasificar --------------------- */

// 3  El Jardin de las Vocales (L1-P1)  ya tiene 5 (A E I O U) -> +1 repaso mixto
$JUEGOS[3] = [
    rZona('Arrastra cada animal a su vocal inicial',
        [z('a', 'A', '🌸'), z('e', 'E', '🌸'), z('i', 'I', '🌸')], [
        p('a', '🕷️', 'arana', 'a'), p('e', '🐘', 'elefante', 'e'), p('i', '🦎', 'iguana', 'i')]),
];

// 7  Constructor de Silabas (L1-P2)  ya tiene 6 -> +0
// (no se agrega)

// 17  Clasifica las Senales (L2-P1)  SEGURIDAD / INFORMACION / PROHIBICION
$zse = [z('seguridad', 'SEGURIDAD', '🦺'), z('informacion', 'INFORMACION', 'ℹ️'), z('prohibicion', 'PROHIBICION', '🚫')];
$JUEGOS[17] = [
    rZona('Arrastra la senal a su categoria', $zse, [
        p('p1', '🚪', 'salida de emergencia', 'seguridad'), p('p2', '🅿️', 'zona de parqueo', 'informacion'), p('p3', '📵', 'no usar celular', 'prohibicion')]),
    rZona('Arrastra la senal a su categoria', $zse, [
        p('p1', '⛑️', 'usar casco', 'seguridad'), p('p2', '🚻', 'banos', 'informacion'), p('p3', '🚷', 'no pasar', 'prohibicion')]),
    rZona('Arrastra la senal a su categoria', $zse, [
        p('p1', '🧯', 'extintor aqui', 'seguridad'), p('p2', '🍴', 'cafeteria', 'informacion'), p('p3', '🚭', 'no fumar', 'prohibicion')]),
    rZona('Arrastra la senal a su categoria', $zse, [
        p('p1', '🥾', 'usar botas', 'seguridad'), p('p2', '🚌', 'parada de bus', 'informacion'), p('p3', '⛔', 'entrada prohibida', 'prohibicion')]),
    rZona('Arrastra la senal a su categoria', $zse, [
        p('p1', '👷', 'zona de obra: cuidado', 'seguridad'), p('p2', '📶', 'aqui hay internet', 'informacion'), p('p3', '🚯', 'no botar basura', 'prohibicion')]),
];

// 20  Arma la Escena (L2-P2)  PERSONAJE / ESPACIO / ACCION
$zae = [z('personaje', 'PERSONAJE', '🧑'), z('espacio', 'ESPACIO', '🏞️'), z('accion', 'ACCION', '🏃')];
$JUEGOS[20] = [
    rZona('Arrastra cada elemento a su lugar en la escena', $zae, [
        p('p1', '👦', 'un nino', 'personaje'), p('p2', '🏫', 'la escuela', 'espacio'), p('p3', '📖', 'lee', 'accion')]),
    rZona('Arrastra cada elemento a su lugar en la escena', $zae, [
        p('p1', '👵', 'la abuela', 'personaje'), p('p2', '🍳', 'la cocina', 'espacio'), p('p3', '🥘', 'cocina', 'accion')]),
    rZona('Arrastra cada elemento a su lugar en la escena', $zae, [
        p('p1', '🐶', 'un perro', 'personaje'), p('p2', '🏖️', 'la playa', 'espacio'), p('p3', '🏊', 'nada', 'accion')]),
    rZona('Arrastra cada elemento a su lugar en la escena', $zae, [
        p('p1', '👨‍🚀', 'un astronauta', 'personaje'), p('p2', '🌙', 'la luna', 'espacio'), p('p3', '🦘', 'salta', 'accion')]),
    rZona('Arrastra cada elemento a su lugar en la escena', $zae, [
        p('p1', '👧', 'una nina', 'personaje'), p('p2', '🌲', 'el bosque', 'espacio'), p('p3', '🚶', 'camina', 'accion')]),
];

// 29  Arma el Afiche (L3-P1)  TITULO / IMAGEN / SONIDO
$zaf = [z('titulo', 'TITULO', '🔤'), z('imagen', 'IMAGEN', '🖼️'), z('sonido', 'SONIDO', '🔊')];
$JUEGOS[29] = [
    rZona('Arrastra cada pieza a su lugar en el afiche', $zaf, [
        p('p1', '📛', 'la frase grande de arriba', 'titulo'), p('p2', '📷', 'una foto del evento', 'imagen'), p('p3', '🎵', 'codigo para oir la cancion', 'sonido')]),
    rZona('Arrastra cada pieza a su lugar en el afiche', $zaf, [
        p('p1', '✍️', 'el nombre de la obra', 'titulo'), p('p2', '🎨', 'un dibujo llamativo', 'imagen'), p('p3', '🔈', 'boton de audio', 'sonido')]),
    rZona('Arrastra cada pieza a su lugar en el afiche', $zaf, [
        p('p1', '🆎', 'palabras destacadas', 'titulo'), p('p2', '🖼️', 'la ilustracion central', 'imagen'), p('p3', '🎙️', 'una nota de voz', 'sonido')]),
    rZona('Arrastra cada pieza a su lugar en el afiche', $zaf, [
        p('p1', '📢', 'el mensaje principal', 'titulo'), p('p2', '📸', 'imagen del lugar', 'imagen'), p('p3', '🔊', 'sonido de fondo', 'sonido')]),
    rZona('Arrastra cada pieza a su lugar en el afiche', $zaf, [
        p('p1', '🔠', 'letras grandes y claras', 'titulo'), p('p2', '🌄', 'una foto de paisaje', 'imagen'), p('p3', '🎶', 'musica del anuncio', 'sonido')]),
];

// 41  El Detective de Texturas (CN1-P1)  SUAVE / ASPERO
$ztx = [z('suave', 'SUAVE', '☁️'), z('aspero', 'ASPERO', '🪨')];
$JUEGOS[41] = [
    rZona('Arrastra el objeto segun su textura', $ztx, [
        p('p1', '🧶', 'lana', 'suave'), p('p2', '🌰', 'cascara de nuez', 'aspero')]),
    rZona('Arrastra el objeto segun su textura', $ztx, [
        p('p1', '🪶', 'una pluma', 'suave'), p('p2', '🧱', 'un ladrillo', 'aspero')]),
    rZona('Arrastra el objeto segun su textura', $ztx, [
        p('p1', '🛏️', 'una almohada', 'suave'), p('p2', '🪵', 'corteza de arbol', 'aspero')]),
    rZona('Arrastra el objeto segun su textura', $ztx, [
        p('p1', '🧴', 'crema', 'suave'), p('p2', '🧽', 'lija', 'aspero')]),
    rZona('Arrastra el objeto segun su textura', $ztx, [
        p('p1', '🐰', 'pelo de conejo', 'suave'), p('p2', '🐊', 'piel de cocodrilo', 'aspero')]),
];

// 44  Construye con Materiales (CN1-P2)  usos -> zonas cambian por ronda
$JUEGOS[44] = [
    rZona('Arrastra el material mas adecuado para cada uso',
        [z('ventana', 'VENTANA', '🪟'), z('llanta', 'LLANTA', '🛞')], [
        p('p1', '🔷', 'vidrio', 'ventana'), p('p2', '⚫', 'caucho', 'llanta')]),
    rZona('Arrastra el material mas adecuado para cada uso',
        [z('libro', 'LIBRO', '📗'), z('cuchara', 'CUCHARA', '🥄')], [
        p('p1', '📄', 'papel', 'libro'), p('p2', '🥈', 'metal', 'cuchara')]),
    rZona('Arrastra el material mas adecuado para cada uso',
        [z('abrigo', 'ABRIGO', '🧥'), z('mesa', 'MESA', '🪑')], [
        p('p1', '🧶', 'lana', 'abrigo'), p('p2', '🪵', 'madera', 'mesa')]),
    rZona('Arrastra el material mas adecuado para cada uso',
        [z('botella', 'BOTELLA', '🍾'), z('paraguas', 'PARAGUAS', '☂️')], [
        p('p1', '🥃', 'vidrio', 'botella'), p('p2', '🧵', 'tela impermeable', 'paraguas')]),
    rZona('Arrastra el material mas adecuado para cada uso',
        [z('globo', 'GLOBO', '🎈'), z('clavo', 'CLAVO', '📌')], [
        p('p1', '🎈', 'goma delgada', 'globo'), p('p2', '⚙️', 'metal', 'clavo')]),
];

// 47  Clasifica: Planta, Animal u Objeto (CN1-P3)
$zpo = [z('planta', 'PLANTA', '🌿'), z('animal', 'ANIMAL', '🐾'), z('objeto', 'OBJETO', '📦')];
$JUEGOS[47] = [
    rZona('Arrastra cada elemento a su grupo', $zpo, [
        p('p1', '🌵', 'cactus', 'planta'), p('p2', '🐈', 'gato', 'animal'), p('p3', '⚽', 'pelota', 'objeto')]),
    rZona('Arrastra cada elemento a su grupo', $zpo, [
        p('p1', '🌳', 'arbol', 'planta'), p('p2', '🐦', 'pajaro', 'animal'), p('p3', '📚', 'libro', 'objeto')]),
    rZona('Arrastra cada elemento a su grupo', $zpo, [
        p('p1', '🌷', 'tulipan', 'planta'), p('p2', '🐢', 'tortuga', 'animal'), p('p3', '🚗', 'carro', 'objeto')]),
    rZona('Arrastra cada elemento a su grupo', $zpo, [
        p('p1', '🍄', 'helecho', 'planta'), p('p2', '🐝', 'abeja', 'animal'), p('p3', '⏰', 'reloj', 'objeto')]),
    rZona('Arrastra cada elemento a su grupo', $zpo, [
        p('p1', '🌴', 'palmera', 'planta'), p('p2', '🐠', 'pez', 'animal'), p('p3', '🪑', 'silla', 'objeto')]),
];

// 53  Pon a Prueba los Materiales (CN2-P1)  FLEXIBLE / RIGIDO
$zfr = [z('flexible', 'FLEXIBLE', '〰️'), z('rigido', 'RIGIDO', '🧱')];
$JUEGOS[53] = [
    rZona('Arrastra el material segun su resistencia', $zfr, [
        p('p1', '🧵', 'un cordon', 'flexible'), p('p2', '🪵', 'un tablon', 'rigido')]),
    rZona('Arrastra el material segun su resistencia', $zfr, [
        p('p1', '🎈', 'una liga', 'flexible'), p('p2', '🪨', 'una roca', 'rigido')]),
    rZona('Arrastra el material segun su resistencia', $zfr, [
        p('p1', '📃', 'una hoja de papel', 'flexible'), p('p2', '🧊', 'un bloque de hielo', 'rigido')]),
    rZona('Arrastra el material segun su resistencia', $zfr, [
        p('p1', '🧦', 'una media', 'flexible'), p('p2', '🥄', 'una cuchara de metal', 'rigido')]),
    rZona('Arrastra el material segun su resistencia', $zfr, [
        p('p1', '🪱', 'un alambre fino', 'flexible'), p('p2', '🏺', 'una vasija de barro', 'rigido')]),
];

// 54  Solido, Liquido o Gas (CN2-P2)
$zslg = [z('solido', 'SOLIDO', '🧊'), z('liquido', 'LIQUIDO', '💧'), z('gas', 'GAS', '💨')];
$JUEGOS[54] = [
    rZona('Arrastra cada sustancia a su estado', $zslg, [
        p('p1', '🍎', 'una manzana', 'solido'), p('p2', '🧃', 'jugo', 'liquido'), p('p3', '🎈', 'aire del globo', 'gas')]),
    rZona('Arrastra cada sustancia a su estado', $zslg, [
        p('p1', '🔑', 'una llave', 'solido'), p('p2', '☕', 'cafe', 'liquido'), p('p3', '💨', 'vapor de la ducha', 'gas')]),
    rZona('Arrastra cada sustancia a su estado', $zslg, [
        p('p1', '🧱', 'un ladrillo', 'solido'), p('p2', '🥛', 'leche', 'liquido'), p('p3', '🌬️', 'el viento', 'gas')]),
    rZona('Arrastra cada sustancia a su estado', $zslg, [
        p('p1', '✏️', 'un lapiz', 'solido'), p('p2', '🌊', 'agua de mar', 'liquido'), p('p3', '🫧', 'burbujas de gas', 'gas')]),
    rZona('Arrastra cada sustancia a su estado', $zslg, [
        p('p1', '🪙', 'una moneda', 'solido'), p('p2', '🍯', 'miel', 'liquido'), p('p3', '🎈', 'helio del globo', 'gas')]),
];

// 59  Arma el Habitat (CN2-P3)  que necesita cada animal -> zonas por ronda + distractores
$JUEGOS[59] = [
    rZona('Arrastra lo que necesita el pajaro para vivir',
        [z('aire', 'AIRE', '💨'), z('nido', 'NIDO', '🪺')], [
        p('p1', '💨', 'aire', 'aire'), p('p2', '🪺', 'un nido', 'nido'), p('p3', '🧊', 'hielo', null)]),
    rZona('Arrastra lo que necesita la planta para vivir',
        [z('luz', 'LUZ', '☀️'), z('agua', 'AGUA', '💧')], [
        p('p1', '☀️', 'luz del sol', 'luz'), p('p2', '💧', 'agua', 'agua'), p('p3', '🌑', 'oscuridad total', null)]),
    rZona('Arrastra lo que necesita el perro para vivir',
        [z('comida', 'COMIDA', '🍖'), z('agua', 'AGUA', '💧')], [
        p('p1', '🍖', 'comida', 'comida'), p('p2', '💧', 'agua', 'agua'), p('p3', '🔥', 'fuego', null)]),
    rZona('Arrastra lo que necesita el camello en el desierto',
        [z('agua', 'AGUA', '💧'), z('sombra', 'SOMBRA', '🌴')], [
        p('p1', '💧', 'agua para varios dias', 'agua'), p('p2', '🌴', 'sombra de una palmera', 'sombra'), p('p3', '❄️', 'nieve', null)]),
    rZona('Arrastra lo que necesita el pez para vivir',
        [z('agua', 'AGUA', '💧'), z('oxigeno', 'OXIGENO', '🫧')], [
        p('p1', '💧', 'agua limpia', 'agua'), p('p2', '🫧', 'oxigeno en el agua', 'oxigeno'), p('p3', '🏜️', 'arena seca', null)]),
];

// 65  Clasifica los Materiales (CN3-P1)  OPACO / TRANSLUCIDO  (+ TRANSPARENTE)
$JUEGOS[65] = [
    rZona('Arrastra el material segun como se comporta con la luz',
        [z('opaco', 'OPACO', '⬛'), z('translucido', 'TRANSLUCIDO', '📄')], [
        p('p1', '🪵', 'tabla de madera', 'opaco'), p('p2', '🧻', 'papel delgado', 'translucido')]),
    rZona('Arrastra el material segun como se comporta con la luz',
        [z('opaco', 'OPACO', '⬛'), z('transparente', 'TRANSPARENTE', '🔍')], [
        p('p1', '📕', 'un libro', 'opaco'), p('p2', '🥛', 'un vaso de vidrio', 'transparente')]),
    rZona('Arrastra el material segun como se comporta con la luz',
        [z('translucido', 'TRANSLUCIDO', '📄'), z('transparente', 'TRANSPARENTE', '🔍')], [
        p('p1', '🥢', 'papel de calcar', 'translucido'), p('p2', '💧', 'agua limpia', 'transparente')]),
    rZona('Arrastra el material segun como se comporta con la luz',
        [z('opaco', 'OPACO', '⬛'), z('translucido', 'TRANSLUCIDO', '📄')], [
        p('p1', '🧱', 'un muro', 'opaco'), p('p2', '🏮', 'papel de una lampara', 'translucido')]),
    rZona('Arrastra el material segun como se comporta con la luz',
        [z('opaco', 'OPACO', '⬛'), z('transparente', 'TRANSPARENTE', '🔍')], [
        p('p1', '👞', 'un zapato', 'opaco'), p('p2', '🫙', 'un frasco vacio', 'transparente')]),
];

// 71  Por Donde Viaja el Sonido (CN3-P3)  SOLIDO / LIQUIDO / GASEOSO
$zso = [z('solido', 'SOLIDO', '🧱'), z('liquido', 'LIQUIDO', '💧'), z('gas', 'GASEOSO', '💨')];
$JUEGOS[71] = [
    rZona('Arrastra el ejemplo hasta el medio por el que viaja el sonido', $zso, [
        p('p1', '🚂', 'oreja pegada al riel del tren', 'solido'), p('p2', '🐋', 'el canto de las ballenas', 'liquido'), p('p3', '📣', 'un grito al aire libre', 'gas')]),
    rZona('Arrastra el ejemplo hasta el medio por el que viaja el sonido', $zso, [
        p('p1', '🪵', 'golpear una mesa', 'solido'), p('p2', '🏊', 'escuchar dentro de la piscina', 'liquido'), p('p3', '🎤', 'hablar por un microfono', 'gas')]),
    rZona('Arrastra el ejemplo hasta el medio por el que viaja el sonido', $zso, [
        p('p1', '📞', 'telefono de dos vasos y una cuerda', 'solido'), p('p2', '🛁', 'burbujas sonando en la tina', 'liquido'), p('p3', '🔔', 'una campana en la torre', 'gas')]),
    rZona('Arrastra el ejemplo hasta el medio por el que viaja el sonido', $zso, [
        p('p1', '🚪', 'tocar la puerta', 'solido'), p('p2', '🐟', 'un pez que oye pasos en la orilla', 'liquido'), p('p3', '🗣️', 'una conversacion', 'gas')]),
    rZona('Arrastra el ejemplo hasta el medio por el que viaja el sonido', $zso, [
        p('p1', '⛏️', 'golpes en una tuberia de metal', 'solido'), p('p2', '🌊', 'el sonido de un barco bajo el agua', 'liquido'), p('p3', '🎺', 'una trompeta en el parque', 'gas')]),
];

/* ---------- tipo 1 : Memoria (id 5) ----------------------------- *
 * "Memo-Vocales" trabaja las 5 vocales: 5 pares es el maximo natural
 * (repetir "letra" crearia cartas ambiguas). Se deja como esta.       */

/* ================================================================== *
 *  PREGUNTAS NUEVAS DE "SELECCIONAR"  (contenidoactividad)
 *  clave = id_actividad ; valor = [ [pregunta,a,b,c,d,ok], ... ]
 *  Se anaden como orden 3,4,5,6.  d = null si no aplica.
 * ================================================================== */

$QUIZ = [];

// 4  Sonido y Figura (L1-P1)
$QUIZ[4] = [
    ['🌙 Que imagen empieza con el mismo sonido que "mano"?', '🌙 luna', '🐘 elefante', '🍇 uva', null, 'A'],
    ['🐟 Que imagen empieza con el mismo sonido que "pez"?', '🐻 oso', '🐧 pinguino', '🕷️ arana', null, 'B'],
    ['🍋 Que imagen empieza con el mismo sonido que "leon"?', '🦎 iguana', '🐘 elefante', '🍋 limon', null, 'C'],
    ['🐱 Que imagen empieza con el mismo sonido que "gato"?', '🐐 gallina', '🐻 oso', '🍇 uva', null, 'A'],
];

// 10  Lector Estrella (L1-P3)
$QUIZ[10] = [
    ['🐟 Cual de estas palabras dice "PEZ"?', 'PEZ', 'PIE', null, null, 'A'],
    ['🐶 Cual de estas palabras dice "PERRO"?', 'PELO', 'PERRO', null, null, 'B'],
    ['🌹 Cual de estas palabras dice "ROSA"?', 'ROSA', 'RUDA', null, null, 'A'],
    ['✋ Cual de estas palabras dice "MANO"?', 'MONO', 'MANO', null, null, 'B'],
];

// 11  Busca la Palabra Correcta (L1-P3)
$QUIZ[11] = [
    ['🐶 Cual es la palabra correcta para esta imagen?', 'PERRO', 'PERLA', null, null, 'A'],
    ['☀️ Cual es la palabra correcta para esta imagen?', 'SAL', 'SOL', null, null, 'B'],
    ['🌙 Cual es la palabra correcta para esta imagen?', 'LUNA', 'LUPA', null, null, 'A'],
    ['🐟 Cual es la palabra correcta para esta imagen?', 'VEZ', 'PEZ', null, null, 'B'],
];

// 14  Cuento Corto, Gran Aventura (L1-P4)
$QUIZ[14] = [
    ['🐰 En la fabula, la liebre perdio porque...', 'Se confio y se durmio', 'Corrio sin parar', null, null, 'A'],
    ['📖 Que ensena el cuento de la tortuga y la liebre?', 'Ser constante ayuda a lograr las metas', 'Hay que dormir mucho', null, null, 'A'],
    ['🐜 En la fabula, la hormiga guardo comida para...', 'El invierno', 'Regalarla toda', null, null, 'A'],
    ['🎶 En la fabula, la cigarra paso el verano...', 'Cantando sin trabajar', 'Guardando comida', null, null, 'A'],
];

// 16  Que Significa (L2-P1)
$QUIZ[16] = [
    ['🏥 Una cruz sobre un edificio significa...', 'Hospital o puesto de salud', 'Zona de juegos', null, null, 'A'],
    ['🚻 Una figura de persona en una puerta significa...', 'Bano', 'Salida de emergencia', null, null, 'A'],
    ['⚠️ Un triangulo amarillo con un signo significa...', 'Precaucion, peligro', 'Todo esta bien', null, null, 'A'],
    ['♻️ Tres flechas en triangulo significan...', 'Reciclar', 'Prohibido pasar', null, null, 'A'],
];

// 19  El Escenario del Cuento (L2-P2)
$QUIZ[19] = [
    ['🌲 Si el cuento habla de arboles altos y animales salvajes, el lugar es...', 'El bosque', 'Un supermercado', null, null, 'A'],
    ['🏙️ Si el cuento nombra edificios, carros y semaforos, el lugar es...', 'La ciudad', 'El fondo del mar', null, null, 'A'],
    ['🏜️ Si el cuento habla de arena, calor y camellos, el lugar es...', 'El desierto', 'Una montana nevada', null, null, 'A'],
    ['🚀 Si el cuento habla de estrellas, planetas y naves, el lugar es...', 'El espacio', 'Una cocina', null, null, 'A'],
];

// 21  Cuento y Vivencia (L2-P3)
$QUIZ[21] = [
    ['📖 En el cuento el personaje pidio perdon. Eso lo hizo sentir...', 'Aliviado', 'Mas enojado', null, null, 'A'],
    ['📖 El personaje ayudo a un amigo. Al final se sintio...', 'Contento', 'Aburrido', null, null, 'A'],
    ['📖 El personaje perdio su juguete favorito. Se sintio...', 'Triste', 'Muy feliz', null, null, 'A'],
    ['📖 El personaje gano un premio. Se sintio...', 'Orgulloso', 'Asustado', null, null, 'A'],
];

// 22  Te ha Pasado (L2-P3)
$QUIZ[22] = [
    ['😮 Alguna vez te sorprendiste como el personaje del cuento?', 'Si, me ha pasado', 'Eso es imposible', null, null, 'A'],
    ['😨 Alguna vez sentiste miedo como el personaje?', 'Puede ser', 'Nunca nadie siente miedo', null, null, 'A'],
    ['😄 Alguna vez te emocionaste por una sorpresa, como el personaje?', 'Si', 'Las sorpresas no emocionan', null, null, 'A'],
    ['🤗 Alguna vez extranaste a alguien, como el personaje?', 'Si, me ha pasado', 'Nadie extrana a nadie', null, null, 'A'],
];

// 24  De Que Tratara (L2-P4)
$QUIZ[24] = [
    ['📚 Un libro titulado "Las Estrellas y los Planetas" trata de...', 'El espacio', 'Recetas de postres', null, null, 'A'],
    ['📚 Un libro titulado "Cuidando a mi Perro" trata de...', 'Como cuidar mascotas', 'Como construir casas', null, null, 'A'],
    ['📚 Un libro titulado "El Dia que Nevo en el Pueblo" trata de...', 'Un dia de nieve', 'Un partido de futbol', null, null, 'A'],
    ['📚 Un libro titulado "Recetas de la Abuela" trata de...', 'Comidas para preparar', 'Viajes al espacio', null, null, 'A'],
];

// 26  Adivina el Final (L2-P4)
$QUIZ[26] = [
    ['🌱 Si la nina rego su planta todos los dias, lo mas probable es que...', 'La planta crecio sana', 'La planta desaparecio', null, null, 'A'],
    ['🧩 Si el nino practico mucho el rompecabezas, al final...', 'Logro armarlo', 'Nunca lo intento', null, null, 'A'],
    ['🥶 Si salieron sin abrigo con mucho frio, lo mas probable es que...', 'Sintieron frio y volvieron por abrigos', 'Sintieron calor', null, null, 'A'],
    ['📢 Si el equipo se preparo mucho para el concurso, lo mas probable es que...', 'Les fue bien', 'No se presentaron', null, null, 'A'],
];

// 28  Que Lenguaje Usa (L3-P1)
$QUIZ[28] = [
    ['📖 Un cuento en un libro usa principalmente...', 'Texto', 'Solo sonido', null, null, 'A'],
    ['📻 Un programa de radio usa principalmente...', 'Sonido', 'Solo imagenes', null, null, 'A'],
    ['🎬 Una pelicula usa...', 'Imagen y sonido juntos', 'Solo texto', null, null, 'A'],
    ['🚦 Una senal de trafico usa principalmente...', 'Imagenes y colores', 'Un texto muy largo', null, null, 'A'],
];

// 30  Quien Cuenta la Historia (L3-P2)
$QUIZ[30] = [
    ['📖 "Nosotros llegamos temprano". Quien cuenta la historia?', 'Un personaje que participa', 'Alguien de afuera', null, null, 'A'],
    ['📖 "El nino abrio la puerta y sonrio". Quien cuenta la historia?', 'Un narrador de afuera', 'El nino mismo', null, null, 'A'],
    ['📖 "Yo tenia mucho miedo esa noche". Quien narra?', 'Un personaje, en primera persona', 'Alguien que no aparece', null, null, 'A'],
    ['📖 "Maria y Luis corrieron al parque". Quien narra?', 'Un narrador de afuera', 'Maria', null, null, 'A'],
];

// 34  Para Que lo Dice (L3-P3)
$QUIZ[34] = [
    ['❓ Si alguien dice "Que hora es?", su proposito es...', 'Preguntar', 'Agradecer', null, null, 'A'],
    ['😀 Si alguien dice "Gracias por tu ayuda", su proposito es...', 'Agradecer', 'Pedir algo', null, null, 'A'],
    ['⚠️ Si alguien dice "Cuidado, esta mojado", su proposito es...', 'Avisar de un peligro', 'Contar un chiste', null, null, 'A'],
    ['👏 Si alguien dice "Lo hiciste muy bien", su proposito es...', 'Felicitar', 'Preguntar la hora', null, null, 'A'],
];

// 36  Lectura entre Lineas (L3-P4)
$QUIZ[36] = [
    ['🥗 "Camila devolvio el plato vacio y sonrio". Que se deduce?', 'Que le gusto la comida', 'Que no tenia hambre', null, null, 'A'],
    ['🧥 "Todos entraron temblando y buscando abrigos". Que se deduce?', 'Que afuera hacia frio', 'Que afuera hacia calor', null, null, 'A'],
    ['📉 "El equipo salio cabizbajo del estadio". Que se deduce?', 'Que perdieron', 'Que ganaron la copa', null, null, 'A'],
    ['🎁 "A Lucia le brillaron los ojos al abrir la caja". Que se deduce?', 'Que le gusto el regalo', 'Que no le importo', null, null, 'A'],
];

// 37  Que Opinas (L3-P4)
$QUIZ[37] = [
    ['🗑️ Un personaje boto basura al rio. Crees que actuo bien?', 'No, contamina el agua', 'Si, no pasa nada', null, null, 'A'],
    ['🤲 Un personaje compartio su merienda. Crees que actuo bien?', 'Si, compartir es bueno', 'No, nunca hay que compartir', null, null, 'A'],
    ['📣 Un personaje se burlo de un companero. Crees que actuo bien?', 'No, burlarse hace dano', 'Si, es gracioso', null, null, 'A'],
    ['♻️ Un personaje reciclo el papel del salon. Crees que actuo bien?', 'Si, cuida el planeta', 'No, es una perdida de tiempo', null, null, 'A'],
];

// 40  Caliente o Frio (CN1-P1)
$QUIZ[40] = [
    ['🔥 Una sopa recien hecha esta...', 'Caliente', 'Fria', null, null, 'A'],
    ['🧊 Un vaso con hielo esta...', 'Caliente', 'Frio', null, null, 'B'],
    ['☀️ La arena de la playa al mediodia esta...', 'Caliente', 'Fria', null, null, 'A'],
    ['❄️ La nieve esta...', 'Caliente', 'Fria', null, null, 'B'],
];

// 43  Fuerte o Flexible (CN1-P2)
$QUIZ[43] = [
    ['🪵 Un tronco de arbol es un material...', 'Duro', 'Flexible', null, null, 'A'],
    ['🧶 Un hilo de lana es un material...', 'Duro', 'Flexible', null, null, 'B'],
    ['🪨 Un ladrillo es un material...', 'Duro', 'Flexible', null, null, 'A'],
    ['🎈 Una liga elastica es un material...', 'Duro', 'Flexible', null, null, 'B'],
];

// 45  Vivo o No Vivo (CN1-P3)
$QUIZ[45] = [
    ['🐶 Un perro es un ser...', 'Vivo', 'No vivo', null, null, 'A'],
    ['🪨 Una piedra es un ser...', 'Vivo', 'No vivo', null, null, 'B'],
    ['🌻 Una flor es un ser...', 'Vivo', 'No vivo', null, null, 'A'],
    ['🚗 Un carro es un ser...', 'Vivo', 'No vivo', null, null, 'B'],
];

// 50  Cambios en mi Cuerpo (CN1-P4)
$QUIZ[50] = [
    ['👟 Si el ano pasado te quedaba un zapato y ahora te aprieta, es porque...', 'Tu pie crecio', 'El zapato encogio solo', null, null, 'A'],
    ['💇 Cortarte el pelo y que vuelva a crecer es un cambio...', 'Normal del cuerpo', 'Muy raro', null, null, 'A'],
    ['👶 Un bebe con el tiempo aprende a...', 'Caminar y hablar', 'Volar', null, null, 'A'],
    ['🦵 Con los anos, los huesos de un nino...', 'Se hacen mas largos', 'Se quedan igual para siempre', null, null, 'A'],
];

// 51  Empuja y Jala (CN2-P1)
$QUIZ[51] = [
    ['🚪 Cerrar una puerta alejandola de ti es...', 'Empujar', 'Jalar', null, null, 'A'],
    ['🎣 Traer la cana de pescar hacia ti es...', 'Jalar', 'Empujar', null, null, 'A'],
    ['🛒 Llevar el carrito hacia adelante es...', 'Empujar', 'Jalar', null, null, 'A'],
    ['🪢 Ganar en la cuerda trayendola hacia tu lado es...', 'Jalar', 'Empujar', null, null, 'A'],
];

// 55  Que Estado Es (CN2-P2)
$QUIZ[55] = [
    ['🥛 La leche en un vaso esta en estado...', 'Liquido', 'Solido', 'Gaseoso', null, 'A'],
    ['🧱 Un ladrillo esta en estado...', 'Liquido', 'Solido', 'Gaseoso', null, 'B'],
    ['🎈 El aire dentro de un globo esta en estado...', 'Liquido', 'Solido', 'Gaseoso', null, 'C'],
    ['💨 El vapor de una olla esta en estado...', 'Liquido', 'Solido', 'Gaseoso', null, 'C'],
];

// 58  Lo Que Necesito para Vivir (CN2-P3)
$QUIZ[58] = [
    ['🐦 Un pajaro necesita para vivir...', 'Aire y comida', 'Vivir bajo el agua', null, null, 'A'],
    ['🌵 Un cactus del desierto necesita...', 'Poca agua y mucho sol', 'Mucha sombra y mucha agua', null, null, 'A'],
    ['🐻 Un oso necesita para vivir...', 'Alimento y un refugio', 'Un carro', null, null, 'A'],
    ['🐠 Un pez necesita el agua para...', 'Respirar y moverse', 'Dormir de pie', null, null, 'A'],
];

// 61  Que Cambio (CN2-P4)
$QUIZ[61] = [
    ['🐛 Entre la oruga y la mariposa, que cambio?', 'La forma del cuerpo', 'Nada cambio', null, null, 'A'],
    ['🌰 Entre la bellota y el roble, que cambio?', 'Crecio y se hizo arbol', 'Sigue igual de pequena', null, null, 'A'],
    ['🐸 Entre el renacuajo y la rana, que cambio?', 'Le salieron patas y perdio la cola', 'Nada', null, null, 'A'],
    ['👶 Entre el bebe y el nino, que cambio?', 'Crecio y aprendio cosas nuevas', 'Nada cambio', null, null, 'A'],
];

// 64  Refleja o Absorbe (CN3-P1)
$QUIZ[64] = [
    ['🥄 Una cuchara de metal brillante frente a la luz...', 'Refleja la luz', 'Se la traga toda', null, null, 'A'],
    ['⬛ Un carton negro frente a la luz...', 'Absorbe la luz', 'La refleja como espejo', null, null, 'A'],
    ['🪟 Un vidrio limpio frente a la luz...', 'Deja pasar la luz', 'La bloquea del todo', null, null, 'A'],
    ['🌕 La Luna se ve de noche porque...', 'Refleja la luz del Sol', 'Tiene luz propia', null, null, 'A'],
];

// 66  Grande o Pequena (CN3-P2)
$QUIZ[66] = [
    ['🔦 Si acercas mucho la linterna al objeto, la sombra se ve...', 'Mas grande', 'Mas pequena', null, null, 'A'],
    ['🔦 Si alejas la linterna del objeto, la sombra se ve...', 'Mas pequena', 'Mas grande', null, null, 'A'],
    ['🌅 Cuando el sol esta bajito, tu sombra es...', 'Muy larga', 'Muy cortita', null, null, 'A'],
    ['🌞 Cuando el sol esta justo arriba, tu sombra es...', 'Corta', 'Larguisima', null, null, 'A'],
];

// 68  Que Pasa con la Sombra (CN3-P2)
$QUIZ[68] = [
    ['🚶 Si te alejas de la lampara, tu sombra...', 'Se hace mas grande', 'Se hace mas pequena', null, null, 'A'],
    ['🧍 Si te acercas a la lampara, tu sombra...', 'Se hace mas grande', 'Desaparece de una', null, null, 'A'],
    ['💡 Sin ninguna luz, hay sombra?', 'No, sin luz no hay sombra', 'Si, siempre hay', null, null, 'A'],
    ['🔆 Con dos lamparas encendidas puedes tener...', 'Dos sombras', 'Ninguna sombra', null, null, 'A'],
];

// 70  Agudo o Grave (CN3-P3)
$QUIZ[70] = [
    ['🐦 El canto de un pajarito pequeno suele ser...', 'Agudo', 'Grave', null, null, 'A'],
    ['🐘 El sonido que hace un elefante suele ser...', 'Grave', 'Agudo', null, null, 'A'],
    ['🔔 Una campanita pequena suena...', 'Aguda', 'Grave', null, null, 'A'],
    ['📢 Un tambor grande y un bombo suenan...', 'Graves', 'Agudos', null, null, 'A'],
];

// 73  Sube o Baja la Temperatura (CN3-P4)
$QUIZ[73] = [
    ['🧊 El hielo se derrite cuando la temperatura...', 'Sube', 'Baja', null, null, 'A'],
    ['💨 El agua se evapora mas rapido cuando la temperatura...', 'Sube', 'Baja', null, null, 'A'],
    ['❄️ El agua se congela cuando la temperatura...', 'Baja mucho', 'Sube mucho', null, null, 'A'],
    ['🍫 Un chocolate se pone duro otra vez cuando la temperatura...', 'Baja', 'Sube', null, null, 'A'],
];

/* ================================================================== *
 *  GENERACION DEL .sql
 * ================================================================== */

$out = [];
$out[] = "-- ==================================================================";
$out[] = "-- IncluKids - Mas rondas / preguntas en los juegos";
$out[] = "-- Generado por database/seeds/_build_mas_rondas.php  (" . date('Y-m-d') . ")";
$out[] = "--";
$out[] = "-- Cargar con:";
$out[] = "--   mysql -u root incluKids --default-character-set=utf8mb4 < este_archivo.sql";
$out[] = "--";
$out[] = "-- Idempotencia: los UPDATE de contenido_juego reescriben datos_json";
$out[] = "-- completo, asi que re-ejecutar deja el mismo resultado. Los INSERT";
$out[] = "-- de contenidoactividad se protegen con NOT EXISTS por (id_actividad, orden).";
$out[] = "-- ==================================================================";
$out[] = "";
$out[] = "SET NAMES utf8mb4;";
$out[] = "START TRANSACTION;";
$out[] = "";

function sqlStr($s) {
    return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $s) . "'";
}

/* ---- contenido_juego: reescribir datos_json con las rondas ampliadas ---- */
$stmt = $pdo->query("SELECT cj.id_actividad, cj.datos_json, a.id_tipo, a.nombre
                     FROM contenido_juego cj JOIN actividades a ON a.id_actividad = cj.id_actividad
                     ORDER BY a.id_tipo, cj.id_actividad");
foreach ($stmt as $row) {
    $id = (int) $row['id_actividad'];
    if (!isset($JUEGOS[$id]) || !$JUEGOS[$id]) continue;

    $datos = json_decode($row['datos_json'], true);
    if (!is_array($datos)) {
        fwrite(STDERR, "AVISO: datos_json invalido en id_actividad $id, se omite\n");
        continue;
    }

    if (isset($datos['pares'])) {
        $datos['pares'] = array_merge($datos['pares'], $JUEGOS[$id]);
        $cuenta = count($datos['pares']);
        $unidad = 'pares';
    } else {
        $datos['rondas'] = array_merge($datos['rondas'] ?? [], $JUEGOS[$id]);
        $cuenta = count($datos['rondas']);
        $unidad = 'rondas';
    }

    $json = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $out[] = "-- [{$row['nombre']}] (id_actividad $id, tipo {$row['id_tipo']}) -> $cuenta $unidad";
    $out[] = "UPDATE contenido_juego SET datos_json = " . sqlStr($json) . " WHERE id_actividad = $id;";
    $out[] = "";
}

/* ---- contenidoactividad: nuevas preguntas de seleccionar ---- */
$out[] = "-- ---------- Preguntas nuevas de \"Seleccionar\" ----------";
$out[] = "";
foreach ($QUIZ as $id => $preguntas) {
    $id = (int) $id;
    $orden = 3;
    $out[] = "-- id_actividad $id";
    foreach ($preguntas as $q) {
        [$preg, $a, $b, $c, $d, $ok] = $q;
        $cols = "(id_actividad, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, orden)";
        $vals = "$id, " . sqlStr($preg) . ", " . sqlStr($a) . ", " . sqlStr($b) . ", "
              . ($c === null ? "NULL" : sqlStr($c)) . ", "
              . ($d === null ? "NULL" : sqlStr($d)) . ", " . sqlStr($ok) . ", $orden";
        $out[] = "INSERT INTO contenidoactividad $cols SELECT $vals FROM DUAL "
               . "WHERE NOT EXISTS (SELECT 1 FROM contenidoactividad WHERE id_actividad = $id AND orden = $orden);";
        $orden++;
    }
    $out[] = "";
}

$out[] = "COMMIT;";
$out[] = "";

$destino = __DIR__ . "/2026_08_26_mas_rondas_juegos.sql";
file_put_contents($destino, implode("\n", $out));
echo "Seed escrito en: $destino\n";
echo "Lineas: " . count($out) . "\n";
