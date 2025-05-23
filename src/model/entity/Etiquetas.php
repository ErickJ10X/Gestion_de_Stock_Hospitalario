<?php

namespace model\entity;

class Etiquetas
{
    private $id_etiqueta;
    private $id_producto;
    private $id_reposicion;
    private $tipo;
    private $prioridad;
    private $Impresa;

    public function __construct($id_etiqueta, $id_producto, $id_reposicion, $tipo, $prioridad, $Impresa)
    {
        $this->id_etiqueta = $id_etiqueta;
        $this->id_producto = $id_producto;
        $this->id_reposicion = $id_reposicion;
        $this->tipo = $tipo;
        $this->prioridad = $prioridad;
        $this->Impresa = $Impresa;
    }

    public function getIdEtiqueta()
    {
        return $this->id_etiqueta;
    }

    public function setIdEtiqueta($id_etiqueta): void
    {
        $this->id_etiqueta = $id_etiqueta;
    }

    public function getIdProducto()
    {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getIdReposicion()
    {
        return $this->id_reposicion;
    }

    public function setIdReposicion($id_reposicion): void
    {
        $this->id_reposicion = $id_reposicion;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getPrioridad()
    {
        return $this->prioridad;
    }

    public function setPrioridad($prioridad): void
    {
        $this->prioridad = $prioridad;
    }

    public function getImpresa()
    {
        return $this->Impresa;
    }

    public function setImpresa($Impresa): void
    {
        $this->Impresa = $Impresa;
    }


}