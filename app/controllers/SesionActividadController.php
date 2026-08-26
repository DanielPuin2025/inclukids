<?php

require_once __DIR__ . "/../models/SesionActividad.php";

class SesionActividadController
{
    public function registrar($idSesion, $idActividad)
    {
        $modelo = new SesionActividad();
        return $modelo->registrar($idSesion, $idActividad);
    }

    public function listarPorSesion($idSesion)
    {
        $modelo = new SesionActividad();
        return $modelo->listarPorSesion($idSesion);
    }

    public function quitar($idSesionActividad)
    {
        $modelo = new SesionActividad();
        return $modelo->quitar($idSesionActividad);
    }
}