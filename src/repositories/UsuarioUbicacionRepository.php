<?php

namespace repositories;

use Models\UsuarioUbicacion;
use Models\Usuario;
use Repositories\Interfaces\UsuarioUbicacionRepositoryInterface;
use src\enum\RolEnum;
use PDO;

class UsuarioUbicacionRepository implements UsuarioUbicacionRepositoryInterface {
    private $db;

    public function __construct() {
        // Usar database.php
        require_once __DIR__ . '/../config/database.php';
        $this->db = getConnection();
    }

    public function findByUsuario(int $idUsuario): array {
        $stmt = $this->db->prepare('
            SELECT uu.* 
            FROM usuario_ubicacion uu
            WHERE uu.id_usuario = :id_usuario
        ');
        
        $stmt->execute(['id_usuario' => $idUsuario]);
        
        $ubicaciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ubicaciones[] = UsuarioUbicacion::fromArray($row);
        }
        
        return $ubicaciones;
    }

    public function findByUbicacion(string $tipoUbicacion, int $idUbicacion): array {
        $stmt = $this->db->prepare('
            SELECT uu.*, u.nombre, u.email, u.rol, u.activo
            FROM usuario_ubicacion uu
            JOIN usuarios u ON uu.id_usuario = u.id_usuario
            WHERE uu.tipo_ubicacion = :tipo_ubicacion AND uu.id_ubicacion = :id_ubicacion
        ');
        
        $stmt->execute([
            'tipo_ubicacion' => $tipoUbicacion,
            'id_ubicacion' => $idUbicacion
        ]);
        
        $ubicaciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ubicacion = UsuarioUbicacion::fromArray($row);
            
            // Crear y asignar el usuario
            $usuario = new Usuario(
                $row['id_usuario'],
                $row['nombre'] ?? '',
                $row['email'] ?? '',
                '', // No devolvemos la contraseña por seguridad
                $row['rol'] ?? RolEnum::USUARIO_BOTIQUIN,
                (bool)($row['activo'] ?? true)
            );
            
            $ubicacion->setUsuario($usuario);
            $ubicaciones[] = $ubicacion;
        }
        
        return $ubicaciones;
    }

    public function find(int $idUsuario, string $tipoUbicacion, int $idUbicacion): ?UsuarioUbicacion {
        $stmt = $this->db->prepare('
            SELECT uu.*
            FROM usuario_ubicacion uu
            WHERE uu.id_usuario = :id_usuario 
              AND uu.tipo_ubicacion = :tipo_ubicacion 
              AND uu.id_ubicacion = :id_ubicacion
        ');
        
        $stmt->execute([
            'id_usuario' => $idUsuario,
            'tipo_ubicacion' => $tipoUbicacion,
            'id_ubicacion' => $idUbicacion
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? UsuarioUbicacion::fromArray($row) : null;
    }

    public function save(UsuarioUbicacion $ubicacion): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO usuario_ubicacion (id_usuario, tipo_ubicacion, id_ubicacion) 
             VALUES (:id_usuario, :tipo_ubicacion, :id_ubicacion)'
        );
        
        return $stmt->execute([
            'id_usuario' => $ubicacion->getIdUsuario(),
            'tipo_ubicacion' => $ubicacion->getTipoUbicacion(),
            'id_ubicacion' => $ubicacion->getIdUbicacion()
        ]);
    }

    public function delete(int $idUsuario, string $tipoUbicacion, int $idUbicacion): bool {
        $stmt = $this->db->prepare('
            DELETE FROM usuario_ubicacion 
            WHERE id_usuario = :id_usuario 
              AND tipo_ubicacion = :tipo_ubicacion 
              AND id_ubicacion = :id_ubicacion
        ');
        
        return $stmt->execute([
            'id_usuario' => $idUsuario,
            'tipo_ubicacion' => $tipoUbicacion,
            'id_ubicacion' => $idUbicacion
        ]);
    }

    public function deleteAllByUsuario(int $idUsuario): bool {
        $stmt = $this->db->prepare('DELETE FROM usuario_ubicacion WHERE id_usuario = :id_usuario');
        return $stmt->execute(['id_usuario' => $idUsuario]);
    }
}
