-- =============================================================
--  IncluKids · Esquema completo de la base de datos + datos base
-- =============================================================
--  Generado desde la base real del proyecto (mysqldump --no-data),
--  porque el repo solo tenía cambios incrementales y nunca el
--  esquema completo.
--
--  Crea la BD `inclukids`, sus 12 tablas y los datos base
--  imprescindibles: materias, tipos de actividad, catálogo de DBA
--  y la cuenta de administración.
--
--  Uso (instalación nueva):
--    C:/xampp/mysql/bin/mysql -u root < database/schema.sql
--
--  ⚠  Este archivo hace DROP TABLE de todas las tablas. NO lo
--     ejecutes contra una base que ya tenga datos reales. Para
--     refrescar el esquema versionado desde una base viva usa:
--       mysqldump -u root --no-data inclukids > database/schema.sql
--     (y vuelve a pegar el bloque de "DATOS BASE" del final).
-- =============================================================

CREATE DATABASE IF NOT EXISTS `inclukids`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE `inclukids`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `progreso`;
DROP TABLE IF EXISTS `sesionactividad`;
DROP TABLE IF EXISTS `sesionesclase`;
DROP TABLE IF EXISTS `contenido_juego`;
DROP TABLE IF EXISTS `contenidoactividad`;
DROP TABLE IF EXISTS `actividades`;
DROP TABLE IF EXISTS `tipoactividad`;
DROP TABLE IF EXISTS `dba`;
DROP TABLE IF EXISTS `estudiantes`;
DROP TABLE IF EXISTS `cursos`;
DROP TABLE IF EXISTS `login_bloqueo`;
DROP TABLE IF EXISTS `docentes`;
DROP TABLE IF EXISTS `materias`;

SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------------
--  Materias (según el alcance del proyecto: solo dos)
-- -------------------------------------------------------------
CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_materia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Docentes (incluye administradores; el rol los diferencia).
--  OJO: la columna de contraseña se llama literalmente `contraseña`.
-- -------------------------------------------------------------
CREATE TABLE `docentes` (
  `id_docente` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `rol` enum('administrador','docente') NOT NULL DEFAULT 'docente',
  `contraseña` varchar(255) NOT NULL,
  `solicitud_password_en` datetime DEFAULT NULL,
  PRIMARY KEY (`id_docente`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Límite de intentos de inicio de sesión por IP (anti fuerza-bruta).
--  La mantiene app/helpers/rate_limit.php.
-- -------------------------------------------------------------
CREATE TABLE `login_bloqueo` (
  `ip` varchar(45) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `intentos` int(11) NOT NULL DEFAULT 0,
  `primer_intento` datetime NOT NULL,
  `ultimo_intento` datetime NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Bitácora de auditoría (quién borró/retiró/restauró qué).
--  La escribe app/helpers/auditoria.php con auditar().
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `id_docente` int(11) DEFAULT NULL,
  `docente_nombre` varchar(200) DEFAULT NULL,
  `rol` varchar(20) DEFAULT NULL,
  `accion` varchar(40) NOT NULL,
  `entidad` varchar(40) NOT NULL,
  `id_entidad` int(11) DEFAULT NULL,
  `detalle` varchar(500) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_auditoria_fecha` (`fecha`),
  KEY `idx_auditoria_entidad` (`entidad`,`id_entidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Cursos (un grado/grupo/año, a cargo de un docente)
-- -------------------------------------------------------------
CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL AUTO_INCREMENT,
  `grado` tinyint(4) NOT NULL,
  `grupo` char(1) NOT NULL,
  `anio` year(4) NOT NULL,
  `id_docente` int(11) NOT NULL,
  PRIMARY KEY (`id_curso`),
  KEY `fk_docente_curso` (`id_docente`),
  CONSTRAINT `fk_docente_curso` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`id_docente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Estudiantes (pertenecen a un curso)
-- -------------------------------------------------------------
CREATE TABLE `estudiantes` (
  `id_estudiante` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `eliminado_en` datetime DEFAULT NULL,
  PRIMARY KEY (`id_estudiante`),
  KEY `fk_curso_estudiante` (`id_curso`),
  KEY `idx_estudiante_eliminado` (`eliminado_en`),
  CONSTRAINT `fk_curso_estudiante` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  DBA · Derechos Básicos de Aprendizaje
--  (materia + grado + periodo -> lista de DBA)
-- -------------------------------------------------------------
CREATE TABLE `dba` (
  `id_dba` int(11) NOT NULL AUTO_INCREMENT,
  `id_materia` int(11) NOT NULL,
  `grado` tinyint(4) NOT NULL,
  `periodo` tinyint(4) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id_dba`),
  KEY `fk_materia_dba` (`id_materia`),
  CONSTRAINT `fk_materia_dba` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Tipos de actividad (mecánica de juego).
--  Los id fijos importan: app/views/juegos/jugar.php mapea
--  id_tipo -> archivo de motor de juego.
-- -------------------------------------------------------------
CREATE TABLE `tipoactividad` (
  `id_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Actividades (un juego, ligado a un DBA y a una mecánica)
-- -------------------------------------------------------------
CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_dba` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `instrucciones` text DEFAULT NULL,
  PRIMARY KEY (`id_actividad`),
  KEY `fk_dba_actividad` (`id_dba`),
  KEY `fk_tipo_actividad` (`id_tipo`),
  CONSTRAINT `fk_dba_actividad` FOREIGN KEY (`id_dba`) REFERENCES `dba` (`id_dba`),
  CONSTRAINT `fk_tipo_actividad` FOREIGN KEY (`id_tipo`) REFERENCES `tipoactividad` (`id_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Contenido de opción múltiple (una fila = una pregunta).
--  Las otras mecánicas usan contenido_juego (JSON).
-- -------------------------------------------------------------
CREATE TABLE `contenidoactividad` (
  `id_contenido` int(11) NOT NULL AUTO_INCREMENT,
  `id_actividad` int(11) NOT NULL,
  `pregunta` text NOT NULL,
  `opcion_a` varchar(255) NOT NULL,
  `opcion_b` varchar(255) NOT NULL,
  `opcion_c` varchar(255) DEFAULT NULL,
  `opcion_d` varchar(255) DEFAULT NULL,
  `respuesta_correcta` enum('A','B','C','D') NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_contenido`),
  KEY `id_actividad` (`id_actividad`),
  CONSTRAINT `contenidoactividad_ibfk_1` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Contenido jugable en JSON para mecánicas que no son de
--  opción múltiple (arrastrar, memoria, rompecabezas, ...).
-- -------------------------------------------------------------
CREATE TABLE `contenido_juego` (
  `id_contenido_juego` int(11) NOT NULL AUTO_INCREMENT,
  `id_actividad` int(11) NOT NULL,
  `datos_json` longtext NOT NULL,
  PRIMARY KEY (`id_contenido_juego`),
  UNIQUE KEY `unico_por_actividad` (`id_actividad`),
  CONSTRAINT `fk_contenido_juego_actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Sesiones de clase (una clase dictada: docente + curso + DBA)
-- -------------------------------------------------------------
CREATE TABLE `sesionesclase` (
  `id_sesion` int(11) NOT NULL AUTO_INCREMENT,
  `id_docente` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_dba` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_sesion`),
  KEY `fk_docente_sesion` (`id_docente`),
  KEY `fk_curso_sesion` (`id_curso`),
  KEY `fk_dba_sesion` (`id_dba`),
  CONSTRAINT `fk_curso_sesion` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  CONSTRAINT `fk_dba_sesion` FOREIGN KEY (`id_dba`) REFERENCES `dba` (`id_dba`),
  CONSTRAINT `fk_docente_sesion` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`id_docente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Actividades trabajadas dentro de una sesión (N:M)
-- -------------------------------------------------------------
CREATE TABLE `sesionactividad` (
  `id_sesion_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_sesion` int(11) NOT NULL,
  `id_actividad` int(11) NOT NULL,
  PRIMARY KEY (`id_sesion_actividad`),
  KEY `fk_sesion_actividad` (`id_sesion`),
  KEY `fk_actividad_sesion` (`id_actividad`),
  CONSTRAINT `fk_actividad_sesion` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`),
  CONSTRAINT `fk_sesion_actividad` FOREIGN KEY (`id_sesion`) REFERENCES `sesionesclase` (`id_sesion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------------
--  Progreso de cada estudiante por sesión
-- -------------------------------------------------------------
CREATE TABLE `progreso` (
  `id_progreso` int(11) NOT NULL AUTO_INCREMENT,
  `id_sesion` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `estado` enum('Logrado','En proceso','Requiere apoyo') NOT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_progreso`),
  KEY `fk_sesion_progreso` (`id_sesion`),
  KEY `fk_estudiante_progreso` (`id_estudiante`),
  CONSTRAINT `fk_estudiante_progreso` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`),
  CONSTRAINT `fk_sesion_progreso` FOREIGN KEY (`id_sesion`) REFERENCES `sesionesclase` (`id_sesion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================================
--  DATOS BASE
--  Necesarios para que la app arranque y para que las semillas
--  de juegos (database/seeds/2026_*.sql, que se unen a `dba` por
--  su `codigo`) tengan a qué engancharse.
--  Los juegos y su contenido se cargan aparte, desde database/seeds/.
-- =============================================================

-- Materias del alcance del proyecto
INSERT INTO `materias` (`id_materia`, `nombre`) VALUES
  (1, 'Lenguaje'),
  (2, 'Ciencias Naturales');

-- Tipos de actividad. Los id DEBEN coincidir con el mapeo de
-- app/views/juegos/jugar.php ($motorPorTipo).
INSERT INTO `tipoactividad` (`id_tipo`, `nombre`) VALUES
  (1, 'Memoria'),
  (2, 'Emparejar'),
  (3, 'Rompecabezas'),
  (4, 'Seleccionar'),
  (5, 'Ordenar'),
  (6, 'Completar'),
  (7, 'Arrastrar y soltar');

-- Catálogo de DBA (texto oficial del MEN, Colombia).
-- Lenguaje grados 1-3 y Ciencias Naturales grados 1-3, 4 periodos c/u.
INSERT INTO `dba` (`id_dba`, `id_materia`, `grado`, `periodo`, `codigo`, `descripcion`) VALUES
  (1,1,1,1,'L1-P1','Relaciona códigos no verbales, como los movimientos corporales y los gestos de las manos o del rostro, con el significado que pueden tomar de acuerdo con el contexto.'),
  (2,1,1,2,'L1-P2','Reconoce en los textos literarios la posibilidad de desarrollar su capacidad creativa y lúdica.'),
  (3,1,1,3,'L1-P3','Interpreta diversos textos a partir de la lectura de palabras sencillas y de las imágenes que contienen.'),
  (4,1,1,4,'L1-P4','Escribe palabras que le permiten comunicar sus ideas, preferencias y aprendizajes.'),
  (5,1,2,1,'L2-P1','Identifica la función que cumplen las señales y símbolos que aparecen en su entorno.'),
  (6,1,2,2,'L2-P2','Identifica algunos elementos constitutivos de textos literarios como personajes, espacios y acciones.'),
  (7,1,2,3,'L2-P3','Comprende diversos textos literarios a partir de sus propias vivencias.'),
  (8,1,2,4,'L2-P4','Predice y analiza los contenidos y estructuras de diversos tipos de texto, a partir de sus conocimientos previos.'),
  (9,1,3,1,'L3-P1','Comprende que algunos escritos y manifestaciones artísticas pueden estar compuestos por texto, sonido e imágenes.'),
  (10,1,3,2,'L3-P2','Reconoce algunas características de los textos narrativos, tales como el concepto de narrador y estructura narrativa, a partir de la recreación y disfrute de los mismos.'),
  (11,1,3,3,'L3-P3','Identifica el papel del emisor y el receptor y sus propósitos comunicativos en una situación específica.'),
  (12,1,3,4,'L3-P4','Interpreta el contenido y la estructura del texto, respondiendo preguntas de orden inferencial y crítico.'),
  (13,2,1,1,'CN1-P1','Comprende que los sentidos le permiten percibir algunas características de los objetos que nos rodean (temperatura, sabor, sonidos, olor, color, texturas y formas).'),
  (14,2,1,2,'CN1-P2','Comprende que existe una gran variedad de materiales y que éstos se utilizan para distintos fines, según sus características (longitud, dureza, flexibilidad, permeabilidad al agua, solubilidad, ductilidad, maleabilidad, color, sabor y textura).'),
  (15,2,1,3,'CN1-P3','Comprende que los seres vivos (plantas y animales) tienen características comunes (se alimentan, respiran, tienen un ciclo de vida, responden al entorno) y los diferencia de los objetos inertes.'),
  (16,2,1,4,'CN1-P4','Comprende que su cuerpo experimenta constantes cambios a lo largo del tiempo y reconoce a partir de su comparación que tiene características similares y diferentes a las de sus padres y compañeros.'),
  (17,2,2,1,'CN2-P1','Comprende que una acción mecánica (fuerza) puede producir distintas deformaciones en un objeto, y que este resiste a las fuerzas de diferente modo, de acuerdo con el material del que está hecho.'),
  (18,2,2,2,'CN2-P2','Comprende que las sustancias pueden encontrarse en distintos estados (sólido, líquido y gaseoso).'),
  (19,2,2,3,'CN2-P3','Comprende la relación entre las características físicas de plantas y animales con los ambientes en donde viven, teniendo en cuenta sus necesidades básicas (luz, agua, aire, suelo, nutrientes, desplazamiento y protección).'),
  (20,2,2,4,'CN2-P4','Explica los procesos de cambios físicos que ocurren en el ciclo de vida de plantas y animales de su entorno, en un período de tiempo determinado.'),
  (21,2,3,1,'CN3-P1','Comprende la forma en que se propaga la luz a través de diferentes materiales (opacos, transparentes como el aire, translúcidos como el papel y reflectivos como el espejo).'),
  (22,2,3,2,'CN3-P2','Comprende la forma en que se produce la sombra y la relación de su tamaño con las distancias entre la fuente de luz, el objeto interpuesto y el lugar donde se produce la sombra.'),
  (23,2,3,3,'CN3-P3','Comprende la naturaleza (fenómeno de la vibración) y las características del sonido (altura, timbre, intensidad) y que este se propaga en distintos medios (sólidos, líquidos, gaseosos).'),
  (24,2,3,4,'CN3-P4','Comprende la influencia de la variación de la temperatura en los cambios de estado de la materia, considerando como ejemplo el caso del agua.');

-- Cuenta de administración inicial.
--   correo:      admin@inclukids.com
--   contraseña:  admin123   <-- CÁMBIALA al primer inicio de sesión
-- El hash es bcrypt (password_hash / PASSWORD_DEFAULT).
INSERT INTO `docentes` (`id_docente`, `nombres`, `apellidos`, `correo`, `rol`, `contraseña`) VALUES
  (1, 'Administrador', 'IncluKids', 'admin@inclukids.com', 'administrador',
   '$2y$10$xl6BKdUf0NHO4BRH2ZJ3iOQaZgT.MMLLe8SU8L.DhalTKS07xBj1.');
