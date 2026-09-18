<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../models/SesionClase.php";

class SesionClaseController
{
    public function listarPorDocente($idDocente)
    {
        $modelo = new SesionClase();
        return $modelo->listarPorDocente($idDocente);
    }

    public function listarTodo($idDocente = null)
    {
        $modelo = new SesionClase();
        return $modelo->listarTodo($idDocente);
    }

    public function contarSinProgreso($idDocente)
    {
        $modelo = new SesionClase();
        return $modelo->contarSinProgreso($idDocente);
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

    public function duplicar($idSesion, $nuevaFecha)
    {
        $modelo = new SesionClase();
        return $modelo->duplicar($idSesion, $nuevaFecha);
    }

    public function eliminar($idSesion)
    {
        $modelo = new SesionClase();
        return $modelo->eliminar($idSesion);
    }
}