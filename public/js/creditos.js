/* ==================================================================
   IncluKids · Botón de créditos (creadores y copyright)
   ------------------------------------------------------------------
   Inyecta un botón flotante "Créditos" abajo a la derecha. Al
   oprimirlo abre una ventana con los creadores y el copyright.

   >>> Para cambiar los nombres, edita solo el objeto CREDITOS. <<<
   ================================================================== */
(function () {
    "use strict";

    if (document.querySelector(".creditos-btn")) return;

    var CREDITOS = {
        proyecto: "IncluKids",
        descripcion: "Plataforma educativa inclusiva para acompañar el aprendizaje de niños y niñas.",
        institucion: "Institución Educativa Megacolegio El Progreso", // opcional: si se deja vacío, no se muestra la sección
        anio: 2026,
        creadores: [
            // foto: archivo dentro de public/img/creadores/ (si falta, se muestran las iniciales)
            { nombre: "Yulieth Camila Daza Diaz", foto: "yulieth.jpg", roles: ["Líder del proyecto", "Gestora de documentación", "Analista de requerimientos"] },
            { nombre: "Julián Camilo Panadero Gallego", foto: "julian.jpg", roles: ["Diseñador de interfaz", "Programador principal", "Desarrollador de base de datos"] },
            { nombre: "Luis Fernando Moreno Córdoba", foto: "luis.jpg", roles: ["Tester y control de calidad", "Programador de apoyo", "Gestor de contenidos educativos"] }
        ]
    };

    function esc(t) {
        var d = document.createElement("div");
        d.textContent = t;
        return d.innerHTML;
    }

    var css =
        ".creditos-btn{position:fixed;right:16px;bottom:16px;z-index:1060;" +
            "display:inline-flex;align-items:center;gap:7px;font-family:inherit;" +
            "font-size:15px;font-weight:600;line-height:1;padding:11px 18px;min-height:44px;" +
            "border-radius:999px;cursor:pointer;border:2px solid var(--petroleo,#2B5F63);" +
            "background:#fff;color:var(--petroleo-oscuro,#1D4448);" +
            "box-shadow:0 2px 8px rgba(20,28,34,.18);}" +
        ".creditos-btn:hover{box-shadow:0 6px 16px rgba(20,28,34,.25);}" +
        ".creditos-btn:focus-visible,.creditos-cerrar:focus-visible{outline:3px solid var(--ocre,#C98A2B);outline-offset:2px;}" +
        ".creditos-fondo{position:fixed;inset:0;z-index:1070;display:none;align-items:center;" +
            "justify-content:center;padding:16px;background:rgba(20,28,34,.55);}" +
        ".creditos-fondo.abierto{display:flex;}" +
        ".creditos-caja{background:#fff;color:#1c2428;max-width:440px;width:100%;max-height:90vh;" +
            "overflow:auto;border-radius:16px;padding:24px;box-shadow:0 12px 40px rgba(0,0,0,.35);}" +
        ".creditos-caja h2{font-size:1.4rem;margin:0 0 4px;color:var(--petroleo-oscuro,#1D4448);}" +
        ".creditos-caja h3{font-size:1rem;margin:18px 0 8px;color:var(--petroleo,#2B5F63);}" +
        ".creditos-caja p{margin:0 0 6px;}" +
        ".creditos-caja ul{list-style:none;padding:0;margin:0;}" +
        ".creditos-caja li{padding:6px 0;border-bottom:1px solid #e3e8e5;}" +
        ".creditos-caja li.creditos-persona{display:flex;gap:14px;align-items:center;padding:10px 0;}" +
        ".creditos-foto{position:relative;flex:0 0 64px;width:64px;height:64px;border-radius:50%;overflow:hidden;" +
            "background:var(--petroleo,#2B5F63);color:#fff;display:flex;align-items:center;justify-content:center;" +
            "font-weight:700;font-size:1.25rem;}" +
        ".creditos-foto img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:50% 30%;}" +
        ".creditos-datos{min-width:0;}" +
        "body.alto-contraste .creditos-foto{border:2px solid #fff;}" +
        ".creditos-rol{display:block;font-size:.875rem;color:#5a6a70;}" +
        ".creditos-copy{margin-top:18px;font-size:.875rem;color:#5a6a70;}" +
        ".creditos-cerrar{margin-top:18px;width:100%;min-height:44px;font-family:inherit;font-size:15px;" +
            "font-weight:600;border:0;border-radius:999px;background:var(--petroleo,#2B5F63);color:#fff;cursor:pointer;}" +
        "body.alto-contraste .creditos-btn,body.alto-contraste .creditos-caja{background:#000 !important;color:#fff !important;border-color:#fff !important;}" +
        "body.alto-contraste .creditos-caja h2,body.alto-contraste .creditos-caja h3,body.alto-contraste .creditos-rol,body.alto-contraste .creditos-copy{color:#fff !important;}" +
        "body.alto-contraste .creditos-cerrar{background:#FFEB3B !important;color:#000 !important;}" +
        "html.texto-grande .creditos-btn,html.texto-grande .creditos-caja{font-size:17px;}" +
        "html.texto-extra .creditos-btn,html.texto-extra .creditos-caja{font-size:19px;}" +
        "@media print{.creditos-btn,.creditos-fondo{display:none !important;}}";

    var style = document.createElement("style");
    style.textContent = css;
    document.head.appendChild(style);

    var lista = CREDITOS.creadores.map(function (c) {
        var roles = (c.roles || []).map(function (r) {
            return '<span class="creditos-rol">' + esc(r) + "</span>";
        }).join("");
        var iniciales = c.nombre.split(" ").filter(Boolean).slice(0, 2).map(function (p) {
            return p.charAt(0);
        }).join("").toUpperCase();
        var img = c.foto
            ? '<img src="/IncluKids/public/img/creadores/' + encodeURIComponent(c.foto) + '" alt="Foto de ' +
              esc(c.nombre) + '" onerror="this.remove()">'
            : "";
        return '<li class="creditos-persona"><span class="creditos-foto" aria-hidden="false">' +
                   '<span class="creditos-iniciales" aria-hidden="true">' + esc(iniciales) + "</span>" + img + "</span>" +
               '<span class="creditos-datos"><strong>' + esc(c.nombre) + "</strong>" + roles + "</span></li>";
    }).join("");

    var boton = document.createElement("button");
    boton.type = "button";
    boton.className = "creditos-btn";
    boton.setAttribute("aria-haspopup", "dialog");
    boton.innerHTML = '<span aria-hidden="true">ⓘ</span> Créditos';

    var fondo = document.createElement("div");
    fondo.className = "creditos-fondo";
    fondo.innerHTML =
        '<div class="creditos-caja" role="dialog" aria-modal="true" aria-labelledby="creditosTitulo">' +
            '<h2 id="creditosTitulo">' + esc(CREDITOS.proyecto) + "</h2>" +
            "<p>" + esc(CREDITOS.descripcion) + "</p>" +
            "<h3>Creadores</h3><ul>" + lista + "</ul>" +
            (CREDITOS.institucion ? "<h3>Institución</h3><p>" + esc(CREDITOS.institucion) + "</p>" : "") +
            '<p class="creditos-copy">© ' + CREDITOS.anio + " " + esc(CREDITOS.proyecto) +
                ". Todos los derechos reservados.</p>" +
            '<button type="button" class="creditos-cerrar">Cerrar</button>' +
        "</div>";

    document.body.appendChild(boton);
    document.body.appendChild(fondo);

    function abrir() {
        fondo.classList.add("abierto");
        fondo.querySelector(".creditos-cerrar").focus();
    }
    function cerrar() {
        fondo.classList.remove("abierto");
        boton.focus();
    }

    boton.addEventListener("click", abrir);
    fondo.querySelector(".creditos-cerrar").addEventListener("click", cerrar);
    fondo.addEventListener("click", function (e) {
        if (e.target === fondo) cerrar();
    });
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && fondo.classList.contains("abierto")) cerrar();
    });
})();
