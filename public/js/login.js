(function () {
    "use strict";

    var toggle = document.getElementById("roleToggle");
    var btnDocente = document.getElementById("btnRolDocente");
    var btnAdmin = document.getElementById("btnRolAdmin");
    var rolInput = document.getElementById("rolSeleccionado");
    var hint = document.getElementById("roleHint");

    var HINTS = {
        docente: "Ingresa con tu correo institucional de docente.",
        administrador: "Ingresa con tu correo institucional de administrador."
    };

    function seleccionarRol(rol) {
        toggle.setAttribute("data-rol", rol);
        rolInput.value = rol;
        hint.textContent = HINTS[rol];

        var activo = rol === "docente" ? btnDocente : btnAdmin;
        var inactivo = rol === "docente" ? btnAdmin : btnDocente;

        activo.classList.add("is-active");
        activo.setAttribute("aria-pressed", "true");
        inactivo.classList.remove("is-active");
        inactivo.setAttribute("aria-pressed", "false");
    }

    if (toggle && btnDocente && btnAdmin) {
        btnDocente.addEventListener("click", function () { seleccionarRol("docente"); });
        btnAdmin.addEventListener("click", function () { seleccionarRol("administrador"); });
    }

    var togglePassword = document.getElementById("togglePassword");
    var passwordInput = document.getElementById("password");

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function () {
            var visible = passwordInput.type === "text";

            passwordInput.type = visible ? "password" : "text";
            togglePassword.setAttribute("aria-pressed", String(!visible));
            togglePassword.setAttribute("aria-label", visible ? "Mostrar contraseña" : "Ocultar contraseña");
            togglePassword.textContent = visible ? "👁️" : "🙈";

            togglePassword.classList.remove("is-blinking");
            void togglePassword.offsetWidth; // reinicia la animación si se repite
            togglePassword.classList.add("is-blinking");
        });
    }

    var form = document.getElementById("loginForm");
    var submitBtn = document.getElementById("loginSubmit");

    if (form && submitBtn) {
        form.addEventListener("submit", function () {
            submitBtn.classList.add("is-loading");
            submitBtn.disabled = true;
        });
    }
})();
