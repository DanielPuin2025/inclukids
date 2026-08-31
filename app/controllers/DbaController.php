<?php

require_once __DIR__ . "/../models/Dba.php";

class DbaController
{
    public function listar()
    {
        $modelo = new Dba();
        return $modelo->listar();
    }

    public function listarPorMateriaGradoPeriodo($idMateria, $grado, $periodo)
    {
        $modelo = new Dba();
        return $modelo->listarPorMateriaGradoPeriodo($idMateria, $grado, $periodo);
    }

    public function listarPorDocente($idDocente)
    {
        $modelo = new Dba();
        return $modelo->listarPorDocente($idDocente);
    }

    public function obtenerPorId($idDba)
    {
        $modelo = new Dba();
        return $modelo->obtenerPorId($idDba);
    }

    public function crear($idMateria, $grado, $periodo, $codigo, $descripcion)
    {
        $modelo = new Dba();
        return $modelo->crear($idMateria, $grado, $periodo, $codigo, $descripcion);
    }

    public function actualizar($idDba, $idMateria, $grado, $periodo, $codigo, $descripcion)
    {
        $modelo = new Dba();
        return $modelo->actualizar($idDba, $idMateria, $grado, $periodo, $codigo, $descripcion);
    }

    public function eliminar($idDba)
    {
        $modelo = new Dba();
        return $modelo->eliminar($idDba);
    }

    public function tieneUsoReal($idDba)
    {
        $modelo = new Dba();
        return $modelo->tieneUsoReal($idDba);
    }

    public function actividadesAsociadas($idDba)
    {
        $modelo = new Dba();
        return $modelo->actividadesAsociadas($idDba);
    }

    public function eliminarConActividades($idDba)
    {
        $modelo = new Dba();
        return $modelo->eliminarConActividades($idDba);
    }
}