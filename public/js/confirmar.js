/* ==================================================================
   IncluKids · Confirmación de borrado propia de la app
   ------------------------------------------------------------------
   Sustituye al confirm() nativo del navegador por un cuadro con el
   estilo de IncluKids.

   Uso: en el <form> que borra algo, en vez de
        onsubmit="return confirm('¿Eliminar...?')"
   se pone
        data-confirmar="¿Eliminar...?"
   y, opcional,
        data-confirmar-boton="Eliminar"   (texto del botón rojo)
        data-confirmar-titulo="¿Eliminar?" (encabezado)

   Se carga en todas las vistas de gestión desde render_head()
   (app/helpers/vista.php). Inyecta su propio <style> y su propio DOM,
   no necesita CSS aparte.
   ================================================================== */
(function () {
    "use strict";

    var CSS =
        '.confirmar-modal{position:fixed;inset:0;z-index:1080;display:flex;' +
            'align-items:center;justify-content:center;padding:20px;}' +
        '.confirmar-modal[hidden]{display:none;}' +
        '.confirmar-modal__fondo{position:absolute;inset:0;background:rgba(20,28,34,.55);' +
            'backdrop-filter:blur(1px);}' +
        '.confirmar-modal__caja{position:relative;width:100%;max-width:420px;' +
            'background:var(--papel-tarjeta,#fff);color:var(--tinta,#24303A);' +
            'border:1px solid var(--borde,#D7DED9);border-radius:16px;' +
            'box-shadow:0 24px 60px rgba(20,28,34,.32);padding:24px 24px 20px;' +
            'animation:confirmar-entra .14s ease-out;}' +
        '@keyframes confirmar-entra{from{opacity:0;transform:translateY(8px) scale(.98);}' +
            'to{opacity:1;transform:none;}}' +
        '.confirmar-modal__titulo{margin:0 0 8px;font-size:20px;font-weight:700;' +
            'color:var(--petroleo-oscuro,#1D4448);}' +
        '.confirmar-modal__texto{margin:0 0 20px;font-size:15px;line-height:1.5;' +
            'color:var(--texto-suave,#5B6B66);}' +
        '.confirmar-modal__acciones{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;}' +
        '.confirmar-modal__btn{font-family:inherit;font-size:15px;font-weight:600;' +
            'min-height:44px;padding:10px 20px;border-radius:999px;cursor:pointer;' +
            'border:2px solid transparent;transition:transform .1s ease,box-shadow .15s ease;}' +
        '.confirmar-modal__btn:active{transform:translateY(1px);}' +
        '.confirmar-modal__btn:focus-visible{outline:3px solid var(--petroleo,#2B5F63);outline-offset:2px;}' +
        '.confirmar-modal__btn--cancelar{background:#fff;color:var(--tinta,#24303A);' +
            'border-color:var(--borde,#D7DED9);}' +
        '.confirmar-modal__btn--cancelar:hover{border-color:var(--tinta,#24303A);}' +
        '.confirmar-modal__btn--eliminar{background:var(--peligro,#B24632);color:#fff;' +
            'border-color:var(--peligro,#B24632);}' +
        '.confirmar-modal__btn--eliminar:hover{box-shadow:0 6px 16px rgba(178,70,50,.4);}' +
        'body.confirmar-abierto{overflow:hidden;}' +
        '@media (prefers-reduced-motion: reduce){.confirmar-modal__caja{animation:none;}' +
            '.confirmar-modal__btn{transition:none;}}';

    var modal, caja, textoEl, tituloEl, btnSi, btnNo, formPendiente, focoPrevio;

    function construir() {
        var style = document.createElement("style");
        style.textContent = CSS;
        document.head.appendChild(style);

        modal = document.createElement("div");
        modal.className = "confirmar-modal";
        modal.hidden = true;
        modal.innerHTML =
            '<div class="confirmar-modal__fondo" data-cerrar></div>' +
            '<div class="confirmar-modal__caja" role="alertdialog" aria-modal="true"' +
                ' aria-labelledby="confirmarTitulo" aria-describedby="confirmarTexto">' +
                '<h2 class="confirmar-modal__titulo" id="confirmarTitulo">¿Eliminar?</h2>' +
                '<p class="confirmar-modal__texto" id="confirmarTexto"></p>' +
                '<div class="confirmar-modal__acciones">' +
                    '<button type="button" class="confirmar-modal__btn confirmar-modal__btn--cancelar">Cancelar</button>' +
                    '<button type="button" class="confirmar-modal__btn confirmar-modal__btn--eliminar">Eliminar</button>' +
                '</div>' +
            '</div>';
        document.body.appendChild(modal);

        caja = modal.querySelector(".confirmar-modal__caja");
        textoEl = modal.querySelector("#confirmarTexto");
        tituloEl = modal.querySelector("#confirmarTitulo");
        btnNo = modal.querySelector(".confirmar-modal__btn--cancelar");
        btnSi = modal.querySelector(".confirmar-modal__btn--eliminar");

        btnSi.addEventListener("click", aceptar);
        btnNo.addEventListener("click", cerrar);
        modal.querySelector(".confirmar-modal__fondo").addEventListener("click", cerrar);
        document.addEventListener("keydown", function (e) {
            if (modal.hidden) return;
            if (e.key === "Escape") { e.preventDefault(); cerrar(); }
            else if (e.key === "Tab") atraparFoco(e);
        });
    }

    function abrir(form) {
        formPendiente = form;
        textoEl.textContent = form.getAttribute("data-confirmar") ||
            "Esta acción no se puede deshacer.";
        tituloEl.textContent = form.getAttribute("data-confirmar-titulo") || "¿Eliminar?";
        btnSi.textContent = form.getAttribute("data-confirmar-boton") || "Eliminar";
        focoPrevio = document.activeElement;
        modal.hidden = false;
        document.body.classList.add("confirmar-abierto");
        // El foco arranca en "Cancelar": es una acción destructiva, no
        // conviene que un Enter de más la dispare.
        btnNo.focus();
    }

    function cerrar() {
        if (!modal || modal.hidden) return;
        modal.hidden = true;
        document.body.classList.remove("confirmar-abierto");
        formPendiente = null;
        if (focoPrevio && focoPrevio.focus) focoPrevio.focus();
    }

    function aceptar() {
        var form = formPendiente;
        cerrar();
        if (!form) return;
        form.dataset.confirmado = "1";   // que el interceptor deje pasar este envío
        if (form.requestSubmit) form.requestSubmit();
        else form.submit();
    }

    function atraparFoco(e) {
        var botones = caja.querySelectorAll("button");
        var primero = botones[0], ultimo = botones[botones.length - 1];
        if (e.shiftKey && document.activeElement === primero) {
            e.preventDefault(); ultimo.focus();
        } else if (!e.shiftKey && document.activeElement === ultimo) {
            e.preventDefault(); primero.focus();
        }
    }

    document.addEventListener("submit", function (e) {
        var form = e.target;
        if (!form || !form.matches || !form.matches("[data-confirmar]")) return;
        if (form.dataset.confirmado === "1") {   // ya se confirmó: seguir
            delete form.dataset.confirmado;
            return;
        }
        e.preventDefault();
        if (!modal) construir();
        abrir(form);
    }, true);
})();
