<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\UsuarioUbicacion;
use model\entity\Usuario;
use PDO;

class UsuarioUbicacionRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findByUsuario(int $idUsuario): array {
        $stmt = $this->conexion->prepare("
            SELECT uu.*, u.nombre, u.email, u.rol, u.activo 
            FROM usuario_ubicacion uu
            LEFT JOIN usuarios u ON uu.id_usuario = u.id_usuario
            WHERE uu.id_usuario = :id_usuario
        ");
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();
        
        $ubicaciones = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ubicacion = UsuarioUbicacion::fromArray($data);
            
            // Si hay datos de usuario, crear el objeto Usuario
            if (isset($data['nombre'])) {
                $usuario = new Usuario();
                $usuario->setIdUsuario($data['id_usuario'])
                        ->setNombre($data['nombre'])
                        ->setEmail($data['email'])
                        ->setRol($data['rol'])
                        ->setActivo($data['activo']);
                
                $ubicacion->setUsuario($usuario);
            }
            
            $ubicaciones[] = $ubicacion;
        }
        
        return $ubicaciones;
    }
    
    public function findByUbicacion(string $tipoUbicacion, int $idUbicacion): array {
        $stmt = $this->conexion->prepare("
            SELECT uu.*, u.nombre, u.email, u.rol, u.activo 
            FROM usuario_ubicacion uu
            LEFT JOIN usuarios u ON uu.id_usuario = u.id_usuario
            WHERE uu.tipo_ubicacion = :tipo_ubicacion AND uu.id_ubicacion = :id_ubicacion
        ");
        $stmt->bindParam(':tipo_ubicacion', $tipoUbicacion);
        $stmt->bindParam(':id_ubicacion', $idUbicacion);
        $stmt->execute();
        
        $ubicaciones = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ubicacion = UsuarioUbicacion::fromArray($data);
            
            // Si hay datos de usuario, crear el objeto Usuario
            if (isset($data['nombre'])) {
                $usuario = new Usuario();
                $usuario->setIdUsuario($data['id_usuario'])
                        ->setNombre($data['nombre'])
                        ->setEmail($data['email'])
                        ->setRol($data['rol'])
                        ->setActivo($data['activo']);
                
                $ubicacion->setUsuario($usuario);
            }
            
            $ubicaciones[] = $ubicacion;
        }
        
        return $ubicaciones;
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("
            SELECT uu.*, u.nombre, u.email, u.rol, u.activo 
            FROM usuario_ubicacion uu
            LEFT JOIN usuarios u ON uu.id_usuario = u.id_usuario
        ");
        
        $ubicaciones = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ubicacion = UsuarioUbicacion::fromArray($data);
            
            // Si hay datos de usuario, crear el objeto Usuario
            if (isset($data['nombre'])) {
                $usuario = new Usuario();
                $usuario->setIdUsuario($data['id_usuario'])
                        ->setNombre($data['nombre'])
                        ->setEmail($data['email'])
                        ->setRol($data['rol'])
                        ->setActivo($data['activo']);
                
                $ubicacion->setUsuario($usuario);
            }
            
            $ubicaciones[] = $ubicacion;
        }
        
        return $ubicaciones;
    }

    public function save(UsuarioUbicacion $usuarioUbicacion): UsuarioUbicacion {
        // Primero verificamos si ya existe esta relación
        $stmt = $this->conexion->prepare("
            SELECT COUNT(*) FROM usuario_ubicacion 
            WHERE id_usuario = :id_usuario 
            AND tipo_ubicacion = :tipo_ubicacion 
            AND id_ubicacion = :id_ubicacion
        ");
        
        $stmt->bindValue(':id_usuario', $usuarioUbicacion->getIdUsuario());
        $stmt->bindValue(':tipo_ubicacion', $usuarioUbicacion->getTipoUbicacion());
        $stmt->bindValue(':id_ubicacion', $usuarioUbicacion->getIdUbicacion());
        
        $stmt->execute();
        $exists = $stmt->fetchColumn() > 0;
        
        if (!$exists) {
            // Si no existe, la insertamos
            $stmtInsert = $this->conexion->prepare("
                INSERT INTO usuario_ubicacion (id_usuario, tipo_ubicacion, id_ubicacion)
                VALUES (:id_usuario, :tipo_ubicacion, :id_ubicacion)
            ");
            
            $stmtInsert->bindValue(':id_usuario', $usuarioUbicacion->getIdUsuario());
            $stmtInsert->bindValue(':tipo_ubicacion', $usuarioUbicacion->getTipoUbicacion());
            $stmtInsert->bindValue(':id_ubicacion', $usuarioUbicacion->getIdUbicacion());
            
            $stmtInsert->execute();
        }
        
        return $usuarioUbicacion;
    }

    public function delete(int $idUsuario, string $tipoUbicacion, int $idUbicacion): bool {
        $stmt = $this->conexion->prepare("
            DELETE FROM usuario_ubicacion 
            WHERE id_usuario = :id_usuario 
            AND tipo_ubicacion = :tipo_ubicacion 
            AND id_ubicacion = :id_ubicacion
        ");
        
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':tipo_ubicacion', $tipoUbicacion);
        $stmt->bindParam(':id_ubicacion', $idUbicacion);
        
        return $stmt->execute();
    }
    
    public function deleteByUsuario(int $idUsuario): bool {
        $stmt = $this->conexion->prepare("DELETE FROM usuario_ubicacion WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $idUsuario);
        return $stmt->execute();
    }
    
    public function deleteByUbicacion(string $tipoUbicacion, int $idUbicacion): bool {
        $stmt = $this->conexion->prepare("
            DELETE FROM usuario_ubicacion 
            WHERE tipo_ubicacion = :tipo_ubicacion AND id_ubicacion = :id_ubicacion
        ");
        $stmt->bindParam(':tipo_ubicacion', $tipoUbicacion);
        $stmt->bindParam(':id_ubicacion', $idUbicacion);
        return $stmt->execute();
    }
}
