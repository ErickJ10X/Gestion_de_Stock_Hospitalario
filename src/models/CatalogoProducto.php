<?php

namespace models;

class CatalogoProducto {
    private $id_catalogo;
    private $id_producto;
    private $id_planta;
    private $activo;
    
    // Relaciones
    private $producto;
    private $planta;

    public function __construct(int $id_catalogo = null, int $id_producto = null, 
                               int $id_planta = null, bool $activo = true) {
        $this->id_catalogo = $id_catalogo;
        $this->id_producto = $id_producto;
        $this->id_planta = $id_planta;
        $this->activo = $activo;
    }

    public function getIdCatalogo(): ?int {
        return $this->id_catalogo;
    }

    public function setIdCatalogo(?int $id_catalogo): self {
        $this->id_catalogo = $id_catalogo;
        return $this;
    }

    public function getIdProducto(): ?int {
        return $this->id_producto;
    }

    public function setIdProducto(?int $id_producto): self {
        $this->id_producto = $id_producto;
        return $this;
    }

    public function getIdPlanta(): ?int {
        return $this->id_planta;
    }

    public function setIdPlanta(?int $id_planta): self {
        $this->id_planta = $id_planta;
        return $this;
    }

    public function isActivo(): bool {
        return $this->activo;
    }

    public function setActivo(bool $activo): self {
        $this->activo = $activo;
        return $this;
    }
    
    public function getProducto(): ?Producto {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self {
        $this->producto = $producto;
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
            'id_catalogo' => $this->id_catalogo,
            'id_producto' => $this->id_producto,
            'id_planta' => $this->id_planta,
            'activo' => $this->activo
        ];
    }
    
    public static function fromArray(array $data): self {
        return new self(
            $data['id_catalogo'] ?? null,
            $data['id_producto'] ?? null,
            $data['id_planta'] ?? null,
            $data['activo'] ?? true
        );
    }
}
