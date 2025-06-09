<?php

namespace model\entity;

use DateTime;

class Reposicion {
    private ?int $id_reposicion = null;
    private int $id_producto;
    private int $desde_almacen;
    private int $hacia_botiquin;
    private int $cantidad_repuesta;
    private DateTime $fecha;
    private bool $estado;
    private bool $urgente;
    private string $notas;

    public function __construct(
        int $id_producto,
        int $desde_almacen,
        int $hacia_botiquin,
        int $cantidad_repuesta,
        DateTime $fecha,
        bool $estado = false,
        bool $urgente = false,
        string $notas = ''
    ) {
        $this->id_producto = $id_producto;
        $this->desde_almacen = $desde_almacen;
        $this->hacia_botiquin = $hacia_botiquin;
        $this->cantidad_repuesta = $cantidad_repuesta;
        $this->fecha = $fecha;
        $this->estado = $estado;
        $this->urgente = $urgente;
        $this->notas = $notas;
    }

    // Método estático para crear una reposición con ID (para uso en el repositorio)
    public static function withId(
        ?int $id_reposicion,
        int $id_producto,
        int $desde_almacen,
        int $hacia_botiquin,
        int $cantidad_repuesta,
        DateTime $fecha,
        bool $estado = false,
        bool $urgente = false,
        string $notas = ''
    ): self {
        $instance = new self(
            $id_producto,
            $desde_almacen,
            $hacia_botiquin,
            $cantidad_repuesta,
            $fecha,
            $estado,
            $urgente,
            $notas
        );
        $instance->id_reposicion = $id_reposicion;
        return $instance;
    }

    public function getId(): ?int {
        return $this->id_reposicion;
    }

    public function setId(?int $id_reposicion): self {
        $this->id_reposicion = $id_reposicion;
        return $this;
    }

    public function getIdProducto(): int {
        return $this->id_producto;
    }

    public function setIdProducto(int $id_producto): self {
        $this->id_producto = $id_producto;
        return $this;
    }

    public function getDesdeAlmacen(): int {
        return $this->desde_almacen;
    }

    public function setDesdeAlmacen(int $desde_almacen): self {
        $this->desde_almacen = $desde_almacen;
        return $this;
    }

    public function getHaciaBotiquin(): int {
        return $this->hacia_botiquin;
    }

    public function setHaciaBotiquin(int $hacia_botiquin): self {
        $this->hacia_botiquin = $hacia_botiquin;
        return $this;
    }

    public function getCantidadRepuesta(): int {
        return $this->cantidad_repuesta;
    }

    public function setCantidadRepuesta(int $cantidad_repuesta): self {
        $this->cantidad_repuesta = $cantidad_repuesta;
        return $this;
    }

    public function getFecha(): DateTime {
        return $this->fecha;
    }

    public function setFecha(DateTime $fecha): self {
        $this->fecha = $fecha;
        return $this;
    }

    public function getEstado(): bool {
        return $this->estado;
    }

    public function setEstado(bool $estado): self {
        $this->estado = $estado;
        return $this;
    }

    public function isUrgente(): bool {
        return $this->urgente;
    }

    public function setUrgente(bool $urgente): self {
        $this->urgente = $urgente;
        return $this;
    }

    public function getNotas(): string {
        return $this->notas;
    }

    public function setNotas(string $notas): self {
        $this->notas = $notas;
        return $this;
    }
}