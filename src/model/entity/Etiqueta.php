<?php

namespace model\entity;

use InvalidArgumentException;

class Etiqueta {
    private ?int $id_etiqueta = null;
    private int $id_producto;
    private int $id_reposicion;
    private string $tipo;
    private string $prioridad;
    private bool $impresa;

    public function __construct(
        int $id_producto,
        int $id_reposicion,
        string $tipo,
        string $prioridad,
        bool $impresa = false
    ) {
        $this->id_producto = $id_producto;
        $this->id_reposicion = $id_reposicion;
        $this->tipo = $tipo;
        $this->prioridad = $prioridad;
        $this->impresa = $impresa;
    }

    public function getId(): ?int {
        return $this->id_etiqueta;
    }

    public function setId(?int $id_etiqueta): self {
        $this->id_etiqueta = $id_etiqueta;
        return $this;
    }

    public function getIdProducto(): int {
        return $this->id_producto;
    }

    public function setIdProducto(int $id_producto): self {
        $this->id_producto = $id_producto;
        return $this;
    }

    public function getIdReposicion(): int {
        return $this->id_reposicion;
    }

    public function setIdReposicion(int $id_reposicion): self {
        $this->id_reposicion = $id_reposicion;
        return $this;
    }

    public function getTipo(): string {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self {
        if (!in_array($tipo, ['Informativa', 'RFID'])) {
            throw new InvalidArgumentException("Tipo de etiqueta no válido");
        }
        $this->tipo = $tipo;
        return $this;
    }

    public function getPrioridad(): string {
        return $this->prioridad;
    }

    public function setPrioridad(string $prioridad): self {
        if (!in_array($prioridad, ['Normal', 'Urgente'])) {
            throw new InvalidArgumentException("Prioridad no válida");
        }
        $this->prioridad = $prioridad;
        return $this;
    }

    public function isImpresa(): bool {
        return $this->impresa;
    }

    public function setImpresa(bool $impresa): self {
        $this->impresa = $impresa;
        return $this;
    }
}
