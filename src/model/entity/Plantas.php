<?php

namespace model\entity;

class Plantas
{
    private int $id_planta;
    private string $nombre;
    private int $id_hospital;

    public function __construct($id_planta = null, $nombre = null, $id_hospital = null)
    {
        $this->id_planta = $id_planta;
        $this->nombre = $nombre;
        $this->id_hospital = $id_hospital;
    }

    public function getIdPlanta(): int
    {
        return $this->id_planta;
    }

    public function setIdPlanta($id_planta): void
    {
        $this->id_planta = $id_planta;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre($nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getIdHospital(): int
    {
        return $this->id_hospital;
    }

    public function setIdHospital($id_hospital): void
    {
        $this->id_hospital = $id_hospital;
    }
}