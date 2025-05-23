<?php

namespace model\entity;

class Etiquetas
{
    private int $id_etiqueta;
    private int $id_producto;
    private int $id_reposicion;
    private string $tipo;
    private string $prioridad;
    private bool $Impresa;

    public function __construct($id_etiqueta, $id_producto, $id_reposicion, $tipo, $prioridad, $Impresa)
    {
        $this->id_etiqueta = $id_etiqueta;
        $this->id_producto = $id_producto;
        $this->id_reposicion = $id_reposicion;
        $this->tipo = $tipo;
        $this->prioridad = $prioridad;
        $this->Impresa = $Impresa;
    }

    public function getIdEtiqueta(): int
    {
        return $this->id_etiqueta;
    }

    public function setIdEtiqueta($id_etiqueta): void
    {
        $this->id_etiqueta = $id_etiqueta;
    }

    public function getIdProducto(): int
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getIdReposicion(): int
    {
        return $this->id_reposicion;
    }

    public function setIdReposicion($id_reposicion): void
    {
        $this->id_reposicion = $id_reposicion;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo($tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getPrioridad(): string
    {
        return $this->prioridad;
    }

    public function setPrioridad($prioridad): void
    {
        $this->prioridad = $prioridad;
    }

    public function getImpresa(): bool
    {
        return $this->Impresa;
    }

    public function setImpresa($Impresa): void
    {
        $this->Impresa = $Impresa;
    }
}