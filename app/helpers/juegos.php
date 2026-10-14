<?php

function juego_url_volver($idSesion)
{
    if ($idSesion > 0) {
        return "../sesiones/confirmacion.php?id_sesion=" . (int) $idSesion;
    }

    if (($_GET["origen"] ?? "") === "paso5") {
        $idCurso = (int) ($_GET["id_curso"] ?? 0);
        $idDba = (int) ($_GET["id_dba"] ?? 0);
        if ($idCurso > 0 && $idDba > 0) {
            return "../sesiones/paso5_actividad.php?id_curso=$idCurso&id_dba=$idDba";
        }
    }

    return "../actividades/index.php";
}
