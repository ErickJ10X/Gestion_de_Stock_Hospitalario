<?php

namespace models;

use models\Producto;

class Pacto
{
    private int $id_pacto;
    private int $id_producto;
    private string $tipo_ubicacion;
    private int $id_destino;
    private int $cantidad_pactada;
    private bool $activo;
    
    // Relación con producto
    private ?Producto $producto = null;

    public function __construct()
    {
        $this->activo = true;
    }

    public function getIdPacto(): int
    {
        return $this->id_pacto;
    }

    public function setIdPacto(int $id_pacto): self
    {
        $this->id_pacto = $id_pacto;
        return $this;
    }

    public function getIdProducto(): int
    {
        return $this->id_producto;
    }

    public function setIdProducto(int $id_producto): self
    {
        $this->id_producto = $id_producto;
        return $this;
    }

    public function getTipoUbicacion(): string
    {
        return $this->tipo_ubicacion;
    }

    public function setTipoUbicacion(string $tipo_ubicacion): self
    {
        if (!in_array($tipo_ubicacion, ['Planta', 'Botiquin'])) {
            throw new \InvalidArgumentException('Tipo de ubicación no válido');
        }
        
        $this->tipo_ubicacion = $tipo_ubicacion;
        return $this;
    }

    public function getIdDestino(): int
    {
        return $this->id_destino;
    }

    public function setIdDestino(int $id_destino): self
    {
        $this->id_destino = $id_destino;
        return $this;
    }

    public function getCantidadPactada(): int
    {
        return $this->cantidad_pactada;
    }

    public function setCantidadPactada(int $cantidad_pactada): self
    {
        $this->cantidad_pactada = $cantidad_pactada;
        return $this;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;
        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;
        return $this;
    }
}
