<?php

namespace model\entity;

use model\enum\RolEnum;

class Usuario
{
    private int $id_usuario;
    private string $nombre;
    private string $email;
    private string $contrasena;
    private string $rol;
    private bool $activo;

    public function __construct($id_usuario = null, $nombre = null, $email = null, $contrasena = null, $rol = null, $activo = true)
    {
        $this->id_usuario = $id_usuario ?? 0;
        $this->nombre = $nombre ?? '';
        $this->email = $email ?? '';
        $this->contrasena = $contrasena ?? '';
        $this->rol = $rol ?? RolEnum::USUARIO_BOTIQUIN;
        $this->activo = $activo ?? true;
    }

    public function getIdUsuario(): mixed
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(mixed $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }

    public function getNombre(): mixed
    {
        return $this->nombre;
    }

    public function setNombre(mixed $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getEmail(): mixed
    {
        return $this->email;
    }

    public function setEmail(mixed $email): void
    {
        $this->email = $email;
    }

    public function getContrasena(): string
    {
        return $this->contrasena;
    }

    public function setContrasena(string $contrasena): void
    {
        $this->contrasena = $contrasena;
    }

    /**
     * Obtiene el rol como string
     */
    public function getRol(): string
    {
        return $this->rol;
    }

    /**
     * Establece el rol validando que sea un valor válido
     */
    public function setRol(string $rol): void
    {
        // Aseguramos que el rol sea válido según el enum
        if (!RolEnum::isValid($rol)) {
            $rol = RolEnum::USUARIO_BOTIQUIN;
        }
        $this->rol = $rol;
    }

    public function getActivo(): mixed
    {
        return $this->activo;
    }

    public function setActivo(mixed $activo): void
    {
        $this->activo = $activo;
    }

    public function hashPassword(): void
    {
        if (!empty($this->contrasena) && !$this->esContrasenaHasheada()) {
            $this->contrasena = password_hash($this->contrasena, PASSWORD_DEFAULT);
        }
    }

    private function esContrasenaHasheada(): bool
    {
        return strlen($this->contrasena) === 60 && substr($this->contrasena, 0, 1) === '$';
    }

    public function verificarContrasena($contrasena): bool
    {
        return password_verify($contrasena, $this->contrasena);
    }
}
