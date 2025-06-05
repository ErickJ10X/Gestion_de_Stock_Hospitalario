<?php

namespace model\entity;

use InvalidArgumentException;
use model\enum\RolEnum;

class Usuario {
    private ?int $id_usuario;
    private string $nombre;
    private string $email;
    private string $contrasena;
    private string $rol;
    private bool $activo;
    
    // Relaciones
    private array $ubicaciones = [];

    public function __construct(int $id_usuario = null, string $nombre = '', string $email = '', 
                               string $contrasena = '', string $rol = RolEnum::USUARIO_BOTIQUIN, bool $activo = true) {
        $this->id_usuario = $id_usuario;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->contrasena = $contrasena;
        $this->setRol($rol);
        $this->activo = $activo;
    }

    public function getIdUsuario(): ?int {
        return $this->id_usuario;
    }

    public function setIdUsuario(?int $id_usuario): self {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self {
        $this->nombre = $nombre;
        return $this;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getContrasena(): string {
        return $this->contrasena;
    }

    public function setContrasena(string $contrasena): self {
        $this->contrasena = $contrasena;
        return $this;
    }

    public function getRol(): string {
        return $this->rol;
    }

    public function setRol(string $rol): self {
        if (!RolEnum::isValid($rol)) {
            throw new InvalidArgumentException('Rol de usuario inválido');
        }
        $this->rol = $rol;
        return $this;
    }

    public function isActivo(): bool {
        return $this->activo;
    }

    public function setActivo(bool $activo): self {
        $this->activo = $activo;
        return $this;
    }
    
    public function getUbicaciones(): array {
        return $this->ubicaciones;
    }
    
    public function setUbicaciones(array $ubicaciones): self {
        $this->ubicaciones = $ubicaciones;
        return $this;
    }
    
    public function addUbicacion(UsuarioUbicacion $ubicacion): self {
        $this->ubicaciones[] = $ubicacion;
        return $this;
    }

    public function toArray(): array {
        return [
            'id_usuario' => $this->id_usuario,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'contrasena' => $this->contrasena,
            'rol' => $this->rol,
            'activo' => $this->activo
        ];
    }
    
    public static function fromArray(array $data): self {
        return new self(
            $data['id_usuario'] ?? null,
            $data['nombre'] ?? '',
            $data['email'] ?? '',
            $data['contrasena'] ?? '',
            $data['rol'] ?? RolEnum::USUARIO_BOTIQUIN,
            $data['activo'] ?? true
        );
    }
    
    public function verificarContrasena(string $contrasena): bool {
        return password_verify($contrasena, $this->contrasena);
    }
    
    public static function hashContrasena(string $contrasena): string {
        return password_hash($contrasena, PASSWORD_BCRYPT);
    }
}
