<?php

namespace model\entity;

class Usuario
{
    private int $id_usuario;
    private string $nombre;
    private string $email;
    private string $contrasena;
    private int $id_rol;
    private bool $activo;

    public function __construct($id_usuario = null, $nombre = null, $email = null, $contrasena = null, $id_rol = null, $activo = true)
    {
        $this->id_usuario = $id_usuario ?? 0;
        $this->nombre = $nombre ?? '';
        $this->email = $email ?? '';
        $this->contrasena = $contrasena ?? '';
        $this->id_rol = $id_rol ?? 5;
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

    public function getIdRol(): mixed
    {
        return $this->id_rol;
    }

    public function setIdRol(mixed $id_rol): void
    {
        $this->id_rol = $id_rol;
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
