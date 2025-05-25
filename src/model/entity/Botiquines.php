<?php

namespace model\entity;

use InvalidArgumentException;

class Botiquines
{
    private int $id_botiquin;
    private string $nombre;
    private int $id_planta;

    public function __construct($id_botiquin = null, $nombre = null, $id_planta = null)
    {
        $this->id_botiquin = $id_botiquin ?? 0;
        $this->nombre = $nombre ?? '';
        $this->id_planta = $id_planta ?? 0;
    }

    public function getIdBotiquines(): int
    {
        return $this->id_botiquin;
    }

    public function setIdBotiquines(int $id_botiquin): void
    {
        $this->id_botiquin = $id_botiquin;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        if (empty(trim($nombre))) {
            throw new InvalidArgumentException("El nombre del botiquín no puede estar vacío");
        }
        $this->nombre = $nombre;
    }

    public function getIdPlanta(): int
    {
        return $this->id_planta;
    }

    public function setIdPlanta(int $id_planta): void
    {
        if ($id_planta <= 0) {
            throw new InvalidArgumentException("El ID de la planta debe ser un número positivo");
        }
        $this->id_planta = $id_planta;
    }
}
