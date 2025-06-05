<?php

namespace model\entity;

class Planta {
    private ?int $id_planta;
    private ?int $id_hospital;
    private string $nombre;
    private bool $activo;
    private ?Hospital $hospital; // Relación con Hospital

    public function __construct(int $id_planta = null, int $id_hospital = null, string $nombre = '', bool $activo = true) {
        $this->id_planta = $id_planta;
        $this->id_hospital = $id_hospital;
        $this->nombre = $nombre;
        $this->activo = $activo;
    }

    public function getIdPlanta(): ?int {
        return $this->id_planta;
    }

    public function setIdPlanta(?int $id_planta): self {
        $this->id_planta = $id_planta;
        return $this;
    }

    public function getIdHospital(): ?int {
        return $this->id_hospital;
    }

    public function setIdHospital(?int $id_hospital): self {
        $this->id_hospital = $id_hospital;
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

    public function getHospital(): ?Hospital {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self {
        $this->hospital = $hospital;
        return $this;
    }

    public function toArray(): array {
        return [
            'id_planta' => $this->id_planta,
            'id_hospital' => $this->id_hospital,
            'nombre' => $this->nombre,
            'activo' => $this->activo
        ];
    }
    
    public static function fromArray(array $data): self {
        return new self(
            $data['id_planta'] ?? null,
            $data['id_hospital'] ?? null,
            $data['nombre'] ?? '',
            $data['activo'] ?? true
        );
    }
}
