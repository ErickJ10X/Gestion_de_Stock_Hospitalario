<?php

namespace model;

class User
{
    private int $id;
    private string $nombre;
    private string $email;
    private string $contrasena;
    private string $rol;

    public function __construct(int $id, string $nombre, string $email, string $contrasena, string $rol)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->contrasena = password_hash($contrasena, PASSWORD_BCRYPT);
        $this->rol = $rol;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getContrasena(): string
    {
        return $this->contrasena;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

}