<?php

namespace model\entity;

class Productos
{
    private int $id_producto;
    private string $codigo;
    private string $nombre;
    private string $descripcion;
    private string $unidad_medida;

    public function __construct($id_producto = null, $codigo = '', $nombre = '', $descripcion = '', $unidad_medida = '')
    {
        $this->id_producto = $id_producto ?? 0;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->unidad_medida = $unidad_medida;
    }

    public function getIdProducto(): int
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }


    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo($codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre($nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getUnidadMedida(): string
    {
        return $this->unidad_medida;
    }


    public function setUnidadMedida($unidad_medida): void
    {
        $this->unidad_medida = $unidad_medida;
    }

}
