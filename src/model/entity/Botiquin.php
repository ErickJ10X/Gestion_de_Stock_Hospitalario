<?php

namespace model\entity;

class Botiquin {
    private ?int $id_botiquin;
    private ?int $id_planta;
    private string $nombre;
    private bool $activo;
    
    // Relaciones
    private ?Planta $planta;

    public function __construct(int $id_botiquin = null, int $id_planta = null, 
                               string $nombre = '', bool $activo = true) {
        $this->id_botiquin = $id_botiquin;
        $this->id_planta = $id_planta;
        $this->nombre = $nombre;
        $this->activo = $activo;
    }

    public function getIdBotiquin(): ?int {
        return $this->id_botiquin;
    }

    public function setIdBotiquin(?int $id_botiquin): self {
        $this->id_botiquin = $id_botiquin;
        return $this;
    }

    public function getIdPlanta(): ?int {
        return $this->id_planta;
    }

    public function setIdPlanta(?int $id_planta): self {
        $this->id_planta = $id_planta;
        return $this;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self {
        $this->nombre = $nombre;
        return $this;
    }

    public function isActivo(): bool {
        return $this->activo;
    }

    public function setActivo(bool $activo): self {
        $this->activo = $activo;
        return $this;
    }
    
    public function getPlanta(): ?Planta {
        return $this->planta;
    }

    public function setPlanta(?Planta $planta): self {
        $this->planta = $planta;
        return $this;
    }

    public function toArray(): array {
        return [
            'id_botiquin' => $this->id_botiquin,
            'id_planta' => $this->id_planta,
            'nombre' => $this->nombre,
            'activo' => $this->activo
        ];
    }
    
    public static function fromArray(array $data): self {
        return new self(
            $data['id_botiquin'] ?? null,
            $data['id_planta'] ?? null,
            $data['nombre'] ?? '',
            $data['activo'] ?? true
        );
    }
}
