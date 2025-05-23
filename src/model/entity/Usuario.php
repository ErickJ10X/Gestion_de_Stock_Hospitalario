<?php

namespace model\entity;

class Usuario
{
    private $id_usuario;
    private $nombre;
    private $email;
    private $contrasena;
    private $id_rol;

    public function __construct($id_usuario = null, $nombre = null, $email = null, $contrasena = null, $id_rol = null)
    {
        $this->id_usuario = $id_usuario;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->contrasena = $contrasena;
        $this->id_rol = $id_rol;
    }

    public function getIdUsuario(): mixed
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(mixed $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }

    public function getNombre(): mixed
    {
        return $this->nombre;
    }

    public function setNombre(mixed $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getEmail(): mixed
    {
        return $this->email;
    }

    public function setEmail(mixed $email): void
    {
        $this->email = $email;
    }

    public function getContrasena(): mixed
    {
        return $this->contrasena;
    }

    public function setContrasena(mixed $contrasena): void
    {
        $this->contrasena = $contrasena;
    }

    public function getIdRol(): mixed
    {
        return $this->id_rol;
    }

    public function setIdRol(mixed $id_rol): void
    {
        $this->id_rol = $id_rol;
    }


    public function verificarContrasena($contrasena)
    {
        return password_verify($contrasena, $this->contrasena);
    }


}
