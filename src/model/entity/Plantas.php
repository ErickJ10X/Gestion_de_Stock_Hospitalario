<?php

namespace model\entity;

use InvalidArgumentException;

class Plantas
{
    private int $id_planta;
    private string $nombre;
    private int $id_hospital;

    public function __construct($id_planta = null, $nombre = null, $id_hospital = null)
    {
        $this->id_planta = $id_planta ?? 0;
        $this->nombre = $nombre ?? '';
        $this->id_hospital = $id_hospital ?? 0;
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
        if (empty($nombre)) {
            throw new InvalidArgumentException("El nombre de la planta no puede estar vacío");
        }
        $this->nombre = $nombre;
    }

    public function getIdHospital(): int
    {
        return $this->id_hospital;
    }

    public function setIdHospital($id_hospital): void
    {
        if ($id_hospital <= 0) {
            throw new InvalidArgumentException("El ID del hospital debe ser un número positivo");
        }
        $this->id_hospital = $id_hospital;
    }
}
