<?php

require_once __DIR__ . "/../models/Curso.php";

class CursoController
{
    public function listar()
    {
        $modelo = new Curso();
        return $modelo->listar();
    }

    public function listarPorDocente($idDocente)
    {
        $modelo = new Curso();
        return $modelo->listarPorDocente($idDocente);
    }

    public function obtenerPorId($idCurso)
    {
        $modelo = new Curso();
        return $modelo->obtenerPorId($idCurso);
    }

    public function crear($grado, $grupo, $anio, $idDocente)
    {
        $modelo = new Curso();
        return $modelo->crear($grado, $grupo, $anio, $idDocente);
    }

    public function actualizar($idCurso, $grado, $grupo, $anio, $idDocente)
    {
        $modelo = new Curso();
        return $modelo->actualizar($idCurso, $grado, $grupo, $anio, $idDocente);
    }

    public function eliminar($idCurso)
    {
        $modelo = new Curso();
        return $modelo->eliminar($idCurso);
    }
}