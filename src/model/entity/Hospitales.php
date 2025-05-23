<?php

namespace model\entity;

class Hospitales
{
    public int $id_hospital;
    public string $nombre;
    public string $ubicacion;

    public function __construct($id, $nombre, $ubicacion)
    {
        $this->id_hospital = $id;
        $this->nombre = $nombre;
        $this->ubicacion = $ubicacion;
    }

    public function getIdHospital(): int
    {
        return $this->id_hospital;
    }

    public function setIdHospital(int $id_hospital): void
    {
        $this->id_hospital = $id_hospital;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getUbicacion(): string
    {
        return $this->ubicacion;
    }

    public function setUbicacion(string $ubicacion): void
    {
        $this->ubicacion = $ubicacion;
    }
}