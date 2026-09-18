<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../models/Progreso.php";

class ProgresoController
{
    public function registrar($idSesion, $idEstudiante, $estado, $observaciones)
    {
        $modelo = new Progreso();
        return $modelo->registrar($idSesion, $idEstudiante, $estado, $observaciones);
    }

    public function registrarOActualizar($idSesion, $idEstudiante, $estado, $observaciones)
    {
        $modelo = new Progreso();
        return $modelo->registrarOActualizar($idSesion, $idEstudiante, $estado, $observaciones);
    }

    public function listarPorSesion($idSesion)
    {
        $modelo = new Progreso();
        return $modelo->listarPorSesion($idSesion);
    }

    public function listarPorDocente($idDocente)
    {
        $modelo = new Progreso();
        return $modelo->listarPorDocente($idDocente);
    }

    public function listarPorCurso($idCurso)
    {
        $modelo = new Progreso();
        return $modelo->listarPorCurso($idCurso);
    }

    public function listarTodo($idDocente = null)
    {
        $modelo = new Progreso();
        return $modelo->listarTodo($idDocente);
    }

    public function listarPorEstudiante($idEstudiante)
    {
        $modelo = new Progreso();
        return $modelo->listarPorEstudiante($idEstudiante);
    }
}