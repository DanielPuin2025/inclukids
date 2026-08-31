<?php

require_once __DIR__ . "/../models/SesionClase.php";

class SesionClaseController
{
    public function listarPorDocente($idDocente)
    {
        $modelo = new SesionClase();
        return $modelo->listarPorDocente($idDocente);
    }

    public function crear($idDocente, $idCurso, $idDba, $fecha, $observaciones)
    {
        $modelo = new SesionClase();
        return $modelo->crear($idDocente, $idCurso, $idDba, $fecha, $observaciones);
    }

    public function obtenerPorId($idSesion)
    {
        $modelo = new SesionClase();
        return $modelo->obtenerPorId($idSesion);
    }

    public function eliminar($idSesion)
    {
        $modelo = new SesionClase();
        return $modelo->eliminar($idSesion);
    }
}