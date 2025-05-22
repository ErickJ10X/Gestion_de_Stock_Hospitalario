<?php

namespace model\entity;

class Productos
{
    private $id;
    private $codigo;
    private $nombre;
    private $descripcion;
    private $unidad_medida;

    public function __construct($id, $codigo, $nombre, $descripcion, $unidad_medida)
    {
        $this->id = $id;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->unidad_medida = $unidad_medida;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
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