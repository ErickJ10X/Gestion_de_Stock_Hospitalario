<?php

namespace model\entity;

class Reposiciones
{
    private $id_reposicion;
    private $id_producto;
    private $desde_almacen;
    private $hasta_botiquin;
    private $cantidad_repuesta;
    private $fecha;
    private $urgente;

    public function __construct($id_reposicion, $id_producto, $desde_almacen, $hasta_botiquin, $cantidad_repuesta, $fecha, $urgente)
    {
        $this->id_reposicion = $id_reposicion;
        $this->id_producto = $id_producto;
        $this->desde_almacen = $desde_almacen;
        $this->hasta_botiquin = $hasta_botiquin;
        $this->cantidad_repuesta = $cantidad_repuesta;
        $this->fecha = $fecha;
        $this->urgente = $urgente;
    }

    public function getIdReposicion()
    {
        return $this->id_reposicion;
    }

    public function setIdReposicion($id_reposicion): void
    {
        $this->id_reposicion = $id_reposicion;
    }

    public function getIdProducto()
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getDesdeAlmacen()
    {
        return $this->desde_almacen;
    }

    public function setDesdeAlmacen($desde_almacen): void
    {
        $this->desde_almacen = $desde_almacen;
    }

    public function getHastaBotiquin()
    {
        return $this->hasta_botiquin;
    }

    public function setHastaBotiquin($hasta_botiquin): void
    {
        $this->hasta_botiquin = $hasta_botiquin;
    }

    public function getCantidadRepuesta()
    {
        return $this->cantidad_repuesta;
    }

    public function setCantidadRepuesta($cantidad_repuesta): void
    {
        $this->cantidad_repuesta = $cantidad_repuesta;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFecha($fecha): void
    {
        $this->fecha = $fecha;
    }

    public function getUrgente()
    {
        return $this->urgente;
    }


    public function setUrgente($urgente): void
    {
        $this->urgente = $urgente;
    }


}