/* ==================================================================
   IncluKids · Voz al señalar (solo pantallas de juego)

   Cuando el docente deja el puntero encima de "algo con nombre claro"
   (un botón, una ficha, una zona, una opción, un título...), se lee ese
   nombre en voz alta. Pensado para que el docente le muestre al niño
   qué es cada cosa sin tener que leérselo él.

   - Respeta el toggle "Leer en voz alta" (localStorage inclukids_juegos.voz).
   - NO pisa la voz propia del juego: solo habla si el motor TTS está
     libre (nada sonando ni en cola) y nunca llama a cancel().
   - No se activa dentro de los paneles de accesibilidad / ajustes ni
     sobre los controles del docente (♿, ⚙️, 🖥️ presentación, ⓘ notas).
   - Solo con puntero fino (ratón / lápiz); en táctil no hay "encima".

   Se carga con <script src=".../juegos-voz.js"> en cada vista de
   app/views/juegos/. No lo usan las vistas de gestión.
   ================================================================== */
(function () {
    "use strict";

    if (!("speechSynthesis" in window)) return;

    var CLAVE_PREFS = "inclukids_juegos";
    var ESPERA_MS = 220;   // el puntero debe quedarse quieto encima este tiempo

    var SELECTOR_LEIBLE = [
        "button", "a[href]", "[role='button']",
        ".pieza", ".ficha", ".carta", ".opcion", ".unir-item",
        ".zona", ".slot",
        ".instruccion", ".mensaje",
        "h1", "h2", "h3"
    ].join(",");

    var SELECTOR_EXCLUIDO = [
        ".a11y-fab", ".a11y-panel", ".a11y-presenta",
        ".juego-ajustes",
        "#notas-docente", "#btnNotas", ".btn-notas",
        "[data-sin-voz]"
    ].join(",");

    function vozActivada() {
        try {
            var prefs = JSON.parse(localStorage.getItem(CLAVE_PREFS) || "{}");
            return prefs.voz !== false;   // por defecto: activada
        } catch (e) {
            return true;
        }
    }

    // Quita emojis, pictogramas y símbolos: la voz debe leer SOLO el
    // texto. (Bloques de emoji, dingbats, flechas, banderas, variation
    // selectors y el zero-width joiner.)
    var RE_EMOJI = /[\u{1F000}-\u{1FAFF}\u{1F1E6}-\u{1F1FF}\u{2190}-\u{21FF}\u{2300}-\u{27BF}\u{2B00}-\u{2BFF}\u{2000}-\u{206F}\u{FE00}-\u{FE0F}\u{200D}\u{20E3}\u{E000}-\u{F8FF}]/gu;

    function soloTexto(t) {
        return (t || "").replace(RE_EMOJI, "").replace(/\s+/g, " ").trim();
    }

    function nombreDe(el) {
        var aria = el.getAttribute && el.getAttribute("aria-label");
        if (aria && soloTexto(aria)) return soloTexto(aria);

        var etiqueta = el.querySelector && el.querySelector(
            ".pieza-etiqueta, .ficha-etiqueta, .zona-etiqueta, .carta-etiqueta, .opcion-etiqueta"
        );
        if (etiqueta && soloTexto(etiqueta.textContent)) return soloTexto(etiqueta.textContent);

        var img = el.querySelector && el.querySelector("img[alt]");
        if (img && soloTexto(img.getAttribute("alt"))) return soloTexto(img.getAttribute("alt"));
        if (el.tagName === "IMG" && soloTexto(el.getAttribute("alt"))) {
            return soloTexto(el.getAttribute("alt"));
        }

        var texto = soloTexto(el.textContent);
        if (!texto || !/[\p{L}\p{N}]/u.test(texto)) return "";
        return texto.length > 140 ? texto.slice(0, 140) : texto;
    }

    function hablar(texto) {
        try {
            // Clave: NO interrumpir lo que el juego esté diciendo.
            if (window.speechSynthesis.speaking || window.speechSynthesis.pending) return;
            var u = new SpeechSynthesisUtterance(texto);
            u.lang = "es-ES";
            u.rate = 0.95;
            window.speechSynthesis.speak(u);
        } catch (e) { /* sin voz: no pasa nada */ }
    }

    var temporizador = null;
    var elActual = null;

    document.addEventListener("pointerover", function (e) {
        if (e.pointerType === "touch") return;

        var destino = e.target;
        if (!destino || !destino.closest) return;

        var el = destino.closest(SELECTOR_LEIBLE);
        if (el && (destino.closest(SELECTOR_EXCLUIDO) || el.closest(SELECTOR_EXCLUIDO))) {
            el = null;
        }

        if (el === elActual) return;
        elActual = el;
        clearTimeout(temporizador);
        if (!el) return;

        temporizador = setTimeout(function () {
            if (elActual !== el || !vozActivada()) return;
            var texto = nombreDe(el);
            if (texto) hablar(texto);
        }, ESPERA_MS);
    });
})();
