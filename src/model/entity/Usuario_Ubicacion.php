<?php

namespace model\entity;

class Usuario_Ubicacion
{
    private $id_usuario;
    private $tipo_ubicacion;
    private $id_ubicacion;

    public function __construct($id_usuario = null, $tipo_ubicacion = null, $id_ubicacion = null)
    {
        $this->id_usuario = $id_usuario;
        $this->tipo_ubicacion = $tipo_ubicacion;
        $this->id_ubicacion = $id_ubicacion;
    }

    public function getIdUsuario(): mixed
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(mixed $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }

    public function getTipoUbicacion(): mixed
    {
        return $this->tipo_ubicacion;
    }

    public function setTipoUbicacion(mixed $tipo_ubicacion): void
    {
        $this->tipo_ubicacion = $tipo_ubicacion;
    }

    public function getIdUbicacion(): mixed
    {
        return $this->id_ubicacion;
    }

    public function setIdUbicacion(mixed $id_ubicacion): void
    {
        $this->id_ubicacion = $id_ubicacion;
    }
}