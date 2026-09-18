<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../models/TipoActividad.php";

class TipoActividadController
{
    public function listar()
    {
        $modelo = new TipoActividad();
        return $modelo->listar();
    }
}