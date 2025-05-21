<?php

namespace model\entity;

class Planta
{
    private $id;
    private $nombre;
    private $hospital_id;

    public function __construct($id, $nombre, $hospital_id)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->hospital_id = $hospital_id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getHospitalId()
    {
        return $this->hospital_id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setHospitalId($hospital_id)
    {
        $this->hospital_id = $hospital_id;
    }
}