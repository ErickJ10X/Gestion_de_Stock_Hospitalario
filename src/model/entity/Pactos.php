<?php

namespace model\entity;

class Pactos
{
    private int $id_pacto;
    private int $id_producto;
    private string $tipo_ubicacion;
    private int $id_destino;
    private int $cantidad_pactada;

    public function __construct($id_pacto, $id_producto, $tipo_ubicacion, $id_destino, $cantidad_pactada)
    {
        $this->id_pacto = $id_pacto;
        $this->id_producto = $id_producto;
        $this->tipo_ubicacion = $tipo_ubicacion;
        $this->id_destino = $id_destino;
        $this->cantidad_pactada = $cantidad_pactada;
    }

    public function getIdPacto(): int
    {
        return $this->id_pacto;
    }

    public function setIdPacto($id_pacto): void
    {
        $this->id_pacto = $id_pacto;
    }

    public function getIdProducto(): int
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getTipoUbicacion(): string
    {
        return $this->tipo_ubicacion;
    }

    public function setTipoUbicacion($tipo_ubicacion): void
    {
        $this->tipo_ubicacion = $tipo_ubicacion;
    }

    public function getIdDestino(): int
    {
        return $this->id_destino;
    }

    public function setIdDestino($id_destino): void
    {
        $this->id_destino = $id_destino;
    }

    public function getCantidadPactada(): int
    {
        return $this->cantidad_pactada;
    }

    public function setCantidadPactada($cantidad_pactada): void
    {
        $this->cantidad_pactada = $cantidad_pactada;
    }
}