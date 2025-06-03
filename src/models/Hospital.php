<?php

namespace models;

class Hospital {
    private $id_hospital;
    private $nombre;
    private $ubicacion;
    private $activo;

    public function __construct(int $id_hospital = null, string $nombre = '', string $ubicacion = '', bool $activo = true) {
        $this->id_hospital = $id_hospital;
        $this->nombre = $nombre;
        $this->ubicacion = $ubicacion;
        $this->activo = $activo;
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

    public function getUbicacion(): ?string {
        return $this->ubicacion;
    }

    public function setUbicacion(?string $ubicacion): self {
        $this->ubicacion = $ubicacion;
        return $this;
    }

    public function isActivo(): bool {
        return $this->activo;
    }

    public function setActivo(bool $activo): self {
        $this->activo = $activo;
        return $this;
    }

    public function toArray(): array {
        return [
            'id_hospital' => $this->id_hospital,
            'nombre' => $this->nombre,
            'ubicacion' => $this->ubicacion,
            'activo' => $this->activo
        ];
    }
    
    public static function fromArray(array $data): self {
        return new self(
            $data['id_hospital'] ?? null,
            $data['nombre'] ?? '',
            $data['ubicacion'] ?? '',
            $data['activo'] ?? true
        );
    }
}
