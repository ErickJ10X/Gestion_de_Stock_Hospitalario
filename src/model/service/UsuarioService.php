<?php

namespace model\service;
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../repository/UsuarioRepository.php');
require_once(__DIR__ . '/../entity/Usuario.php');
require_once(__DIR__ . '/../enum/RolEnum.php');

use model\entity\Usuario;
use model\enum\RolEnum;
use model\repository\UsuarioRepository;
use PDOException;
use Exception;

class UsuarioService
{
    private UsuarioRepository $usuarioRepository;

    public function __construct()
    {
        $this->usuarioRepository = new UsuarioRepository();
    }

    public function getAllUsuarios(): array
    {
        try {
            return $this->usuarioRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al cargar los usuarios: " . $e->getMessage());
        }
    }

    public function deleteUser($id): bool
    {
        try {
            return $this->usuarioRepository->deleteById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el usuario: " . $e->getMessage());
        }
    }

    public function updateUser($id, $nombre, $email, $password, $rol, $activo = true): bool
    {
        try {
            $usuario = $this->usuarioRepository->findById($id);
            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }

            $usuario->setNombre($nombre);
            $usuario->setEmail($email);
            
            // Verifica que el rol sea un valor válido del enum
            if (!RolEnum::isValid($rol)) {
                $rol = RolEnum::USUARIO_BOTIQUIN;
            }
            
            $usuario->setRol($rol);
            $usuario->setActivo($activo);

            if (!empty($password)) {
                $usuario->setContrasena($password);
                $usuario->hashPassword();
            }

            return $this->usuarioRepository->update($usuario);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }

    public function createUser($nombre, $email, $password, $rol = null, $activo = true): bool
    {
        try {
            // Si no se especifica rol, usar el predeterminado (Usuario de botiquín)
            if ($rol === null) {
                $rol = RolEnum::USUARIO_BOTIQUIN;
            }
            
            // Verifica que el rol sea un valor válido del enum
            if (!RolEnum::isValid($rol)) {
                $rol = RolEnum::USUARIO_BOTIQUIN;
            }

            $usuario = new Usuario(
                null,
                $nombre,
                $email,
                $password,
                $rol,
                $activo
            );

            $usuario->hashPassword();

            return $this->usuarioRepository->save($usuario);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el usuario: " . $e->getMessage());
        }
    }

    public function verifyLogin($email, $contrasena): false|array
    {
        try {
            $usuario = $this->usuarioRepository->findByEmail($email);

            if ($usuario && $usuario->verificarContrasena($contrasena)) {
                return [
                    'id' => $usuario->getIdUsuario(),
                    'nombre' => $usuario->getNombre(),
                    'email' => $usuario->getEmail(),
                    'rol' => $usuario->getRol()
                ];
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar el login: " . $e->getMessage());
        }
    }

    public function getUserByEmail($email): ?Usuario
    {
        try {
            return $this->usuarioRepository->findByEmail($email);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario por email: " . $e->getMessage());
        }
    }
    
    public function getUserById($id): ?Usuario
    {
        try {
            return $this->usuarioRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario por ID: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene todas las opciones de roles disponibles
     */
    public function getRolOptions(): array
    {
        return RolEnum::getValues();
    }
    
    /**
     * Obtiene el mapeo de clave => valor de los roles
     */
    public function getRolKeyValues(): array
    {
        return RolEnum::getKeyValues();
    }
}
