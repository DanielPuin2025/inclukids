<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../models/ContenidoJuego.php";

class ContenidoJuegoController
{
    public function obtenerPorActividad($idActividad)
    {
        $modelo = new ContenidoJuego();
        return $modelo->obtenerPorActividad($idActividad);
    }

    public function guardar($idActividad, $datosJson)
    {
        $modelo = new ContenidoJuego();
        return $modelo->guardar($idActividad, $datosJson);
    }
}
