<?php

namespace repositories;

use Models\Usuario;
use Models\UsuarioUbicacion;
use Repositories\Interfaces\UsuarioRepositoryInterface;
use PDO;

class UsuarioRepository implements UsuarioRepositoryInterface {
    private $db;

    public function __construct() {
        // Usar database.php
        require_once __DIR__ . '/../config/database.php';
        $this->db = getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query('SELECT * FROM usuarios ORDER BY nombre');
        $usuarios = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = Usuario::fromArray($row);
        }
        
        return $usuarios;
    }

    public function findById(int $id): ?Usuario {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id_usuario = :id');
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        $usuario = Usuario::fromArray($row);
        return $this->loadUbicaciones($usuario);
    }

    public function findByEmail(string $email): ?Usuario {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email');
        $stmt->execute(['email' => $email]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        $usuario = Usuario::fromArray($row);
        return $this->loadUbicaciones($usuario);
    }

    public function findByRol(string $rol): array {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE rol = :rol ORDER BY nombre');
        $stmt->execute(['rol' => $rol]);
        
        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = Usuario::fromArray($row);
        }
        
        return $usuarios;
    }

    public function findActive(): array {
        $stmt = $this->db->query('SELECT * FROM usuarios WHERE activo = 1 ORDER BY nombre');
        $usuarios = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = Usuario::fromArray($row);
        }
        
        return $usuarios;
    }

    public function findByUbicacion(string $tipoUbicacion, int $idUbicacion): array {
        $stmt = $this->db->prepare('
            SELECT u.* 
            FROM usuarios u
            JOIN usuario_ubicacion uu ON u.id_usuario = uu.id_usuario
            WHERE uu.tipo_ubicacion = :tipo_ubicacion AND uu.id_ubicacion = :id_ubicacion
            ORDER BY u.nombre
        ');
        
        $stmt->execute([
            'tipo_ubicacion' => $tipoUbicacion,
            'id_ubicacion' => $idUbicacion
        ]);
        
        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = Usuario::fromArray($row);
            $usuarios[] = $this->loadUbicaciones($usuario);
        }
        
        return $usuarios;
    }

    public function save(Usuario $usuario): Usuario {
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nombre, email, contrasena, rol, activo) 
             VALUES (:nombre, :email, :contrasena, :rol, :activo)'
        );
        
        $stmt->execute([
            'nombre' => $usuario->getNombre(),
            'email' => $usuario->getEmail(),
            'contrasena' => $usuario->getContrasena(),
            'rol' => $usuario->getRol(),
            'activo' => $usuario->isActivo() ? 1 : 0
        ]);
        
        $id = $this->db->lastInsertId();
        $usuario->setIdUsuario((int)$id);
        
        return $usuario;
    }

    public function update(Usuario $usuario): bool {
        $stmt = $this->db->prepare(
            'UPDATE usuarios 
             SET nombre = :nombre,
                 email = :email,
                 contrasena = :contrasena,
                 rol = :rol,
                 activo = :activo
             WHERE id_usuario = :id'
        );
        
        return $stmt->execute([
            'id' => $usuario->getIdUsuario(),
            'nombre' => $usuario->getNombre(),
            'email' => $usuario->getEmail(),
            'contrasena' => $usuario->getContrasena(),
            'rol' => $usuario->getRol(),
            'activo' => $usuario->isActivo() ? 1 : 0
        ]);
    }

    public function delete(int $id): bool {
        // Primero eliminamos las relaciones de ubicación
        $stmtUbicaciones = $this->db->prepare('DELETE FROM usuario_ubicacion WHERE id_usuario = :id');
        $stmtUbicaciones->execute(['id' => $id]);
        
        // Luego eliminamos el usuario
        $stmt = $this->db->prepare('DELETE FROM usuarios WHERE id_usuario = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function activate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE usuarios SET activo = 1 WHERE id_usuario = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE usuarios SET activo = 0 WHERE id_usuario = :id');
        return $stmt->execute(['id' => $id]);
    }
    
    public function loadUbicaciones(Usuario $usuario): Usuario {
        $stmt = $this->db->prepare('SELECT * FROM usuario_ubicacion WHERE id_usuario = :id_usuario');
        $stmt->execute(['id_usuario' => $usuario->getIdUsuario()]);
        
        $ubicaciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ubicaciones[] = UsuarioUbicacion::fromArray($row);
        }
        
        $usuario->setUbicaciones($ubicaciones);
        return $usuario;
    }
}
