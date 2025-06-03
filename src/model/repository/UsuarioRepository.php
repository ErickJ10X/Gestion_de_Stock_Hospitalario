<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\Usuario;
use model\entity\UsuarioUbicacion;
use PDO;

class UsuarioRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?Usuario {
        $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        $usuario = Usuario::fromArray($data);
        
        // Cargar las ubicaciones asociadas al usuario
        $stmtUbicaciones = $this->conexion->prepare("SELECT * FROM usuario_ubicacion WHERE id_usuario = :id_usuario");
        $stmtUbicaciones->bindParam(':id_usuario', $id);
        $stmtUbicaciones->execute();
        
        $ubicaciones = [];
        while ($ubicacionData = $stmtUbicaciones->fetch(PDO::FETCH_ASSOC)) {
            $ubicacion = UsuarioUbicacion::fromArray($ubicacionData);
            $ubicacion->setUsuario($usuario);
            $ubicaciones[] = $ubicacion;
        }
        
        $usuario->setUbicaciones($ubicaciones);
        
        return $usuario;
    }
    
    public function findByEmail(string $email): ?Usuario {
        $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        $usuario = Usuario::fromArray($data);
        
        // Cargar las ubicaciones asociadas al usuario
        $stmtUbicaciones = $this->conexion->prepare("SELECT * FROM usuario_ubicacion WHERE id_usuario = :id_usuario");
        $stmtUbicaciones->bindParam(':id_usuario', $usuario->getIdUsuario());
        $stmtUbicaciones->execute();
        
        $ubicaciones = [];
        while ($ubicacionData = $stmtUbicaciones->fetch(PDO::FETCH_ASSOC)) {
            $ubicacion = UsuarioUbicacion::fromArray($ubicacionData);
            $ubicacion->setUsuario($usuario);
            $ubicaciones[] = $ubicacion;
        }
        
        $usuario->setUbicaciones($ubicaciones);
        
        return $usuario;
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("SELECT * FROM usuarios");
        
        $usuarios = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = Usuario::fromArray($data);
        }
        
        return $usuarios;
    }
    
    public function findByRol(string $rol): array {
        $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE rol = :rol");
        $stmt->bindParam(':rol', $rol);
        $stmt->execute();
        
        $usuarios = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = Usuario::fromArray($data);
        }
        
        return $usuarios;
    }
    
    public function findActive(): array {
        $stmt = $this->conexion->query("SELECT * FROM usuarios WHERE activo = 1");
        
        $usuarios = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = Usuario::fromArray($data);
        }
        
        return $usuarios;
    }
    
    public function findByUbicacion(string $tipoUbicacion, int $idUbicacion): array {
        $stmt = $this->conexion->prepare("
            SELECT u.* 
            FROM usuarios u
            JOIN usuario_ubicacion uu ON u.id_usuario = uu.id_usuario
            WHERE uu.tipo_ubicacion = :tipo_ubicacion AND uu.id_ubicacion = :id_ubicacion
        ");
        $stmt->bindParam(':tipo_ubicacion', $tipoUbicacion);
        $stmt->bindParam(':id_ubicacion', $idUbicacion);
        $stmt->execute();
        
        $usuarios = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = Usuario::fromArray($data);
        }
        
        return $usuarios;
    }

    public function save(Usuario $usuario): Usuario {
        if ($usuario->getIdUsuario() === null) {
            return $this->insert($usuario);
        } else {
            return $this->update($usuario);
        }
    }

    private function insert(Usuario $usuario): Usuario {
        $stmt = $this->conexion->prepare("
            INSERT INTO usuarios (nombre, email, contrasena, rol, activo)
            VALUES (:nombre, :email, :contrasena, :rol, :activo)
        ");
        
        $stmt->bindValue(':nombre', $usuario->getNombre());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':contrasena', $usuario->getContrasena());
        $stmt->bindValue(':rol', $usuario->getRol());
        $stmt->bindValue(':activo', $usuario->isActivo(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $usuario->setIdUsuario($this->conexion->lastInsertId());
        
        // Guardar las ubicaciones si existen
        $this->saveUbicaciones($usuario);
        
        return $usuario;
    }

    private function update(Usuario $usuario): Usuario {
        $stmt = $this->conexion->prepare("
            UPDATE usuarios
            SET nombre = :nombre,
                email = :email,
                rol = :rol,
                activo = :activo
            WHERE id_usuario = :id_usuario
        ");
        
        $stmt->bindValue(':nombre', $usuario->getNombre());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':rol', $usuario->getRol());
        $stmt->bindValue(':activo', $usuario->isActivo(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_usuario', $usuario->getIdUsuario());
        
        $stmt->execute();
        
        // Actualizar ubicaciones
        $this->saveUbicaciones($usuario);
        
        return $usuario;
    }
    
    public function updatePassword(int $idUsuario, string $hashedPassword): bool {
        $stmt = $this->conexion->prepare("
            UPDATE usuarios
            SET contrasena = :contrasena
            WHERE id_usuario = :id_usuario
        ");
        
        $stmt->bindParam(':contrasena', $hashedPassword);
        $stmt->bindParam(':id_usuario', $idUsuario);
        
        return $stmt->execute();
    }
    
    private function saveUbicaciones(Usuario $usuario): void {
        // Si el usuario tiene ubicaciones definidas, las guardamos
        if (!empty($usuario->getUbicaciones())) {
            // Primero eliminamos todas las ubicaciones actuales del usuario
            $stmtDelete = $this->conexion->prepare("DELETE FROM usuario_ubicacion WHERE id_usuario = :id_usuario");
            $stmtDelete->bindValue(':id_usuario', $usuario->getIdUsuario());
            $stmtDelete->execute();
            
            // Luego insertamos las nuevas ubicaciones
            $stmtInsert = $this->conexion->prepare("
                INSERT INTO usuario_ubicacion (id_usuario, tipo_ubicacion, id_ubicacion)
                VALUES (:id_usuario, :tipo_ubicacion, :id_ubicacion)
            ");
            
            foreach ($usuario->getUbicaciones() as $ubicacion) {
                $stmtInsert->bindValue(':id_usuario', $usuario->getIdUsuario());
                $stmtInsert->bindValue(':tipo_ubicacion', $ubicacion->getTipoUbicacion());
                $stmtInsert->bindValue(':id_ubicacion', $ubicacion->getIdUbicacion());
                $stmtInsert->execute();
            }
        }
    }

    public function delete(int $id): bool {
        // Primero eliminamos las ubicaciones asociadas
        $stmtUbicaciones = $this->conexion->prepare("DELETE FROM usuario_ubicacion WHERE id_usuario = :id_usuario");
        $stmtUbicaciones->bindParam(':id_usuario', $id);
        $stmtUbicaciones->execute();
        
        // Luego eliminamos el usuario
        $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function softDelete(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE usuarios SET activo = 0 WHERE id_usuario = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
