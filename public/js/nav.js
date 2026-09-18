/* ==================================================================
   IncluKids · Barra de navegación fija (Atrás / Inicio)
   ------------------------------------------------------------------
   Inyecta arriba de cada vista de gestión una barra pegajosa con dos
   botones grandes y visibles:
     ←  Atrás   -> history.back()
     🏠 Inicio  -> el panel (home.php se redirige solo al panel del
                   docente si no eres admin)

   Se carga desde render_head() (app/helpers/vista.php). No aparece en
   los propios paneles de inicio ni en las pantallas de juego (esas no
   usan render_head).
   ================================================================== */
(function () {
    "use strict";

    // En los paneles de inicio no tiene sentido "Inicio"/"Atrás".
    if (/\/dashboard\/(home|visualizador)\.php/.test(location.pathname)) return;
    if (document.querySelector(".nav-fija")) return;

    var INICIO = "/IncluKids/app/views/dashboard/home.php";

    var css =
        ".nav-fija{position:sticky;top:0;z-index:1050;display:flex;gap:10px;" +
            "align-items:center;padding:10px 14px;margin-bottom:4px;" +
            "background:rgba(255,255,255,.92);backdrop-filter:blur(6px);" +
            "border-bottom:1px solid var(--borde,#D7DED9);}" +
        ".nav-fija__btn{display:inline-flex;align-items:center;gap:7px;" +
            "font-family:inherit;font-size:15px;font-weight:600;line-height:1;" +
            "padding:11px 18px;min-height:44px;border-radius:999px;cursor:pointer;" +
            "text-decoration:none;border:2px solid var(--petroleo,#2B5F63);" +
            "background:#fff;color:var(--petroleo-oscuro,#1D4448);" +
            "box-shadow:0 2px 8px rgba(20,28,34,.12);" +
            "transition:transform .1s ease,box-shadow .15s ease,background-color .15s ease;}" +
        ".nav-fija__btn:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(20,28,34,.2);}" +
        ".nav-fija__btn:active{transform:translateY(0);}" +
        ".nav-fija__btn:focus-visible{outline:3px solid var(--ocre,#C98A2B);outline-offset:2px;}" +
        ".nav-fija__btn--inicio{background:var(--petroleo,#2B5F63);color:#fff;}" +
        ".nav-fija__icono{font-size:18px;}" +
        "@media (max-width:520px){.nav-fija__txt{display:none;}.nav-fija__btn{padding:11px 14px;}}" +
        /* alto contraste: barra y botones oscuros, con borde blanco */
        "body.alto-contraste .nav-fija{background:#000 !important;border-bottom-color:#fff !important;}" +
        "body.alto-contraste .nav-fija__btn{background:#000 !important;color:#fff !important;border-color:#fff !important;}" +
        "body.alto-contraste .nav-fija__btn--inicio{background:#FFEB3B !important;color:#000 !important;border-color:#FFEB3B !important;}" +
        /* texto más grande: los botones acompañan */
        "html.texto-grande .nav-fija__btn{font-size:17px;}" +
        "html.texto-extra .nav-fija__btn{font-size:19px;}" +
        /* al imprimir (p. ej. el reporte del estudiante) la barra no sale */
        "@media print{.nav-fija{display:none !important;}}";

    var style = document.createElement("style");
    style.textContent = css;
    document.head.appendChild(style);

    var nav = document.createElement("nav");
    nav.className = "nav-fija";
    nav.setAttribute("aria-label", "Navegación principal");
    nav.innerHTML =
        '<button type="button" class="nav-fija__btn nav-fija__btn--atras">' +
            '<span class="nav-fija__icono" aria-hidden="true">←</span>' +
            '<span class="nav-fija__txt">Atrás</span></button>' +
        '<a href="' + INICIO + '" class="nav-fija__btn nav-fija__btn--inicio">' +
            '<span class="nav-fija__icono" aria-hidden="true">🏠</span>' +
            '<span class="nav-fija__txt">Inicio</span></a>';

    (document.body || document.documentElement).insertBefore(nav, document.body.firstChild);

    nav.querySelector(".nav-fija__btn--atras").addEventListener("click", function () {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = INICIO;
        }
    });
})();
