<?php

namespace model\entity;

class Botiquines
{
    private int $id_botiquines;
    private string $nombre;
    private int $id_planta;

    public function __construct($id_botiquines = null, $nombre = null, $id_planta = null)
    {
        $this->id_botiquines = $id_botiquines;
        $this->nombre = $nombre;
        $this->id_planta = $id_planta;
    }

    public function getIdBotiquines(): mixed
    {
        return $this->id_botiquines;
    }

    public function setIdBotiquines(mixed $id_botiquines): void
    {
        $this->id_botiquines = $id_botiquines;
    }

    public function getNombre(): mixed
    {
        return $this->nombre;
    }

    public function setNombre(mixed $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getIdPlanta(): mixed
    {
        return $this->id_planta;
    }

    public function setIdPlanta(mixed $id_planta): void
    {
        $this->id_planta = $id_planta;
    }

}
