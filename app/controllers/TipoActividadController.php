<?php

require_once __DIR__ . "/../models/TipoActividad.php";

class TipoActividadController
{
    public function listar()
    {
        $modelo = new TipoActividad();
        return $modelo->listar();
    }
}