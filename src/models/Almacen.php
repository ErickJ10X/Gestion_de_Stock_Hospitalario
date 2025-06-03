<?php

namespace models;

class Almacen {
    private $id_almacen;
    private $tipo;
    private $id_planta;
    private $id_hospital;
    private $activo;
    
    // Relaciones
    private $planta;
    private $hospital;

    public function __construct(int $id_almacen = null, string $tipo = 'General', 
                               ?int $id_planta = null, int $id_hospital = null, bool $activo = true) {
        $this->id_almacen = $id_almacen;
        $this->tipo = $tipo;
        $this->id_planta = $id_planta;
        $this->id_hospital = $id_hospital;
        $this->activo = $activo;
    }

    public function getIdAlmacen(): ?int {
        return $this->id_almacen;
    }

    public function setIdAlmacen(?int $id_almacen): self {
        $this->id_almacen = $id_almacen;
        return $this;
    }

    public function getTipo(): string {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self {
        if (!in_array($tipo, ['General', 'Planta'])) {
            throw new \InvalidArgumentException('Tipo de almacén inválido. Valores permitidos: General, Planta');
        }
        $this->tipo = $tipo;
        return $this;
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
    
    public function getHospital(): ?Hospital {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self {
        $this->hospital = $hospital;
        return $this;
    }

    public function toArray(): array {
        return [
            'id_almacen' => $this->id_almacen,
            'tipo' => $this->tipo,
            'id_planta' => $this->id_planta,
            'id_hospital' => $this->id_hospital,
            'activo' => $this->activo
        ];
    }
    
    public static function fromArray(array $data): self {
        return new self(
            $data['id_almacen'] ?? null,
            $data['tipo'] ?? 'General',
            $data['id_planta'] ?? null,
            $data['id_hospital'] ?? null,
            $data['activo'] ?? true
        );
    }
}
