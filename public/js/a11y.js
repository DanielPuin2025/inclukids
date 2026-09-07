/* ==================================================================
   IncluKids · Accesibilidad compartida (sin dependencias)
   ------------------------------------------------------------------
   Inyecta un botón flotante "♿" con tres ajustes y los aplica en
   cuanto carga la página:

     - Tamaño de texto:  normal / grande / extra
     - Alto contraste
     - Reducir movimiento

   Se guardan en localStorage bajo "inclukids_a11y", la MISMA clave y
   las MISMAS clases (html.texto-grande / html.texto-extra /
   body.alto-contraste / body.reducir-movimiento) que ya usa el menú
   de accesibilidad de los paneles de administrador y docente, para
   que la preferencia viaje entre esas pantallas y los juegos / login.

   En las pantallas de juego también sincroniza "reducir movimiento"
   con la clave propia de los juegos ("inclukids_juegos").
   ================================================================== */
(function () {
    "use strict";

    if (window.__inclukidsA11y) return;
    window.__inclukidsA11y = true;

    var CLAVE = "inclukids_a11y";
    var CLAVE_JUEGOS = "inclukids_juegos";

    var prefiereMenosMovimiento = !!(window.matchMedia &&
        window.matchMedia("(prefers-reduced-motion: reduce)").matches);

    function cargar() {
        var base = { tamano: "normal", contraste: false, movimiento: prefiereMenosMovimiento };
        try {
            var g = JSON.parse(localStorage.getItem(CLAVE)) || {};
            if (g.tamano === "grande" || g.tamano === "extra" || g.tamano === "normal") base.tamano = g.tamano;
            if (typeof g.contraste === "boolean") base.contraste = g.contraste;
            if (typeof g.movimiento === "boolean") base.movimiento = g.movimiento;
        } catch (e) { /* almacenamiento bloqueado: se usan los valores por defecto */ }
        return base;
    }

    function guardar(prefs) {
        try {
            localStorage.setItem(CLAVE, JSON.stringify(prefs));
        } catch (e) { /* modo privado: no se recuerda entre visitas */ }
    }

    var prefs = cargar();

    function esPantallaDeJuego() {
        return !!document.querySelector(
            ".instruccion, .juego-instruccion, .barra-progreso, .juego-progreso, .piezas, .cartas, .opciones, .juego-encabezado"
        );
    }

    function sincronizarConJuegos() {
        // La clave de los juegos usa "movimiento" con el sentido INVERSO
        // (true = se permiten animaciones). Aquí "movimiento" significa
        // "reducir movimiento", así que se invierte al sincronizar.
        try {
            var j = JSON.parse(localStorage.getItem(CLAVE_JUEGOS)) || {};
            j.movimiento = !prefs.movimiento;
            localStorage.setItem(CLAVE_JUEGOS, JSON.stringify(j));
        } catch (e) { /* sin almacenamiento: los juegos usarán su propio defecto */ }

        if (document.body) {
            document.body.classList.toggle("menos-movimiento", prefs.movimiento);
        }
    }

    function aplicar() {
        var raiz = document.documentElement;
        raiz.classList.toggle("texto-grande", prefs.tamano === "grande");
        raiz.classList.toggle("texto-extra", prefs.tamano === "extra");

        if (document.body) {
            document.body.classList.toggle("alto-contraste", !!prefs.contraste);
            document.body.classList.toggle("reducir-movimiento", !!prefs.movimiento);
            if (esPantallaDeJuego()) document.body.classList.add("a11y-juego");
        }
        sincronizarConJuegos();
    }

    // El tamaño de texto se puede aplicar ya mismo sobre <html> (menos
    // parpadeo). El resto necesita <body>.
    aplicar();

    function construirWidget() {
        if (!document.body || document.querySelector(".a11y-fab")) return;

        var fab = document.createElement("button");
        fab.type = "button";
        fab.className = "a11y-fab";
        fab.setAttribute("aria-haspopup", "true");
        fab.setAttribute("aria-expanded", "false");
        fab.setAttribute("aria-label", "Accesibilidad");
        fab.textContent = "♿"; // ♿

        var panel = document.createElement("div");
        panel.className = "a11y-panel";
        panel.hidden = true;
        panel.setAttribute("role", "dialog");
        panel.setAttribute("aria-label", "Ajustes de accesibilidad");
        panel.innerHTML =
            '<p class="a11y-panel__titulo">Accesibilidad</p>' +
            '<div class="a11y-fila">' +
                '<span id="a11y-lbl-tamano">Tamaño de texto</span>' +
                '<span class="a11y-tamanos" role="group" aria-labelledby="a11y-lbl-tamano">' +
                    '<button type="button" data-tamano="normal" aria-pressed="false">A</button>' +
                    '<button type="button" data-tamano="grande" aria-pressed="false">A+</button>' +
                    '<button type="button" data-tamano="extra" aria-pressed="false">A++</button>' +
                '</span>' +
            '</div>' +
            '<label class="a11y-fila">' +
                '<span>Alto contraste</span>' +
                '<span class="a11y-switch">' +
                    '<input type="checkbox" data-pref="contraste">' +
                    '<span class="a11y-switch__visual" aria-hidden="true"></span>' +
                '</span>' +
            '</label>' +
            '<label class="a11y-fila">' +
                '<span>Reducir movimiento</span>' +
                '<span class="a11y-switch">' +
                    '<input type="checkbox" data-pref="movimiento">' +
                    '<span class="a11y-switch__visual" aria-hidden="true"></span>' +
                '</span>' +
            '</label>';

        document.body.appendChild(fab);
        document.body.appendChild(panel);

        function sincronizarControles() {
            var botones = panel.querySelectorAll(".a11y-tamanos button");
            Array.prototype.forEach.call(botones, function (b) {
                b.setAttribute("aria-pressed", b.dataset.tamano === prefs.tamano ? "true" : "false");
            });
            panel.querySelector('[data-pref="contraste"]').checked = !!prefs.contraste;
            panel.querySelector('[data-pref="movimiento"]').checked = !!prefs.movimiento;
        }

        function abrir(v) {
            panel.hidden = !v;
            fab.setAttribute("aria-expanded", v ? "true" : "false");
        }

        fab.addEventListener("click", function (e) {
            e.stopPropagation();
            abrir(panel.hidden);
        });
        document.addEventListener("click", function (e) {
            if (!panel.hidden && !panel.contains(e.target) && e.target !== fab) abrir(false);
        });
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape" && !panel.hidden) { abrir(false); fab.focus(); }
        });

        Array.prototype.forEach.call(panel.querySelectorAll(".a11y-tamanos button"), function (b) {
            b.addEventListener("click", function () {
                prefs.tamano = b.dataset.tamano;
                guardar(prefs);
                aplicar();
                sincronizarControles();
            });
        });
        Array.prototype.forEach.call(panel.querySelectorAll('input[type="checkbox"]'), function (input) {
            input.addEventListener("change", function () {
                prefs[input.dataset.pref] = input.checked;
                guardar(prefs);
                aplicar();
            });
        });

        sincronizarControles();
    }

    function iniciar() {
        aplicar();
        construirWidget();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", iniciar);
    } else {
        iniciar();
    }
})();
