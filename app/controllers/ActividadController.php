<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../models/Actividad.php";

class ActividadController
{
    public function listar()
    {
        $modelo = new Actividad();
        return $modelo->listar();
    }

    public function listarPorDba($idDba)
    {
        $modelo = new Actividad();
        return $modelo->listarPorDba($idDba);
    }

    public function obtenerPorId($idActividad)
    {
        $modelo = new Actividad();
        return $modelo->obtenerPorId($idActividad);
    }

    public function crear($idDba, $idTipo, $nombre, $descripcion, $instrucciones)
    {
        $modelo = new Actividad();
        return $modelo->crear($idDba, $idTipo, $nombre, $descripcion, $instrucciones);
    }

    public function actualizar($idActividad, $idDba, $idTipo, $nombre, $descripcion, $instrucciones)
    {
        $modelo = new Actividad();
        return $modelo->actualizar($idActividad, $idDba, $idTipo, $nombre, $descripcion, $instrucciones);
    }

    public function eliminar($idActividad)
    {
        $modelo = new Actividad();
        return $modelo->eliminar($idActividad);
    }
}