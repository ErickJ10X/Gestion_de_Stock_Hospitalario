<?php

namespace models;

use App\Entity\Botiquin;
use App\Entity\Producto;
use App\Entity\Usuario;

class LecturaStock
{
    private int $id_lectura;
    private int $id_producto;
    private int $id_botiquin;
    private int $cantidad_disponible;
    private \DateTime $fecha_lectura;
    private int $registrado_por;
    
    // Relaciones (objetos)
    private ?Producto $producto = null;
    private ?Botiquin $botiquin = null;
    private ?Usuario $usuario = null;

    public function __construct()
    {
        $this->fecha_lectura = new \DateTime();
    }

    public function getIdLectura(): int
    {
        return $this->id_lectura;
    }

    public function setIdLectura(int $id_lectura): self
    {
        $this->id_lectura = $id_lectura;
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

    public function getIdBotiquin(): int
    {
        return $this->id_botiquin;
    }

    public function setIdBotiquin(int $id_botiquin): self
    {
        $this->id_botiquin = $id_botiquin;
        return $this;
    }

    public function getCantidadDisponible(): int
    {
        return $this->cantidad_disponible;
    }

    public function setCantidadDisponible(int $cantidad_disponible): self
    {
        $this->cantidad_disponible = $cantidad_disponible;
        return $this;
    }

    public function getFechaLectura(): \DateTime
    {
        return $this->fecha_lectura;
    }

    public function setFechaLectura(\DateTime $fecha_lectura): self
    {
        $this->fecha_lectura = $fecha_lectura;
        return $this;
    }

    public function getRegistradoPor(): int
    {
        return $this->registrado_por;
    }

    public function setRegistradoPor(int $registrado_por): self
    {
        $this->registrado_por = $registrado_por;
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

    public function getBotiquin(): ?Botiquin
    {
        return $this->botiquin;
    }

    public function setBotiquin(?Botiquin $botiquin): self
    {
        $this->botiquin = $botiquin;
        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }
}
