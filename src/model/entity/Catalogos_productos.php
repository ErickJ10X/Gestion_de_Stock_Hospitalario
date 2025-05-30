<?php

namespace model\entity;

class Catalogos_productos
{
    private int $id_catalogo;
    private int $id_producto;
    private int $id_planta;

    public function __construct($id_catalogo, $id_producto, $id_planta)
    {
        $this->id_catalogo = $id_catalogo;
        $this->id_producto = $id_producto;
        $this->id_planta = $id_planta;
    }

    public function getIdCatalogo(): int
    {
        return $this->id_catalogo;
    }

    public function setIdCatalogo($id_catalogo): void
    {
        $this->id_catalogo = $id_catalogo;
    }

    public function getIdProducto(): int
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getIdPlanta(): int
    {
        return $this->id_planta;
    }

    public function setIdPlanta($id_planta): void
    {
        $this->id_planta = $id_planta;
    }
}