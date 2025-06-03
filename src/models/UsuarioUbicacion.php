<?php

namespace models;

class UsuarioUbicacion {
    private $id_usuario;
    private $tipo_ubicacion;
    private $id_ubicacion;
    
    // Relaciones
    private $usuario;
    private $ubicacion; // Puede ser Hospital, Planta o Botiquin
    
    const TIPO_HOSPITAL = 'Hospital';
    const TIPO_PLANTA = 'Planta';
    const TIPO_BOTIQUIN = 'Botiquin';
    
    const TIPOS_VALIDOS = [
        self::TIPO_HOSPITAL,
        self::TIPO_PLANTA,
        self::TIPO_BOTIQUIN
    ];

    public function __construct(int $id_usuario = null, string $tipo_ubicacion = '', int $id_ubicacion = null) {
        $this->id_usuario = $id_usuario;
        $this->tipo_ubicacion = $tipo_ubicacion;
        $this->id_ubicacion = $id_ubicacion;
    }

    public function getIdUsuario(): ?int {
        return $this->id_usuario;
    }

    public function setIdUsuario(?int $id_usuario): self {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    public function getTipoUbicacion(): string {
        return $this->tipo_ubicacion;
    }

    public function setTipoUbicacion(string $tipo_ubicacion): self {
        if (!in_array($tipo_ubicacion, self::TIPOS_VALIDOS)) {
            throw new \InvalidArgumentException('Tipo de ubicación inválido');
        }
        $this->tipo_ubicacion = $tipo_ubicacion;
        return $this;
    }

    public function getIdUbicacion(): ?int {
        return $this->id_ubicacion;
    }

    public function setIdUbicacion(?int $id_ubicacion): self {
        $this->id_ubicacion = $id_ubicacion;
        return $this;
    }
    
    public function getUsuario(): ?Usuario {
        return $this->usuario;
    }
    
    public function setUsuario(?Usuario $usuario): self {
        $this->usuario = $usuario;
        return $this;
    }
    
    public function getUbicacion() {
        return $this->ubicacion;
    }
    
    public function setUbicacion($ubicacion): self {
        $this->ubicacion = $ubicacion;
        return $this;
    }

    public function toArray(): array {
        return [
            'id_usuario' => $this->id_usuario,
            'tipo_ubicacion' => $this->tipo_ubicacion,
            'id_ubicacion' => $this->id_ubicacion
        ];
    }
    
    public static function fromArray(array $data): self {
        return new self(
            $data['id_usuario'] ?? null,
            $data['tipo_ubicacion'] ?? '',
            $data['id_ubicacion'] ?? null
        );
    }
}
