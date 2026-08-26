<?php

require_once __DIR__ . "/../models/Progreso.php";

class ProgresoController
{
    public function registrar($idSesion, $idEstudiante, $estado, $observaciones)
    {
        $modelo = new Progreso();
        return $modelo->registrar($idSesion, $idEstudiante, $estado, $observaciones);
    }

    public function listarPorSesion($idSesion)
    {
        $modelo = new Progreso();
        return $modelo->listarPorSesion($idSesion);
    }

    public function listarPorEstudiante($idEstudiante)
    {
        $modelo = new Progreso();
        return $modelo->listarPorEstudiante($idEstudiante);
    }
}