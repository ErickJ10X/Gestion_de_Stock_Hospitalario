<?php

namespace model\entity;

class Productos
{
    private $id_producto;
    private $codigo;
    private $nombre;
    private $descripcion;
    private $unidad_medida;

    public function __construct($id_producto, $codigo, $nombre, $descripcion, $unidad_medida)
    {
        $this->id_producto = $id_producto;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->unidad_medida = $unidad_medida;
    }

    public function getIdProducto()
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }


    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getUnidadMedida()
    {
        return $this->unidad_medida;
    }


    public function setUnidadMedida($unidad_medida): void
    {
        $this->unidad_medida = $unidad_medida;
    }

}