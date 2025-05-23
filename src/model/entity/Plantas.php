<?php

namespace model\entity;

class Plantas
{
    private $id_planta;
    private $nombre;
    private $id_hospital;

    public function __construct($id_planta = null, $nombre = null, $id_hospital = null)
    {
        $this->id_planta = $id_planta;
        $this->nombre = $nombre;
        $this->id_hospital = $id_hospital;
    }

    public function getIdPlanta()
    {
        return $this->id_planta;
    }

    public function setIdPlanta($id_planta)
    {
        $this->id_planta = $id_planta;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getIdHospital()
    {
        return $this->id_hospital;
    }

    public function setIdHospital($id_hospital)
    {
        $this->id_hospital = $id_hospital;
    }
}
