<?php

defined("INCLUKIDS") or exit("Acceso no permitido.");

require_once __DIR__ . "/../models/Docente.php";

class DocenteController
{
    public function listar()
    {
        $modelo = new Docente();
        return $modelo->listar();
    }

    public function obtenerPorId($idDocente)
    {
        $modelo = new Docente();
        return $modelo->obtenerPorId($idDocente);
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $modelo = new Docente();
        return $modelo->buscarPorCorreo($correo);
    }

    public function crear($nombres, $apellidos, $correo, $contrasena)
    {
        $modelo = new Docente();
        return $modelo->crear($nombres, $apellidos, $correo, $contrasena);
    }

    public function actualizar($idDocente, $nombres, $apellidos, $correo)
    {
        $modelo = new Docente();
        return $modelo->actualizar($idDocente, $nombres, $apellidos, $correo);
    }

    public function cambiarContrasena($idDocente, $nuevaContrasena)
    {
        $modelo = new Docente();
        return $modelo->cambiarContrasena($idDocente, $nuevaContrasena);
    }

    public function marcarSolicitudPassword(int $idDocente): void
    {
        $modelo = new Docente();
        $modelo->marcarSolicitudPassword($idDocente);
    }

    public function eliminar($idDocente)
    {
        $modelo = new Docente();
        return $modelo->eliminar($idDocente);
    }
}