<?php

namespace services;

use Models\Usuario;
use Models\UsuarioUbicacion;
use Repositories\Interfaces\UsuarioRepositoryInterface;
use Repositories\Interfaces\UsuarioUbicacionRepositoryInterface;
use Services\Interfaces\UsuarioServiceInterface;
use src\enum\RolEnum;

class UsuarioService implements UsuarioServiceInterface {
    private UsuarioRepositoryInterface $usuarioRepository;
    private UsuarioUbicacionRepositoryInterface $usuarioUbicacionRepository;

    public function __construct(
        UsuarioRepositoryInterface $usuarioRepository,
        UsuarioUbicacionRepositoryInterface $usuarioUbicacionRepository
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->usuarioUbicacionRepository = $usuarioUbicacionRepository;
    }

    public function getAllUsuarios(): array {
        return $this->usuarioRepository->findAll();
    }

    public function getUsuarioById(int $id): ?Usuario {
        return $this->usuarioRepository->findById($id);
    }

    public function getUsuarioByEmail(string $email): ?Usuario {
        return $this->usuarioRepository->findByEmail($email);
    }

    public function getUsuariosByRol(string $rol): array {
        if (!RolEnum::isValid($rol)) {
            throw new \InvalidArgumentException('Rol de usuario inválido');
        }
        
        return $this->usuarioRepository->findByRol($rol);
    }

    public function getActiveUsuarios(): array {
        return $this->usuarioRepository->findActive();
    }

    public function getUsuariosByUbicacion(string $tipoUbicacion, int $idUbicacion): array {
        // Validar tipo de ubicación
        if (!in_array($tipoUbicacion, UsuarioUbicacion::TIPOS_VALIDOS)) {
            throw new \InvalidArgumentException('Tipo de ubicación inválido');
        }
        
        return $this->usuarioRepository->findByUbicacion($tipoUbicacion, $idUbicacion);
    }
    
    public function autenticar(string $email, string $contrasena): ?Usuario {
        $usuario = $this->usuarioRepository->findByEmail($email);
        
        if ($usuario && $usuario->verificarContrasena($contrasena) && $usuario->isActivo()) {
            return $usuario;
        }
        
        return null;
    }

    public function createUsuario(Usuario $usuario, string $contrasena, array $ubicaciones = []): Usuario {
        // Validaciones
        $this->validateUsuario($usuario);
        
        // Verificar que el email no exista ya
        $existingUser = $this->usuarioRepository->findByEmail($usuario->getEmail());
        if ($existingUser !== null) {
            throw new \InvalidArgumentException('Ya existe un usuario con este email');
        }
        
        // Encriptar contraseña
        $usuario->setContrasena(Usuario::hashContrasena($contrasena));
        
        // Guardar usuario
        $usuario = $this->usuarioRepository->save($usuario);
        
        // Guardar ubicaciones
        if (!empty($ubicaciones)) {
            foreach ($ubicaciones as $ubicacion) {
                // Validar datos de ubicación
                if (!isset($ubicacion['tipo']) || !isset($ubicacion['id'])) {
                    continue;
                }
                
                $tipo = $ubicacion['tipo'];
                $id = (int)$ubicacion['id'];
                
                // Validar tipo
                if (!in_array($tipo, UsuarioUbicacion::TIPOS_VALIDOS)) {
                    continue;
                }
                
                // Guardar relación usuario-ubicación
                $usuarioUbicacion = new UsuarioUbicacion($usuario->getIdUsuario(), $tipo, $id);
                $this->usuarioUbicacionRepository->save($usuarioUbicacion);
            }
        }
        
        // Recargar usuario con ubicaciones
        return $this->usuarioRepository->findById($usuario->getIdUsuario());
    }

    public function updateUsuario(Usuario $usuario, ?string $contrasena = null): bool {
        // Validaciones
        $this->validateUsuario($usuario);
        
        if ($usuario->getIdUsuario() === null) {
            throw new \InvalidArgumentException('No se puede actualizar un usuario sin ID');
        }
        
        // Verificar que el usuario existe
        $existingUsuario = $this->usuarioRepository->findById($usuario->getIdUsuario());
        if ($existingUsuario === null) {
            throw new \InvalidArgumentException('El usuario no existe');
        }
        
        // Verificar que el email no esté en uso por otro usuario
        $otherUsuario = $this->usuarioRepository->findByEmail($usuario->getEmail());
        if ($otherUsuario !== null && $otherUsuario->getIdUsuario() !== $usuario->getIdUsuario()) {
            throw new \InvalidArgumentException('El email ya está en uso por otro usuario');
        }
        
        // Si se proporciona una nueva contraseña, encriptarla
        if ($contrasena !== null) {
            $usuario->setContrasena(Usuario::hashContrasena($contrasena));
        } else {
            // Mantener la contraseña actual
            $usuario->setContrasena($existingUsuario->getContrasena());
        }
        
        return $this->usuarioRepository->update($usuario);
    }
    
    public function updateUbicaciones(int $idUsuario, array $ubicaciones): bool {
        // Verificar que el usuario existe
        $usuario = $this->usuarioRepository->findById($idUsuario);
        if ($usuario === null) {
            throw new \InvalidArgumentException('El usuario no existe');
        }
        
        // Eliminar todas las ubicaciones actuales
        $this->usuarioUbicacionRepository->deleteAllByUsuario($idUsuario);
        
        // Guardar nuevas ubicaciones
        $success = true;
        foreach ($ubicaciones as $ubicacion) {
            // Validar datos de ubicación
            if (!isset($ubicacion['tipo']) || !isset($ubicacion['id'])) {
                continue;
            }
            
            $tipo = $ubicacion['tipo'];
            $id = (int)$ubicacion['id'];
            
            // Validar tipo
            if (!in_array($tipo, UsuarioUbicacion::TIPOS_VALIDOS)) {
                continue;
            }
            
            // Guardar relación usuario-ubicación
            $usuarioUbicacion = new UsuarioUbicacion($idUsuario, $tipo, $id);
            $result = $this->usuarioUbicacionRepository->save($usuarioUbicacion);
            
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }

    public function deleteUsuario(int $id): bool {
        // Verificar que el usuario existe
        $existingUsuario = $this->usuarioRepository->findById($id);
        if ($existingUsuario === null) {
            throw new \InvalidArgumentException('El usuario no existe');
        }
        
        return $this->usuarioRepository->delete($id);
    }

    public function activateUsuario(int $id): bool {
        // Verificar que el usuario existe
        $existingUsuario = $this->usuarioRepository->findById($id);
        if ($existingUsuario === null) {
            throw new \InvalidArgumentException('El usuario no existe');
        }
        
        return $this->usuarioRepository->activate($id);
    }

    public function deactivateUsuario(int $id): bool {
        // Verificar que el usuario existe
        $existingUsuario = $this->usuarioRepository->findById($id);
        if ($existingUsuario === null) {
            throw new \InvalidArgumentException('El usuario no existe');
        }
        
        return $this->usuarioRepository->deactivate($id);
    }
    
    public function cambiarContrasena(int $id, string $contrasena): bool {
        // Verificar que el usuario existe
        $usuario = $this->usuarioRepository->findById($id);
        if ($usuario === null) {
            throw new \InvalidArgumentException('El usuario no existe');
        }
        
        // Actualizar contraseña
        $usuario->setContrasena(Usuario::hashContrasena($contrasena));
        
        return $this->usuarioRepository->update($usuario);
    }
    
    private function validateUsuario(Usuario $usuario): void {
        if (empty($usuario->getNombre())) {
            throw new \InvalidArgumentException('El nombre del usuario no puede estar vacío');
        }
        
        if (empty($usuario->getEmail())) {
            throw new \InvalidArgumentException('El email del usuario no puede estar vacío');
        }
        
        if (!filter_var($usuario->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('El email del usuario no es válido');
        }
        
        if (!RolEnum::isValid($usuario->getRol())) {
            throw new \InvalidArgumentException('Rol de usuario inválido');
        }
    }
}
