<?php

namespace model\entity;

class Reposiciones
{
    private int $id_reposicion;
    private int $id_producto;
    private int $desde_almacen;
    private int $hasta_botiquin;
    private int $cantidad_repuesta;
    private string $fecha;
    private bool $urgente;

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

    public function getIdReposicion(): int
    {
        return $this->id_reposicion;
    }

    public function setIdReposicion($id_reposicion): void
    {
        $this->id_reposicion = $id_reposicion;
    }

    public function getIdProducto(): int
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getDesdeAlmacen(): int
    {
        return $this->desde_almacen;
    }

    public function setDesdeAlmacen($desde_almacen): void
    {
        $this->desde_almacen = $desde_almacen;
    }

    public function getHastaBotiquin(): int
    {
        return $this->hasta_botiquin;
    }

    public function setHastaBotiquin($hasta_botiquin): void
    {
        $this->hasta_botiquin = $hasta_botiquin;
    }

    public function getCantidadRepuesta(): int
    {
        return $this->cantidad_repuesta;
    }

    public function setCantidadRepuesta($cantidad_repuesta): void
    {
        $this->cantidad_repuesta = $cantidad_repuesta;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function setFecha($fecha): void
    {
        $this->fecha = $fecha;
    }

    public function getUrgente(): bool
    {
        return $this->urgente;
    }


    public function setUrgente($urgente): void
    {
        $this->urgente = $urgente;
    }
}