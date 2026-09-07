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

> **Instalación nueva:** `schema.sql` ya incluye todo, no hace falta
> tocar `database/migrations/`.
> **Base que ya existía** (creada antes de que hubiera `schema.sql`):
> aplica las migraciones que te falten, en orden de fecha, p. ej.
> `mysql -u root inclukids < database/migrations/2026_09_07_crear_login_bloqueo.sql`.

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

> Cámbiala al primer inicio de sesión (menú de la cuenta → Mi perfil).
> El registro público (`/IncluKids/app/views/auth/registro.php`) solo
> crea cuentas de rol **docente**.

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
dashboard.php   (obsoleto: stub antiguo; el panel real es
                 app/views/dashboard/home.php)
logout.php
```

## Roles y navegación

- **Administrador** → `app/views/dashboard/home.php`: vista global
  (estudiantes, cursos, DBA, actividades, sesiones y progreso de todos
  los docentes), más un "Panel de un docente" para ver su propia cuenta
  como la vería un profesor.
- **Docente** → `app/views/dashboard/visualizador.php`: solo sus cursos,
  sus estudiantes y su progreso. Flujo de clase:
  `crear sesión → curso → materia → periodo → DBA → actividades →
  registrar progreso`.

## Accesibilidad

Menú de la cuenta → **Accesibilidad**: tamaño de texto (A / A+ / A++),
alto contraste y reducir movimiento. Las preferencias se guardan en
`localStorage` (clave `inclukids_a11y`). *Pendiente:* el panel solo está
en los paneles de administrador y docente; falta llevarlo a las
pantallas de juego y al login.

## Notas de desarrollo

- Sin framework: enrutamiento por archivo (cada vista es un endpoint).
  Toda vista de gestión empieza con
  `require __DIR__ . "/../../bootstrap.php";` (y, si es solo de admin,
  `requireAdministrador("../dashboard/visualizador.php");` justo
  después). `app/bootstrap.php` hace `session_start()`, el cierre por
  inactividad y el guard de "hay que estar logueado", y deja cargados
  los helpers de CSRF y de rol. Las pantallas de juego NO lo usan.
- Sin dependencias de Composer ni `node_modules`; Bootstrap y jQuery
  están vendorizados en `public/`.
- Sin suite de pruebas todavía.
