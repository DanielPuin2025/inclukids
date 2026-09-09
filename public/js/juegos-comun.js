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
        // Renglón opcional con un dato objetivo para el docente
        // ("N preguntas · ayudas: M"). Discreto, nunca marca nada como mal.
        var detalle = opciones.detalle
            ? '<p class="juego-final__detalle" style="font-size:19px;font-weight:600;opacity:.68;margin:6px 0 0;">'
              + escapar(opciones.detalle) + '</p>'
            : '';

        contenedor.innerHTML =
            '<div class="juego-final__emoji" aria-hidden="true">🌟</div>' +
            '<h2>' + escapar(titulo) + '</h2>' +
            detalle +
            '<p>Lo hiciste muy bien.</p>' +
            '<a class="btn-volver" href="' + escaparAtributo(urlVolver) + '">' +
            escapar(textoVolver) + '</a>';
        contenedor.hidden = false;

        var boton = contenedor.querySelector(".btn-volver");
        if (boton) foco(boton);

        // El confeti y el sonido de cierre son solo el "premio extra" del
        // modo estímulo (opt-in por niño) — en el modo base la pantalla
        // final sigue siendo la misma de siempre, sin sorpresas nuevas.
        if (prefs.estimulo) {
            confeti();
            sonido("final");
        }
    }

    /* ---------- Confeti (decorativo, no bloquea nada) ---------- */
    var COLORES_CONFETI = ["#2F6E73", "#3C7A4E", "#D98A3D", "#B4534A", "#6B5B95"];

    function confeti(cantidad) {
        cantidad = cantidad || 26;
        // Si el sistema/niño pide menos movimiento, el CSS global ya
        // reduce cualquier animación a 0 — igual evitamos crear y
        // desechar 26 nodos de más quie no se van a ver.
        if (!prefs.movimiento) return;

        var capa = document.createElement("div");
        capa.className = "j-confeti";
        capa.setAttribute("aria-hidden", "true");

        for (var i = 0; i < cantidad; i++) {
            var pieza = document.createElement("div");
            pieza.className = "j-confeti__pieza";
            var izquierda = Math.random() * 100;
            var tam = 6 + Math.random() * 8;
            var duracion = 1600 + Math.random() * 1400;
            var retraso = Math.random() * 400;
            var deriva = (Math.random() - 0.5) * 160;
            var giro = 180 + Math.random() * 360;
            pieza.style.left = izquierda + "vw";
            pieza.style.setProperty("--tam", tam + "px");
            pieza.style.setProperty("--duracion", duracion + "ms");
            pieza.style.setProperty("--retraso", retraso + "ms");
            pieza.style.setProperty("--deriva", deriva + "px");
            pieza.style.setProperty("--giro", giro + "deg");
            pieza.style.setProperty("--color", COLORES_CONFETI[i % COLORES_CONFETI.length]);
            capa.appendChild(pieza);
        }

        document.body.appendChild(capa);
        setTimeout(function () {
            if (capa.parentNode) capa.parentNode.removeChild(capa);
        }, 3200);
    }

    /* ---------- Sonido (tono sintetizado, sin archivos de audio) ----------
       Solo se usa como refuerzo del modo estímulo (nunca en el modo base).
       Un acorde corto y suave, no un "beep" agudo. */
    var contextoAudio = null;
    function tonoSuave(frecuencias, duracionMs) {
        try {
            if (!contextoAudio) {
                var AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                contextoAudio = new AudioCtx();
            }
            if (contextoAudio.state === "suspended") contextoAudio.resume();

            var ahora = contextoAudio.currentTime;
            frecuencias.forEach(function (frecuencia, i) {
                var osc = contextoAudio.createOscillator();
                var ganancia = contextoAudio.createGain();
                osc.type = "sine";
                osc.frequency.value = frecuencia;
                ganancia.gain.setValueAtTime(0, ahora);
                ganancia.gain.linearRampToValueAtTime(0.06, ahora + 0.02);
                ganancia.gain.exponentialRampToValueAtTime(0.0001, ahora + duracionMs / 1000);
                osc.connect(ganancia);
                ganancia.connect(contextoAudio.destination);
                osc.start(ahora + i * 0.09);
                osc.stop(ahora + duracionMs / 1000 + i * 0.09);
            });
        } catch (e) {
            /* Web Audio no disponible: no pasa nada, el juego sigue igual */
        }
    }
    function sonido(tipo) {
        if (!prefs.estimulo) return;
        if (tipo === "acierto") tonoSuave([523.25, 659.25], 260); // Do-Mi
        else if (tipo === "final") tonoSuave([523.25, 659.25, 783.99], 500); // Do-Mi-Sol
    }

    /* ---------- Sonido de toque ----------
       Un "tick" muy corto y suave en cuanto se toca cualquier botón u
       opción, desde el momento en que se entra al juego. A diferencia
       de sonido(), este NO depende de "Más color y premios": es
       retroalimentación básica de que el toque se registró, no un
       premio — así que suena siempre. */
    function tonoClic() {
        tonoSuave([700], 90);
    }
    function activarSonidoDeToque() {
        // Fase de captura: se dispara ANTES que los manejadores propios
        // de cada motor de juego (drag, opciones, etc.), así que nunca
        // se pierde aunque ese manejador detenga la propagación luego.
        document.addEventListener("pointerdown", function (e) {
            if (e.target.closest && e.target.closest(
                "button, .opcion, [role='button'], a.btn-volver"
            )) {
                tonoClic();
            }
        }, true);
    }

    /* ---------- Racha de aciertos seguidos (solo modo estímulo) ---------- */
    var rachaActual = 0;
    var elementoRacha = null;
    var temporizadorRacha = null;
    var HITOS_RACHA = [3, 5, 8];

    function marcarAcierto() {
        rachaActual++;
        sonido("acierto");
        if (!prefs.estimulo) return;
        if (HITOS_RACHA.indexOf(rachaActual) !== -1) {
            mostrarRacha(rachaActual + " seguidas 🔥");
        }
    }
    function marcarError() {
        rachaActual = 0;
    }
    function mostrarRacha(texto) {
        if (!elementoRacha) {
            elementoRacha = document.createElement("div");
            elementoRacha.className = "j-racha";
            elementoRacha.setAttribute("aria-hidden", "true");
            document.body.appendChild(elementoRacha);
        }
        elementoRacha.textContent = texto;
        elementoRacha.classList.add("visible");
        clearTimeout(temporizadorRacha);
        temporizadorRacha = setTimeout(function () {
            elementoRacha.classList.remove("visible");
        }, 1600);
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
        activarSonidoDeToque();
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
        foco: foco,
        confeti: confeti,
        sonido: sonido,
        marcarAcierto: marcarAcierto,
        marcarError: marcarError
    };
})();
