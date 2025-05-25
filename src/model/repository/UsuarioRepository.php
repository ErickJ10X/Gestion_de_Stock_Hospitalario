<?php

namespace model\repository;
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../entity/Usuario.php');
require_once(__DIR__ . '/../enum/RolEnum.php');

use model\entity\Usuario;
use PDO;

class UsuarioRepository
{
    private ?PDO $pdo;
    
    public function __construct()
    {
        $this->pdo = getConnection();
    }
    
    public function mapToUsuario($row): Usuario
    {
        return new Usuario(
            $row['id_usuario'] ?? null,
            $row['nombre'] ?? null,
            $row['email'] ?? null,
            $row['contrasena'] ?? null,
            $row['id_rol'] ?? null,
            $row['activo'] ?? true
        );
    }
    
    private function mapToUsuarioArray(array $rows): array
    {
        $usuarios = [];
        foreach ($rows as $row) {
            $usuarios[] = $this->mapToUsuario($row);
        }
        return $usuarios;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM usuarios";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->mapToUsuarioArray($rows);
    }

    public function findById($id): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToUsuario($row) : null;
    }

    public function findByEmail($email): ?Usuario
    {
        $sql = "SELECT id_usuario, nombre, email, contrasena, id_rol, activo FROM usuarios WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToUsuario($row) : null;
    }

    public function save(Usuario $usuario): bool
    {
        $usuario->hashPassword();
        
        $sql = "INSERT INTO usuarios (nombre, email, contrasena, id_rol, activo) VALUES (?, ?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([
            $usuario->getNombre(),
            $usuario->getEmail(),
            $usuario->getContrasena(),
            $usuario->getIdRol(),
            $usuario->getActivo()
        ]);
    }
    
    public function deleteById($id): bool
    {
        return $this->pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?")->execute([$id]);
    }
    
    public function update(Usuario $usuario): bool
    {
        $sql = "UPDATE usuarios SET nombre = ?, email = ?, contrasena = ?, id_rol = ?, activo = ? WHERE id_usuario = ?";
        return $this->pdo->prepare($sql)->execute([
            $usuario->getNombre(),
            $usuario->getEmail(),
            $usuario->getContrasena(),
            $usuario->getIdRol(),
            $usuario->getActivo(),
            $usuario->getIdUsuario()
        ]);
    }
}
