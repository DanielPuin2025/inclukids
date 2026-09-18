<?php
require __DIR__ . "/../../bootstrap.php";

requireAdministrador("../dashboard/visualizador.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

csrf_verificar();

require_once __DIR__ . "/../../controllers/DocenteController.php";

$idDocente = (int) ($_POST["id_docente"] ?? 0); // 0 significa "es nuevo"
$nombres = trim($_POST["nombres"] ?? "");
$apellidos = trim($_POST["apellidos"] ?? "");
$correo = trim($_POST["correo"] ?? "");

$volver = $idDocente > 0 ? "editar.php?id_docente=$idDocente" : "nuevo.php";

if ($nombres === "" || $apellidos === "" || $correo === "") {
    flash_set("error", "Completa nombres, apellidos y correo.");
    header("Location: $volver");
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    flash_set("error", "Ese correo no es válido.");
    header("Location: $volver");
    exit();
}

$docenteController = new DocenteController();

try {
    if ($idDocente === 0) {
        // Contraseña temporal legible: mismo formato que el reseteo
        // desde el panel (docentes/restablecer_password.php).
        $temporal = "IK-" . random_int(1000, 9999) . "-" . bin2hex(random_bytes(2));

        $docenteController->crear($nombres, $apellidos, $correo, $temporal);
        $nuevo = $docenteController->buscarPorCorreo($correo);

        auditar("crear", "docente", $nuevo["id_docente"] ?? null, "$nombres $apellidos ($correo)");

        flash_set("exito",
            "Cuenta creada para $nombres $apellidos. Contraseña temporal: "
            . "$temporal — pásasela por un canal seguro y pídele que la "
            . "cambie al entrar (Mi perfil), o que la recupere por correo "
            . "si la olvida.");
    } else {
        $docente = $docenteController->obtenerPorId($idDocente);
        if (!$docente || $docente["rol"] === "administrador") {
            flash_set("error", "Ese docente no existe o no se puede editar desde aquí.");
            header("Location: index.php");
            exit();
        }

        $docenteController->actualizar($idDocente, $nombres, $apellidos, $correo);
        auditar("actualizar", "docente", $idDocente, "$nombres $apellidos ($correo)");
        flash_set("exito", "Datos de $nombres $apellidos actualizados.");
    }
} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        flash_set("error", "Ya existe una cuenta registrada con ese correo.");
    } else {
        error_log("IncluKids - no se pudo guardar docente: " . $e->getMessage());
        flash_set("error", "Ocurrió un error al guardar. Inténtalo de nuevo.");
    }
    header("Location: $volver");
    exit();
}

header("Location: index.php");
exit();
