<?php

namespace model\entity;

class Lecturas_stock
{
    private int $id_lectura;
    private int $id_producto;
    private int $id_botiquin;
    private int $cantidad_disponible;
    private string $fecha_lectura;
    private int $registrado_por;

    public function __construct($id_lectura, $id_producto, $id_botiquin, $cantidad_disponible, $fecha_lectura, $registrado_por)
    {
        $this->id_lectura = $id_lectura;
        $this->id_producto = $id_producto;
        $this->id_botiquin = $id_botiquin;
        $this->cantidad_disponible = $cantidad_disponible;
        $this->fecha_lectura = $fecha_lectura;
        $this->registrado_por = $registrado_por;
    }

    public function getIdLectura()
    {
        return $this->id_lectura;
    }

    public function setIdLectura($id_lectura): void
    {
        $this->id_lectura = $id_lectura;
    }

    public function getIdProducto()
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getIdBotiquin()
    {
        return $this->id_botiquin;
    }

    public function setIdBotiquin($id_botiquin): void
    {
        $this->id_botiquin = $id_botiquin;
    }

    public function getCantidadDisponible()
    {
        return $this->cantidad_disponible;
    }

    public function setCantidadDisponible($cantidad_disponible): void
    {
        $this->cantidad_disponible = $cantidad_disponible;
    }

    public function getFechaLectura()
    {
        return $this->fecha_lectura;
    }

    public function setFechaLectura($fecha_lectura): void
    {
        $this->fecha_lectura = $fecha_lectura;
    }

    public function getRegistradoPor()
    {
        return $this->registrado_por;
    }

    public function setRegistradoPor($registrado_por): void
    {
        $this->registrado_por = $registrado_por;
    }


}