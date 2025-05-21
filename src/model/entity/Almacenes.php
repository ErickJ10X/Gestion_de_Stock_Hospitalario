<?php

namespace model\entity;

class Almacenes
{
    private $id;
    private $planta_id;

    public function __construct($id = null, $planta_id = null)
    {
        $this->id = $id;
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

    public function getPlantaId()
    {
        return $this->planta_id;
    }

    public function setPlantaId($planta_id)
    {
        $this->planta_id = $planta_id;
    }
}
