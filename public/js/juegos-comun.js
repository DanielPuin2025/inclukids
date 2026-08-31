/* ==================================================================
   IncluKids - Utilidades compartidas de las pantallas de juego
   Expone window.IncluKidsJuegos. Sin dependencias.

   Principios (ver public/css/juegos.css):
     - La voz NUNCA se dispara sola: solo cuando el niño pulsa 🔊.
     - Preferencias por niño en localStorage: más estímulo, leer en
       voz alta, menos movimiento.
     - Estructura de progreso ("Paso X de Y") y pantalla final
       consistentes en los 4 juegos.
   ================================================================== */
(function () {
    "use strict";

    var CLAVE = "inclukids_juegos";

    var prefsPorDefecto = {
        estimulo: false,
        voz: true,
        // "movimiento: true" = se permiten animaciones. Si el sistema pide
        // reducir movimiento, arrancamos en false.
        movimiento: !(window.matchMedia &&
            window.matchMedia("(prefers-reduced-motion: reduce)").matches)
    };

    function cargarPrefs() {
        try {
            var guardado = JSON.parse(localStorage.getItem(CLAVE)) || {};
            return Object.assign({}, prefsPorDefecto, guardado);
        } catch (e) {
            return Object.assign({}, prefsPorDefecto);
        }
    }

    function guardarPrefs(prefs) {
        try {
            localStorage.setItem(CLAVE, JSON.stringify(prefs));
        } catch (e) {
            /* modo privado / almacenamiento bloqueado: no se recuerda */
        }
    }

    var prefs = cargarPrefs();

    function aplicarPrefsAlBody() {
        document.body.classList.toggle("modo-estimulo", !!prefs.estimulo);
        document.body.classList.toggle("menos-movimiento", !prefs.movimiento);
    }

    /* ---------- Voz (solo bajo demanda) ---------- */
    function hablar(texto) {
        if (!prefs.voz) return;
        if (!("speechSynthesis" in window) || !texto) return;
        window.speechSynthesis.cancel();
        var u = new SpeechSynthesisUtterance(String(texto));
        u.lang = "es-ES";
        u.rate = 0.9;
        window.speechSynthesis.speak(u);
    }

    function callarVoz() {
        if ("speechSynthesis" in window) window.speechSynthesis.cancel();
    }

    /* ---------- Progreso: "Paso X de Y" + puntos ---------- */
    function barraProgreso(contenedor, total, indice) {
        if (!contenedor) return;
        contenedor.innerHTML = "";
        for (var i = 0; i < total; i++) {
            var punto = document.createElement("div");
            punto.className = "juego-progreso__punto" +
                (i < indice ? " hecho" : (i === indice ? " activo" : ""));
            contenedor.appendChild(punto);
        }
        contenedor.setAttribute("role", "img");
        contenedor.setAttribute("aria-label",
            "Paso " + Math.min(indice + 1, total) + " de " + total);

        var etiqueta = document.querySelector("[data-juego-paso]");
        if (etiqueta) {
            etiqueta.textContent = "Paso " + Math.min(indice + 1, total) +
                " de " + total;
        }
    }

    /* ---------- Pantalla final consistente ---------- */
    function pantallaFinal(contenedor, opciones) {
        if (!contenedor) return;
        opciones = opciones || {};
        var titulo = opciones.titulo || "¡Actividad terminada!";
        var urlVolver = opciones.urlVolver || "#";
        var textoVolver = opciones.textoVolver || "Volver a la sesión";

        contenedor.innerHTML =
            '<div class="juego-final__emoji" aria-hidden="true">🌟</div>' +
            '<h2>' + escapar(titulo) + '</h2>' +
            '<p>Lo hiciste muy bien.</p>' +
            '<a class="btn-volver" href="' + escaparAtributo(urlVolver) + '">' +
            escapar(textoVolver) + '</a>';
        contenedor.hidden = false;

        var boton = contenedor.querySelector(".btn-volver");
        if (boton) foco(boton);
    }

    /* ---------- Foco seguro ---------- */
    function foco(el) {
        if (!el) return;
        try {
            el.focus({ preventScroll: false });
        } catch (e) {
            el.focus();
        }
    }

    /* ---------- Engranaje de ajustes ---------- */
    function montarAjustes() {
        if (document.querySelector(".juego-ajustes")) return;

        var cont = document.createElement("div");
        cont.className = "juego-ajustes";
        cont.innerHTML =
            '<button type="button" class="juego-ajustes__boton" aria-expanded="false" aria-label="Ajustes">⚙️</button>' +
            '<div class="juego-ajustes__panel" hidden>' +
                '<p class="juego-ajustes__titulo">Ajustes</p>' +
                fila("estimulo", "Más color y premios") +
                fila("voz", "Leer en voz alta") +
                fila("movimiento", "Permitir animaciones") +
            '</div>';
        document.body.appendChild(cont);

        var boton = cont.querySelector(".juego-ajustes__boton");
        var panel = cont.querySelector(".juego-ajustes__panel");

        boton.addEventListener("click", function (e) {
            e.stopPropagation();
            var abierto = panel.hidden;
            panel.hidden = !abierto;
            boton.setAttribute("aria-expanded", abierto ? "true" : "false");
        });
        document.addEventListener("click", function (e) {
            if (!cont.contains(e.target)) {
                panel.hidden = true;
                boton.setAttribute("aria-expanded", "false");
            }
        });
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                panel.hidden = true;
                boton.setAttribute("aria-expanded", "false");
            }
        });

        Array.prototype.forEach.call(
            panel.querySelectorAll('input[type="checkbox"]'),
            function (input) {
                input.checked = !!prefs[input.dataset.pref];
                input.addEventListener("change", function () {
                    prefs[input.dataset.pref] = input.checked;
                    guardarPrefs(prefs);
                    aplicarPrefsAlBody();
                    if (input.dataset.pref === "voz" && !input.checked) callarVoz();
                });
            }
        );
    }

    function fila(clave, texto) {
        return '<label class="juego-ajustes__fila">' +
            '<span>' + texto + '</span>' +
            '<input type="checkbox" data-pref="' + clave + '">' +
            '<span class="juego-switch" aria-hidden="true"></span>' +
            '</label>';
    }

    /* ---------- Helpers de escape ---------- */
    function escapar(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
        });
    }
    function escaparAtributo(s) {
        return String(s).replace(/["'<>]/g, function (c) {
            return { '"': "%22", "'": "%27", "<": "%3C", ">": "%3E" }[c];
        });
    }

    /* ---------- Arranque ---------- */
    function iniciar() {
        document.body.classList.add("juego");
        aplicarPrefsAlBody();
        montarAjustes();
    }
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", iniciar);
    } else {
        iniciar();
    }

    window.IncluKidsJuegos = {
        prefs: prefs,
        hablar: hablar,
        callarVoz: callarVoz,
        barraProgreso: barraProgreso,
        pantallaFinal: pantallaFinal,
        montarAjustes: montarAjustes,
        foco: foco
    };
})();
