<?php

require_once __DIR__ . "/../config/database.php";

class Docente
{
    private $conexion;

    public function __construct()
    {
        $database = new Database();
        $this->conexion = $database->conectar();
    }

    public function autenticar($correo, $contrasena)
    {
        $sql = "SELECT id_docente, nombres, apellidos, correo, rol, contraseña
                FROM docentes
                WHERE correo = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$correo]);
        $docente = $consulta->fetch(PDO::FETCH_ASSOC);

        if (!$docente) {
            return null;
        }

        $guardada = $docente["contraseña"];

        if (password_verify($contrasena, $guardada)) {
            unset($docente["contraseña"]);
            return $docente;
        }

        if (hash_equals($guardada, $contrasena)) {
            $nuevoHash = password_hash($contrasena, PASSWORD_DEFAULT);
            $this->actualizarContrasena($docente["id_docente"], $nuevoHash);
            unset($docente["contraseña"]);
            return $docente;
        }

        return null;
    }

    private function actualizarContrasena($idDocente, $nuevoHash)
    {
        $sql = "UPDATE docentes SET contraseña = ? WHERE id_docente = ?";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$nuevoHash, $idDocente]);
    }

    public function obtenerPorId($idDocente)
    {
        $sql = "SELECT id_docente, nombres, apellidos, correo, rol
                FROM docentes
                WHERE id_docente = ?";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([$idDocente]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function listar()
    {
        $sql = "SELECT id_docente, nombres, apellidos, correo, rol
                FROM docentes
                ORDER BY nombres";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un docente nuevo. La contraseña se guarda con hash
     * desde el inicio (nunca en texto plano).
     *
     * El registro público (app/views/auth/guardar_registro.php) nunca
     * debe crear administradores, por eso el rol siempre queda fijo
     * en 'docente' aquí.
     */
    public function crear($nombres, $apellidos, $correo, $contrasena)
    {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $sql = "INSERT INTO docentes (nombres, apellidos, correo, rol, contraseña)
                VALUES (?, ?, ?, 'docente', ?)";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$nombres, $apellidos, $correo, $hash]);
    }

    /**
     * Actualiza los datos básicos (no la contraseña, para eso usa
     * cambiarContrasena por separado y evitar cambios accidentales).
     */
    public function actualizar($idDocente, $nombres, $apellidos, $correo)
    {
        $sql = "UPDATE docentes
                SET nombres = ?, apellidos = ?, correo = ?
                WHERE id_docente = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$nombres, $apellidos, $correo, $idDocente]);
    }

    public function cambiarContrasena($idDocente, $nuevaContrasena)
    {
        $hash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        return $this->actualizarContrasenaPublica($idDocente, $hash);
    }

    private function actualizarContrasenaPublica($idDocente, $hash)
    {
        $sql = "UPDATE docentes SET contraseña = ? WHERE id_docente = ?";
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$hash, $idDocente]);
    }

    public function eliminar($idDocente)
    {
        $sql = "DELETE FROM docentes WHERE id_docente = ?";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([$idDocente]);
    }
}