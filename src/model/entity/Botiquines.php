<?php

namespace model\entity;

class Botiquines
{
    private $id;
    private $nombre;
    private $planta_id;

    public function __construct($id = null, $nombre = null, $planta_id = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->planta_id = $planta_id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getPlantaId()
    {
        return $this->planta_id;
    }

    public function setPlantaId($planta_id)
    {
        $this->planta_id = $planta_id;
    }
}
