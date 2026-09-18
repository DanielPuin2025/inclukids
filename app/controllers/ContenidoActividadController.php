<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../models/ContenidoActividad.php";

class ContenidoActividadController
{
    public function listarPorActividad($idActividad)
    {
        $modelo = new ContenidoActividad();
        return $modelo->listarPorActividad($idActividad);
    }

    public function crear($idActividad, $pregunta, $opcionA, $opcionB, $opcionC, $opcionD, $respuestaCorrecta, $orden)
    {
        $modelo = new ContenidoActividad();
        return $modelo->crear($idActividad, $pregunta, $opcionA, $opcionB, $opcionC, $opcionD, $respuestaCorrecta, $orden);
    }

    public function eliminar($idContenido)
    {
        $modelo = new ContenidoActividad();
        return $modelo->eliminar($idContenido);
    }
}