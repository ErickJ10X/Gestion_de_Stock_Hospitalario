<?php

namespace model\entity;

class Roles
{
    private int $id_rol;
    private string $nombre;

    public function __construct($id_rol = null, $nombre = null)
    {
        $this->id_rol = $id_rol;
        $this->nombre = $nombre;
    }

    public function getIdRol(): mixed
    {
        return $this->id_rol;
    }

    public function setIdRol(mixed $id_rol): void
    {
        $this->id_rol = $id_rol;
    }

    public function getNombre(): mixed
    {
        return $this->nombre;
    }

    public function setNombre(mixed $nombre): void
    {
        $this->nombre = $nombre;
    }


}