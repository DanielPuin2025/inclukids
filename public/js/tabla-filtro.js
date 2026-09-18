/* ==================================================================
   IncluKids · Filtro rápido de tablas
   ------------------------------------------------------------------
   Un <input data-filtro-tabla="idDeLaTabla"> filtra en vivo las filas
   del <tbody> de esa tabla por texto (sin distinguir mayúsculas ni
   acentos). Opcional: <small data-filtro-cuenta="idDeLaTabla"> muestra
   "N de M", y una <tr data-filtro-vacio hidden> se enseña cuando no
   hay coincidencias.

   Se carga en todas las vistas de gestión desde render_head().
   ================================================================== */
(function () {
    "use strict";

    function norm(s) {
        return (s || "").toLowerCase()
            .normalize("NFD").replace(/[̀-ͯ]/g, "");
    }

    function aplicar(input) {
        var id = input.getAttribute("data-filtro-tabla");
        var tabla = document.getElementById(id);
        if (!tabla || !tabla.tBodies[0]) return;

        var q = norm(input.value.trim());
        var filas = tabla.tBodies[0].rows;
        var visibles = 0, total = 0;

        for (var i = 0; i < filas.length; i++) {
            var fila = filas[i];
            if (fila.hasAttribute("data-filtro-vacio")) continue;
            total++;
            var coincide = q === "" || norm(fila.textContent).indexOf(q) !== -1;
            fila.hidden = !coincide;
            if (coincide) visibles++;
        }

        var cuenta = document.querySelector('[data-filtro-cuenta="' + id + '"]');
        if (cuenta) cuenta.textContent = q === "" ? "" : (visibles + " de " + total);

        var vacio = tabla.querySelector("[data-filtro-vacio]");
        if (vacio) vacio.hidden = !(q !== "" && visibles === 0);
    }

    document.addEventListener("input", function (e) {
        if (e.target && e.target.matches && e.target.matches("[data-filtro-tabla]")) {
            aplicar(e.target);
        }
    });
})();
