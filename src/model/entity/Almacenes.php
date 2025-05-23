<?php

namespace model\entity;

class Almacenes
{
    private int $id_almacen;
    private int $id_planta;
    private string $tipo;
    private int $id_hospital;

    public function __construct($id_almacen, $id_planta, $tipo, $id_hospital)
    {
        $this->id_almacen = $id_almacen;
        $this->id_planta = $id_planta;
        $this->tipo = $tipo;
        $this->id_hospital = $id_hospital;
    }

    public function getIdAlmacen()
    {
        return $this->id_almacen;
    }

    public function setIdAlmacen($id_almacen): void
    {
        $this->id_almacen = $id_almacen;
    }

    public function getIdPlanta(): int
    {
        return $this->id_planta;
    }

    public function setIdPlanta($id_planta): void
    {
        $this->id_planta = $id_planta;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo($tipo): void
    {
        $this->tipo = $tipo;
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
