<?php

require_once __DIR__ . "/../models/Materia.php";

class MateriaController
{
    public function listar()
    {
        $modelo = new Materia();
        return $modelo->listar();
    }

    public function obtenerPorId($idMateria)
    {
        $modelo = new Materia();
        return $modelo->obtenerPorId($idMateria);
    }
}