<?php

namespace model\service;

require_once(__DIR__ . '/../repository/UsuarioRepository.php');
require_once(__DIR__ . '/../repository/UsuarioUbicacionRepository.php');

use model\entity\Usuario;
use model\entity\UsuarioUbicacion;
use model\repository\UsuarioRepository;
use model\repository\UsuarioUbicacionRepository;
use InvalidArgumentException;

class UsuarioService {
    private UsuarioRepository $usuarioRepository;
    private ?UsuarioUbicacionRepository $ubicacionRepository;

    public function __construct() {
        $this->usuarioRepository = new UsuarioRepository();
        $this->ubicacionRepository = new UsuarioUbicacionRepository();
    }

    public function getUsuarioById(int $id): ?Usuario {
        return $this->usuarioRepository->findById($id);
    }

    public function getUsuarioByEmail(string $email): ?Usuario {
        return $this->usuarioRepository->findByEmail($email);
    }

    public function getAllUsuarios(): array {
        return $this->usuarioRepository->findAll();
    }

    public function getUsuariosByRol(string $rol): array {
        return $this->usuarioRepository->findByRol($rol);
    }

    public function getActiveUsuarios(): array {
        return $this->usuarioRepository->findActive();
    }

    public function getUsuariosByUbicacion(string $tipoUbicacion, int $idUbicacion): array {
        return $this->usuarioRepository->findByUbicacion($tipoUbicacion, $idUbicacion);
    }

    public function createUsuario(array $data): Usuario {
        $this->validateUsuarioData($data);
        
        // Verificar si ya existe un usuario con el mismo email
        if ($this->usuarioRepository->findByEmail($data['email'])) {
            throw new InvalidArgumentException('Ya existe un usuario con este correo electrónico');
        }
        
        // Crear el usuario
        $usuario = new Usuario();
        $usuario->setNombre($data['nombre'])
                ->setEmail($data['email'])
                ->setContrasena(Usuario::hashContrasena($data['contrasena']))
                ->setRol($data['rol'])
                ->setActivo($data['activo'] ?? true);
        
        // Guardar el usuario
        $usuario = $this->usuarioRepository->save($usuario);
        
        // Procesar ubicaciones si existen
        if (isset($data['ubicaciones']) && is_array($data['ubicaciones'])) {
            $this->procesarUbicaciones($usuario, $data['ubicaciones']);
        }
        
        return $usuario;
    }

    public function updateUsuario(int $id, array $data): Usuario {
        $usuario = $this->usuarioRepository->findById($id);
        if (!$usuario) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }
        
        // Verificar si el email ya está en uso por otro usuario
        if (isset($data['email']) && $data['email'] !== $usuario->getEmail()) {
            $existingUser = $this->usuarioRepository->findByEmail($data['email']);
            if ($existingUser && $existingUser->getIdUsuario() !== $id) {
                throw new InvalidArgumentException('Ya existe otro usuario con este correo electrónico');
            }
            $usuario->setEmail($data['email']);
        }
        
        if (isset($data['nombre'])) {
            $usuario->setNombre($data['nombre']);
        }
        
        if (isset($data['rol'])) {
            $usuario->setRol($data['rol']);
        }
        
        if (isset($data['activo'])) {
            $usuario->setActivo($data['activo']);
        }
        
        // Actualizar el usuario
        $usuario = $this->usuarioRepository->save($usuario);
        
        // Procesar ubicaciones si existen
        if (isset($data['ubicaciones']) && is_array($data['ubicaciones'])) {
            $this->procesarUbicaciones($usuario, $data['ubicaciones']);
        }
        
        return $usuario;
    }
    
    public function cambiarContrasena(int $idUsuario, string $nuevaContrasena): bool {
        $usuario = $this->usuarioRepository->findById($idUsuario);
        if (!$usuario) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }
        
        if (strlen($nuevaContrasena) < 6) {
            throw new InvalidArgumentException('La contraseña debe tener al menos 6 caracteres');
        }
        
        $hashedPassword = Usuario::hashContrasena($nuevaContrasena);
        return $this->usuarioRepository->updatePassword($idUsuario, $hashedPassword);
    }

    public function login(string $email, string $contrasena): ?Usuario {
        $usuario = $this->usuarioRepository->findByEmail($email);
        
        if (!$usuario || !$usuario->verificarContrasena($contrasena)) {
            return null;
        }
        
        if (!$usuario->isActivo()) {
            throw new InvalidArgumentException('Esta cuenta está desactivada');
        }
        
        return $usuario;
    }

    public function deleteUsuario(int $id): bool {
        return $this->usuarioRepository->delete($id);
    }

    public function desactivarUsuario(int $id): bool {
        return $this->usuarioRepository->softDelete($id);
    }
    
    public function asignarUbicacion(int $idUsuario, string $tipoUbicacion, int $idUbicacion): bool {
        $usuario = $this->usuarioRepository->findById($idUsuario);
        if (!$usuario) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }
        
        $ubicacion = new UsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
        $this->ubicacionRepository->save($ubicacion);
        
        return true;
    }
    
    public function eliminarUbicacion(int $idUsuario, string $tipoUbicacion, int $idUbicacion): bool {
        return $this->ubicacionRepository->delete($idUsuario, $tipoUbicacion, $idUbicacion);
    }

    private function validateUsuarioData(array $data): void {
        if (!isset($data['nombre']) || empty($data['nombre'])) {
            throw new InvalidArgumentException('El nombre es obligatorio');
        }
        
        if (!isset($data['email']) || empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El email es obligatorio y debe tener un formato válido');
        }
        
        if (isset($data['contrasena']) && (empty($data['contrasena']) || strlen($data['contrasena']) < 6)) {
            throw new InvalidArgumentException('La contraseña es obligatoria y debe tener al menos 6 caracteres');
        }
        
        if (!isset($data['rol']) || empty($data['rol'])) {
            throw new InvalidArgumentException('El rol es obligatorio');
        }
    }
    
    private function procesarUbicaciones(Usuario $usuario, array $ubicacionesData): void {
        // Eliminar ubicaciones actuales
        $this->ubicacionRepository->deleteByUsuario($usuario->getIdUsuario());
        
        $ubicaciones = [];
        foreach ($ubicacionesData as $ubicacionData) {
            if (!isset($ubicacionData['tipo_ubicacion']) || !isset($ubicacionData['id_ubicacion'])) {
                continue;
            }
            
            $ubicacion = new UsuarioUbicacion(
                $usuario->getIdUsuario(),
                $ubicacionData['tipo_ubicacion'],
                $ubicacionData['id_ubicacion']
            );
            
            $ubicacion->setUsuario($usuario);
            $this->ubicacionRepository->save($ubicacion);
            $ubicaciones[] = $ubicacion;
        }
        
        $usuario->setUbicaciones($ubicaciones);
    }
}
