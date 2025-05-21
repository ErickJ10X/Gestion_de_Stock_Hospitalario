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
            $row['id'],
            $row['nombre'],
            $row['email'],
            $row['contrasena'],
            $row['rol']
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
        $sql = "SELECT id, nombre, email, contrasena, rol FROM usuarios";
        return $this->mapToUsuarioArray($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findById($id): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $usuario = new Usuario();
            $usuario->setId($row['id']);
            $usuario->setNombre($row['nombre']);
            $usuario->setEmail($row['email']);
            $usuario->setRol($row['rol']);
            
            return $usuario;
        }
        
        return null;
    }

    public function findByEmail($email): ?Usuario
    {
        $sql = "SELECT id, nombre, email, contrasena, rol FROM usuarios WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToUsuario($row) : null;
    }

    public function save(Usuario $usuario): bool
    {
        $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol) VALUES (?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([
            $usuario->nombre,
            $usuario->email,
            $usuario->contrasena,
            $usuario->getRolValue()
        ]);
    }
    
    public function deleteById($id): bool
    {
        return $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
    }
    
    public function update(Usuario $usuario): bool
    {
        $sql = "UPDATE usuarios SET nombre = ?, email = ?, contrasena = ?, rol = ? WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([
            $usuario->nombre,
            $usuario->email,
            $usuario->contrasena,
            $usuario->getRolValue(),
            $usuario->id
        ]);
    }
}
