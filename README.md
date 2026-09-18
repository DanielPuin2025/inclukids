# IncluKids

Plataforma web de apoyo al aula para **educación inclusiva** en primaria
(grados 1.º a 3.º), enfocada en **Lengua Castellana** y **Ciencias
Naturales**. El docente arma sesiones de clase a partir de los **DBA**
(Derechos Básicos de Aprendizaje), trabaja con los estudiantes una
colección de **juegos** adaptados (con opciones de accesibilidad) y
registra el **progreso** de cada niño. Hay un rol de **administrador**
con una vista global de todo el colegio.

## Requisitos

- **XAMPP** (o equivalente) con:
  - PHP **8.0+** (usa `password_hash`, operador `??`, tipados sueltos)
  - MySQL / MariaDB
- El proyecto se sirve desde `htdocs` con el nombre de carpeta
  **`IncluKids`** (las rutas de recursos están escritas como
  `/IncluKids/public/...`). Si la clonas con otro nombre, ajusta esas
  rutas o crea un alias en Apache.
- Apache con **`AllowOverride All`** en `htdocs` (valor por defecto de
  XAMPP) y los módulos `mod_headers`, `mod_rewrite` y `mod_alias`
  activados, para que `.htaccess` aplique las cabeceras de seguridad y
  bloquee el acceso directo a `database/`, `app/config/`, etc. Si no se
  puede usar `.htaccess`, el sitio funciona igual pero sin ese
  endurecimiento.

## Puesta en marcha

1. **Clonar** dentro de `htdocs`:

   ```bash
   cd C:/xampp/htdocs
   git clone <url-del-repo> IncluKids
   ```

2. **Crear la base de datos y el esquema** (crea la BD `inclukids`,
   las 12 tablas y los datos base: materias, tipos de actividad,
   catálogo de DBA y la cuenta de administración):

   ```bash
   C:/xampp/mysql/bin/mysql -u root < IncluKids/database/schema.sql
   ```

3. **Cargar el contenido de actividades y juegos** (72 actividades,
   sus preguntas y su contenido jugable, tal cual producción):

   ```bash
   C:/xampp/mysql/bin/mysql -u root inclukids < IncluKids/database/seeds/9999_12_31_contenido_completo.sql
   ```

4. **Abrir** `http://localhost/IncluKids/`.

> **Instalación nueva:** `schema.sql` ya incluye todo (incluidas las
> tablas `login_bloqueo` y `auditoria` y la columna
> `estudiantes.eliminado_en`), no hace falta tocar `database/migrations/`.
> **Base que ya existía:** corre el runner de migraciones, que aplica
> las que falten en orden y lleva registro en la tabla `migraciones`:
> `C:\xampp\php\php.exe scripts\migrar.php` (o `scripts\migrar.bat`;
> `--list` muestra el estado sin aplicar nada). Es seguro correrlo
> varias veces y también sobre una base creada desde `schema.sql` (las
> migraciones que "ya estaban" se marcan igual).

> Los archivos `database/seeds/2026_*.sql` son el **historial** de cómo
> se fue construyendo el contenido; no hace falta ejecutarlos y no lo
> reproducen entero (parte se editó desde el panel). Los
> `database/seeds/_build_*.php` son scripts generadores, no se corren
> contra la BD.

### Cuenta de administración inicial

| Campo      | Valor                   |
|------------|-------------------------|
| Correo     | `admin@inclukids.com`   |
| Contraseña | `admin123`              |

> **Hay que cambiarla en el primer inicio de sesión**: mientras el
> administrador siga con `admin123` (o cualquier contraseña que no
> cumpla la política mínima), el resto del panel queda bloqueado y se
> redirige a "Mi perfil" hasta cambiarla. El **registro público está
> cerrado**: las cuentas de docente las crea el administrador desde
> "Docentes" en el panel (`app/views/docentes/nuevo.php`).

## Configuración de la base de datos

Las credenciales están en [`app/config/database.php`](app/config/database.php)
(`localhost`, usuario `root`, sin contraseña, BD `incluKids` — MySQL en
Windows la trata como `inclukids`). Ajusta ese archivo si tu MySQL usa
otras.

### Volver a generar el esquema versionado desde una base viva

`schema.sql` se generó con `mysqldump`. Si cambias tablas en tu BD y
quieres actualizar el archivo del repo:

```bash
C:/xampp/mysql/bin/mysqldump -u root --no-data inclukids > database/schema.sql
# y vuelve a pegar al final el bloque "DATOS BASE"
C:/xampp/mysql/bin/mysqldump -u root --no-create-info --skip-extended-insert \
  --complete-insert inclukids actividades contenidoactividad contenido_juego \
  > database/seeds/9999_12_31_contenido_completo.sql
```

## Estructura

```
app/
  config/       Conexión PDO a MySQL
  controllers/  Un controlador por entidad (Login, Estudiante, DBA, ...)
  models/       Acceso a datos con PDO + consultas preparadas
  helpers/      auth.php (guard de rol administrador)
  views/        Vistas PHP; cada carpeta es un módulo
    juegos/     Un archivo por mecánica de juego (arrastrar, memoria, ...)
database/
  schema.sql    Esquema completo + datos base  ← empezar aquí
  migrations/   Cambios incrementales aplicados sobre el esquema
  seeds/        Actividades, contenido de juegos y ajustes
public/
  css/ js/ img/ (incluye img/iconos/*.svg, ilustraciones de los juegos)
index.php       Login
logout.php
```

## Roles y navegación

- **Administrador** → `app/views/dashboard/home.php`: vista global
  (estudiantes, cursos, DBA, actividades, sesiones y progreso de todos
  los docentes), más un "Panel de un docente" para ver su propia cuenta
  como la vería un profesor.
- **Docente** → `app/views/dashboard/visualizador.php`: solo sus cursos,
  sus estudiantes y su progreso. El panel son tarjetas: **Mis
  estudiantes**, **Mis sesiones de clase**
  (`app/views/sesiones/mis_sesiones.php`) y **Progreso de mis
  estudiantes** (`app/views/progreso/panel.php`) son enlaces a su propia
  vista; **DBA correspondientes** se despliega en el sitio. Flujo de
  clase: `crear sesión → curso → materia → periodo → DBA → actividades →
  registrar progreso`.

## Accesibilidad

Ajustes: **tamaño de texto** (A / A+ / A++), **alto contraste**,
**reducir movimiento** y, en las pantallas de juego, **leer en voz
alta**. Se guardan en `localStorage` (`inclukids_a11y`; la voz y el
movimiento también en `inclukids_juegos`, que es lo que leen los
motores de juego).

El **tamaño de texto** toma las clases `html.texto-grande` /
`html.texto-extra`. Como casi todo el texto de la app va en `px`, subir
solo el `font-size` de `<html>` casi no se notaba; ahora, **fuera de
las pantallas de juego**, se escala el contenido entero con `zoom`
(A+ = 1.25, A++ = 1.6) y hay un respaldo con `font-size` de raíz para
navegadores sin `zoom`. Ese bloque `@supports` está **duplicado** en
`public/css/a11y.css` y `public/css/panel.css`: cámbialos juntos. **En
los juegos** no se usa `zoom` (rompería el arrastre): se suben solo los
textos, con saltos marcados entre los tres tamaños.

El **alto contraste** (`body.alto-contraste`) pinta el fondo de negro y
**todo el texto de blanco** con una regla comodín
(`body.alto-contraste *`), para que ninguna utilidad de Bootstrap
(`.text-muted`, `<small>`…) ni texto suelto se quede oscuro e ilegible.
Lo que sí lleva color —enlaces (amarillo), botones (negro sobre
amarillo), estados de acierto (verde)— se re-corrige con selectores más
específicos que ganan a la comodín. Este bloque también vive por
duplicado en `a11y.css` y `panel.css`.

Dos formas de acceder a los mismos ajustes, sincronizadas por esa
clave:

- **Paneles de administrador y docente**: menú de la cuenta →
  *Accesibilidad* (ya existía).
- **Resto de vistas de gestión, juegos y login**: botón flotante ♿
  abajo a la izquierda, inyectado por `public/js/a11y.js`
  (+ `public/css/a11y.css`). Los `<link>`/`<script>` llevan `?v=N` para
  saltarse la caché del navegador: en las vistas de gestión ese número
  es la constante `ASSET_VER` de [`app/helpers/vista.php`](app/helpers/vista.php)
  (súbela al cambiar esos archivos); los juegos y el login lo llevan
  escrito a mano en su propio `<head>`.

### En las pantallas de juego

Como la app la maneja el docente y el estudiante solo mira y responde
de viva voz:

- **Botón 🔊 "Escuchar"**: lee la consigna **y también** las opciones /
  fichas / casillas que hay en pantalla (respeta el toggle "Leer en voz
  alta"). Las ilustraciones llevan `alt` con su etiqueta.
- **Voz al señalar** (`public/js/juegos-voz.js`, solo en las pantallas
  de juego): si el docente deja el puntero sobre una ficha, zona,
  opción, botón o título, se lee ese nombre en voz alta (respeta el
  mismo toggle "Leer en voz alta"). No se activa en táctil ni dentro de
  los paneles de accesibilidad / ajustes / notas del docente.
- **Sonidos de toque**: un tono corto al pulsar cualquier botón y, en
  los juegos de arrastrar (Arrastrar, Completar, Rompecabezas), un tono
  al **agarrar** y otro al **soltar** una ficha.
- **Botón 🖥️ "Presentación"** (arriba a la izquierda): deja la pantalla
  limpia para el niño — oculta los controles del docente (♿ y ⚙️),
  agranda el contenido y pasa a pantalla completa. Se sale con el mismo
  botón o con `Escape`.
- **Botón ⓘ "Notas para el docente"** (arriba a la derecha, oculto en
  modo presentación): muestra el objetivo y las instrucciones/adaptaciones
  de la actividad (campo `instrucciones` de la BD), que si no solo se ven
  en la pantalla de edición del admin.
- **Pantalla final**: un renglón discreto con `N rondas · ayudas: M`
  (o `sin ayudas 🌟`) — un dato objetivo para que el docente decida la
  nota, sin marcar nada como "mal".

## Notas de desarrollo

- Sin framework: enrutamiento por archivo (cada vista es un endpoint).
  Toda vista de gestión empieza con
  `require __DIR__ . "/../../bootstrap.php";` (y, si es solo de admin,
  `requireAdministrador("../dashboard/visualizador.php");` justo
  después). `app/bootstrap.php` arranca la sesión con la cookie
  endurecida (`sesion_iniciar()`), aplica el cierre por inactividad y
  el guard de "hay que estar logueado", y deja cargados los helpers de
  CSRF, de rol, de vista, de avisos (`flash_*`) y de auditoría. Las
  pantallas de juego NO lo usan, pero sí llaman a `sesion_iniciar()`
  (de `app/helpers/session_boot.php`).
- **Guarda `INCLUKIDS`**: `session_boot.php` (lo primero que carga cada
  punto de entrada) define la constante `INCLUKIDS`; los archivos de
  `app/config`, `app/models`, `app/controllers` y `app/helpers`
  empiezan con `defined("INCLUKIDS") or exit(...)`. Defensa en
  profundidad por si se despliega sin el `.htaccess` que ya los bloquea.
- La cabecera HTML de las vistas de gestión se genera con
  `render_head("Título"[, $opts])` de
  [`app/helpers/vista.php`](app/helpers/vista.php), no copiando el
  bloque `<!DOCTYPE> … </head>` en cada archivo. `render_head()` ya
  incluye `a11y.js`, `confirmar.js` y `tabla-filtro.js`.
- **Avisos**: los handlers guardan `flash_set("exito"|"error", "…")` y
  redirigen; la vista de destino los pinta con `<?= flash_render() ?>`.
  Sustituye a los `die("Faltan datos…")` que dejaban páginas muertas.
- **Confirmación de borrado**: los formularios que eliminan algo llevan
  `data-confirmar="¿…?"` (y opcional `data-confirmar-boton` /
  `data-confirmar-titulo`) en vez de `onsubmit="return confirm(...)"`.
  [`public/js/confirmar.js`](public/js/confirmar.js) intercepta el envío
  y muestra un cuadro con el estilo de la app (Esc / clic fuera =
  cancelar; el foco arranca en "Cancelar"). Sin JS no hay confirmación.
- **Filtro de tablas**: un `<input data-filtro-tabla="idTabla">` filtra
  en vivo las filas de esa tabla (sin acentos ni mayúsculas), con un
  contador `data-filtro-cuenta` y una fila `data-filtro-vacio`
  ([`public/js/tabla-filtro.js`](public/js/tabla-filtro.js)). Está en
  Actividades, DBA, Mis estudiantes, la Bitácora, Cursos, Docentes y
  Mis sesiones.
- **Retirar estudiantes** es un **borrado suave**: se marca
  `estudiantes.eliminado_en` y deja de listarse, pero su progreso se
  conserva. La **papelera** (`estudiantes/papelera.php`, enlace
  "🗑️ Retirados" en las listas) permite **restaurar** o **borrar en
  firme** (esto último sí borra al estudiante y su progreso).
- **Bitácora**: `auditar($accion, $entidad, $id, $detalle)` de
  [`app/helpers/auditoria.php`](app/helpers/auditoria.php) registra los
  borrados/retiros/restauraciones en la tabla `auditoria` (quién, qué,
  cuándo, IP). Se ve en `app/views/auditoria/index.php` (solo admin).
  Si la tabla no existe, `auditar()` no rompe nada.
- **Exportar progreso a CSV**: `app/views/progreso/exportar.php`
  (botón "⬇️ Exportar CSV" en el panel del docente, en la vista global
  y en cada curso). Separador `;` y BOM UTF-8 para que Excel lo abra
  bien.
- **Cobertura de DBA por curso**: `app/views/progreso/cobertura.php`
  (enlace "🗺️ Cobertura de DBA" en `estudiantes/curso.php`,
  `progreso/global.php` y `progreso/panel.php`). Para cada DBA del
  grado del curso, dice si ya se trabajó en alguna sesión de clase de
  ese curso (`Dba::coberturaPorCurso()` cuenta las sesiones por DBA;
  `0` = pendiente), agrupado por materia y periodo. La vista es
  role-aware: el admin elige cualquier curso, el docente solo los
  suyos (con chequeo de permiso en servidor, no solo en el `<select>`).
- **Aviso de sesiones sin progreso**: el panel del docente
  (`dashboard/visualizador.php`) muestra un aviso si tiene sesiones de
  clase sin ningún progreso registrado (`SesionClase::contarSinProgreso()`),
  y `sesiones/mis_sesiones.php` marca cada fila con `Sin progreso` /
  `N registrados` (columna `n_progreso` de `listarPorDocente()`). El
  panel del admin (`progreso/global.php`) ya tenía su propio contador.
- **Importar estudiantes**: `app/views/estudiantes/importar.php`
  (botón "📋 Importar" en `estudiantes/curso.php` y
  `estudiantes/mis_estudiantes.php`). Se pega una lista, una persona
  por línea, `Nombres, Apellidos` (acepta coma o tabulación, para
  pegar desde una hoja de cálculo). Valida línea por línea: si hay
  algún error no inserta **nada** y vuelve a pintar el formulario con
  el texto y la lista de errores; si está todo bien inserta en una
  transacción (`Estudiante::crearVarios()`, todos o ninguno). Igual
  que el alta normal, el docente solo puede importar a un curso suyo.
- **Registrar progreso es idempotente**: `guardar_progreso.php` usa
  `Progreso::registrarOActualizar()` — si un estudiante ya tenía
  progreso en esa sesión, lo **actualiza** en vez de crear una fila
  nueva. Así "Registrar progreso" sirve también para corregir
  (`registrar_progreso.php` llega pre-marcado con lo ya guardado y el
  botón dice "Guardar cambios"), y volver a guardar la misma sesión ya
  no duplica filas. `mis_sesiones.php` muestra "Editar progreso" en las
  sesiones que ya lo tienen.
- **Barra de estados de progreso**: `app/views/progreso/_barra_estados.php`
  es un parcial que pinta una barra 100 % apilada (Logrado / En proceso /
  Requiere apoyo) a partir de un array `$conteo`. Se incluye en
  `progreso/panel.php` y `progreso/global.php`, junto a las tarjetas de
  números. Colores = paleta de estado del proyecto; el color nunca va
  solo (cada segmento y cada entrada de la leyenda llevan etiqueta), y
  `public/css/a11y.css` tiene su bloque de alto contraste.
- **Vista previa de juegos**: `app/views/juegos/*` ahora aceptan
  `id_sesion` ausente o `0` = modo vista previa (el docente abre un
  juego del catálogo para explorarlo, sin sesión de clase detrás).
  Cada motor calcula `$urlVolver` / `$textoVolver` — con sesión vuelve
  a `sesiones/confirmacion.php`, sin ella al catálogo
  (`actividades/index.php`). Botón "▶️ Probar" en `actividades/index.php`
  y en el paso 5 del asistente de sesión.
- **Panel de docentes (admin)**: `app/views/docentes/index.php` lista
  las cuentas; "🔑 Restablecer contraseña" (`restablecer_password.php`)
  genera una temporal `IK-####-####`, la fija con
  `Docente::cambiarContrasena()`, la muestra **una vez** por `flash` y
  lo deja en la bitácora (`auditar("reset_password", ...)`). No opera
  sobre cuentas de administrador (esas usan "Mi perfil" o
  `scripts/reset_admin_password.php`). Enlace desde el panel de admin.
- **Duplicar sesión de clase**: `app/views/sesiones/duplicar.php`
  (botón "🔁 Duplicar" en `sesiones/mis_sesiones.php`). Crea una sesión
  nueva con el mismo docente, curso y DBA en otra fecha y copia las
  filas de `sesionactividad`; **no** copia el progreso
  (`SesionClase::duplicar()`, en transacción). El docente solo puede
  duplicar sus propias sesiones. Al terminar va a `confirmacion.php` de
  la sesión nueva.
- **Runner de migraciones**: `scripts/migrar.php` (+ `scripts/migrar.bat`)
  recorre `database/migrations/*.sql` en orden, aplica las que no estén
  en la tabla `migraciones` y las registra. Tolera los errores de "ya
  existe" (columna / tabla / clave) para poder correrlo también sobre
  una base creada desde `schema.sql`. `--list` muestra el estado sin
  aplicar nada. Se detiene ante cualquier otro error sin marcar esa
  migración como aplicada.
- **Cuentas de docente (admin)**: `app/views/docentes/index.php` lista
  las cuentas; "+ Nuevo docente" (`nuevo.php` / `guardar.php`) las crea
  con una contraseña temporal generada (formato `IK-####-xxxx`, igual
  que el reseteo) que se muestra **una vez** por `flash`; "Editar"
  (`editar.php`) cambia nombres/apellidos/correo (no la contraseña);
  "Eliminar" (`eliminar.php`) las borra (bloqueado si el docente
  todavía tiene cursos o sesiones a su cargo: hay que reasignarlos
  primero, mismo `try/catch` de FK que `cursos/eliminar.php`). El
  registro público (`auth/registro.php` / `guardar_registro.php`) está
  **cerrado**: ya no crea cuentas ni por el formulario ni por POST
  directo, solo explica que hay que pedirle la cuenta al administrador.
  Todo queda en la bitácora (`crear` / `actualizar` / `eliminar`,
  entidad `docente`).
- **Política de contraseña**: `app/helpers/password.php` →
  `password_politica_error()` (mínimo 8 caracteres, nada de comunes ni
  de un solo carácter repetido). La usan el cambio desde "Mi perfil"
  (`docentes/guardar_password.php`) y el chequeo de "contraseña de
  fábrica". Si el **administrador** inicia sesión con una contraseña
  que no cumple (p. ej. `admin123`), `LoginController` marca
  `$_SESSION["forzar_cambio_password"]` y el guard de `app/bootstrap.php`
  lo manda a "Mi perfil" hasta que la cambie (solo se permiten
  `perfil.php` / `guardar_perfil.php` / `guardar_password.php`).
  `guardar_password.php` levanta la marca al guardar una válida.
- **Freno de fuerza bruta en el login**: [`app/helpers/rate_limit.php`](app/helpers/rate_limit.php)
  bloquea una IP durante 15 minutos tras 8 intentos fallidos seguidos
  (tabla `login_bloqueo`, una fila por IP; un inicio de sesión correcto
  limpia el contador). Si la tabla no existe (base sin migrar), falla
  en abierto: nunca deja a nadie fuera por un problema de
  infraestructura.
- **"Olvidé mi contraseña" (docentes)**: enlace en el login →
  `auth/olvide_password.php` (pide el correo) → `auth/olvide_enviar.php`
  marca `docentes.solicitud_password_en = NOW()` (mensaje siempre igual
  exista o no esa cuenta, para no filtrar qué correos están registrados).
  No manda correo: el administrador ve las solicitudes pendientes en
  "Docentes" (aviso + fila resaltada + fecha) y las resuelve con
  "🔑 Restablecer contraseña" (genera la temporal y de paso limpia la
  marca — `Docente::actualizarContrasenaPublica()` siempre pone
  `solicitud_password_en = NULL` al cambiar la contraseña, sea el admin o
  el propio docente desde "Mi perfil").
- **Reporte de curso imprimible**: `app/views/progreso/reporte_curso.php`
  (botón "🖨️ Reporte" en `estudiantes/curso.php`, "🖨️ Reporte de curso"
  en `progreso/panel.php`). Estudiantes, resumen de progreso por DBA
  (estado más reciente de cada estudiante) y contadores, con CSS de
  impresión (`window.print()` → PDF). Role-aware con selector de curso,
  igual que `cobertura.php`.
- **Juegos de arrastrar** (`arrastrar.php`, `ordenar.php`): además del
  arrastre, se puede jugar **tocando la ficha y luego la casilla**, o
  con **teclado** (Tab + Enter/Espacio; Esc cancela la selección).
- Config de BD por entorno: si existe
  `app/config/database.local.php` (git-ignored, plantilla en
  `.example`), sus claves (`host`/`dbname`/`user`/`password`)
  sobrescriben las de `database.php`.
- Respaldo: `scripts/respaldo.bat` (Windows/Task Scheduler) y
  `scripts/respaldo.sh` (cron) hacen `mysqldump` a `backups/` y
  conservan los 14 más recientes.
- Sin dependencias de Composer ni `node_modules`; Bootstrap y jQuery
  están vendorizados en `public/`.
- Sin suite de pruebas todavía.
