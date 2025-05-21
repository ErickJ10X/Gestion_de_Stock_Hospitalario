<?php

namespace model\entity;

class Usuario
{
    private $id;
    private $nombre;
    private $email;
    private $contrasena;
    private $rol;

    public function __construct($id = null, $nombre = null, $email = null, $contrasena = null, $rol = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->contrasena = $contrasena;
        $this->rol = $rol;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRol()
    {
        return $this->rol;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;
    }

    public function setRol($rol)
    {
        $this->rol = $rol;
    }

    public function verificarContrasena($contrasena)
    {
        return password_verify($contrasena, $this->contrasena);
    }


}
