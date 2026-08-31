<?php

require_once __DIR__ . "/../models/Estudiante.php";

class EstudianteController
{
    public function listar()
    {
        $modelo = new Estudiante();
        return $modelo->listar();
    }

    public function listarPorCurso($idCurso)
    {
        $modelo = new Estudiante();
        return $modelo->listarPorCurso($idCurso);
    }

    public function listarPorDocente($idDocente)
    {
        $modelo = new Estudiante();
        return $modelo->listarPorDocente($idDocente);
    }

    public function obtenerPorId($idEstudiante)
    {
        $modelo = new Estudiante();
        return $modelo->obtenerPorId($idEstudiante);
    }

    public function crear($nombres, $apellidos, $idCurso)
    {
        $modelo = new Estudiante();
        return $modelo->crear($nombres, $apellidos, $idCurso);
    }

    public function actualizar($idEstudiante, $nombres, $apellidos, $idCurso)
    {
        $modelo = new Estudiante();
        return $modelo->actualizar($idEstudiante, $nombres, $apellidos, $idCurso);
    }

    public function eliminar($idEstudiante)
    {
        $modelo = new Estudiante();
        return $modelo->eliminar($idEstudiante);
    }
}