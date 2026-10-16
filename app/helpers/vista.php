<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

/**
 * Cabecera HTML común de las vistas de gestión (docente y admin).
 *
 * Sustituye al bloque `<!DOCTYPE html> … </head>` que estaba copiado
 * y pegado, idéntico, en ~30 vistas. Además centraliza en ASSET_VER
 * el número de versión de a11y.css / a11y.js: antes había que subirlo
 * a mano en cada archivo al cambiar esos recursos (y el README avisaba
 * de que era fácil olvidarlo).
 *
 * Uso dentro de una vista, en el punto donde antes iba `<!DOCTYPE html>`:
 *
 *     <?php render_head("Cursos"); ?>
 *     <body>
 *     ...
 *
 *     <?php render_head("Estudiantes", ["css" => ["panel.css"]]); ?>
 *
 * Opciones:
 *   css        => hojas extra de public/css/ (además de bootstrap,
 *                 inclukids y a11y). Un nombre suelto ("panel.css")
 *                 lleva ?v=ASSET_VER.
 *   icon       => favicon del proyecto. true por defecto (igual que en
 *                 login y juegos); pasar false para omitirlo.
 *   head_extra => HTML crudo que se inyecta justo antes de </head>
 *                 (p. ej. un bloque <style> propio de esa vista).
 *
 * NO lo usan las pantallas de juego (tienen su propio <head> con
 * fuentes de Google y estilos en línea) ni las de login/registro.
 */

const ASSET_VER = 10;

/** URL de un recurso de public/ con el parámetro de versión pegado. */
function asset_url(string $rel): string
{
    $sep = strpos($rel, "?") === false ? "?" : "&";
    return "/IncluKids/public/" . $rel . $sep . "v=" . ASSET_VER;
}

function render_head(string $titulo, array $opts = []): void
{
    $cssExtra = $opts["css"] ?? [];
    $conIcono = $opts["icon"] ?? true;
    $headExtra = $opts["head_extra"] ?? "";

    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"es\">\n";
    echo "<head>\n";
    echo "<meta charset=\"UTF-8\">\n";
    echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
    echo "<title>" . htmlspecialchars($titulo) . "</title>\n";

    if ($conIcono) {
        echo "<link rel=\"icon\" href=\"/IncluKids/public/img/logo-inclukids.jpg\">\n";
    }

    echo "<link href=\"/IncluKids/public/css/bootstrap.min.css\" rel=\"stylesheet\">\n";
    echo "<link href=\"/IncluKids/public/css/inclukids.css\" rel=\"stylesheet\">\n";
    echo "<link href=\"" . asset_url("css/a11y.css") . "\" rel=\"stylesheet\">\n";

    foreach ($cssExtra as $hoja) {
        $href = strpos($hoja, "?") === false
            ? asset_url("css/" . $hoja)
            : "/IncluKids/public/css/" . $hoja;
        echo "<link href=\"" . htmlspecialchars($href) . "\" rel=\"stylesheet\">\n";
    }

    echo "<script src=\"" . asset_url("js/a11y.js") . "\" defer></script>\n";
    echo "<script src=\"" . asset_url("js/confirmar.js") . "\" defer></script>\n";
    echo "<script src=\"" . asset_url("js/tabla-filtro.js") . "\" defer></script>\n";
    echo "<script src=\"" . asset_url("js/nav.js") . "\" defer></script>\n";
    echo "<script src=\"" . asset_url("js/creditos.js") . "\" defer></script>\n";

    if ($headExtra !== "") {
        echo $headExtra . "\n";
    }

    echo "</head>\n";
}
