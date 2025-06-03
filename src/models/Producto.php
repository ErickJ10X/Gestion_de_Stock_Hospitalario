<?php

namespace models;

class Producto {
    private $id_producto;
    private $codigo;
    private $nombre;
    private $descripcion;
    private $unidad_medida;
    private $activo;

    public function __construct(int $id_producto = null, string $codigo = '', string $nombre = '', 
                               string $descripcion = '', string $unidad_medida = '', bool $activo = true) {
        $this->id_producto = $id_producto;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->unidad_medida = $unidad_medida;
        $this->activo = $activo;
    }

    public function getIdProducto(): ?int {
        return $this->id_producto;
    }

    public function setIdProducto(?int $id_producto): self {
        $this->id_producto = $id_producto;
        return $this;
    }

    public function getCodigo(): string {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self {
        $this->codigo = $codigo;
        return $this;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDescripcion(): ?string {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getUnidadMedida(): string {
        return $this->unidad_medida;
    }

    public function setUnidadMedida(string $unidad_medida): self {
        $this->unidad_medida = $unidad_medida;
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
            'id_producto' => $this->id_producto,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'unidad_medida' => $this->unidad_medida,
            'activo' => $this->activo
        ];
    }
    
    public static function fromArray(array $data): self {
        return new self(
            $data['id_producto'] ?? null,
            $data['codigo'] ?? '',
            $data['nombre'] ?? '',
            $data['descripcion'] ?? '',
            $data['unidad_medida'] ?? '',
            $data['activo'] ?? true
        );
    }
}
