<?php

namespace model\entity;

require_once(__DIR__ . '/../enum/RolEnum.php');
use App\model\enum\RolEnum;

class Usuario
{
    private int $id;
    private string $nombre;
    private string $email;
    private string $contrasena;
    private RolEnum $rol;

    public function __construct(int $id, string $nombre, string $email, string $contrasena, string|RolEnum $rol)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        
        if (strpos($contrasena, '$2y$') === 0) {
            $this->contrasena = $contrasena;
        } else {
            $this->contrasena = password_hash($contrasena, PASSWORD_BCRYPT);
        }
        
        if ($rol instanceof RolEnum) {
            $this->rol = $rol;
        } else {
            if (RolEnum::isValid($rol)) {
                foreach (RolEnum::cases() as $case) {
                    if ($case->value === $rol) {
                        $this->rol = $case;
                        break;
                    }
                }
            } else {
                $this->rol = RolEnum::USUARIO_BOTIQUIN;
            }
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getContrasena(): string
    {
        return $this->contrasena;
    }

    public function getRol(): RolEnum
    {
        return $this->rol;
    }

    public function getRolValue(): string
    {
        return $this->rol->value;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setContrasena(string $contrasena): void
    {
        $this->contrasena = $contrasena;
    }

    public function setRol(string|RolEnum $rol): void
    {
        if ($rol instanceof RolEnum) {
            $this->rol = $rol;
        } else {
            if (RolEnum::isValid($rol)) {
                foreach (RolEnum::cases() as $case) {
                    if ($case->value === $rol) {
                        $this->rol = $case;
                        break;
                    }
                }
            }
        }
    }

    public function verificarContrasena(string $contrasena): bool
    {
        return password_verify($contrasena, $this->contrasena);
    }
}
