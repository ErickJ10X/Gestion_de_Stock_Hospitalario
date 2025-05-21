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

    public function updateUser($id, $nombre, $email, $password, $rol): bool
    {
        try {
            $usuario = $this->usuarioRepository->findById($id);
            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }
            
            $usuarioActualizado = new Usuario(
                $id,
                $nombre,
                $email,
                !empty($password) ? $password : $usuario->getContrasena(),
                $rol
            );
            
            return $this->usuarioRepository->update($usuarioActualizado);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }

    public function createUser($nombre, $email, $password, $rol = 'Usuario de botiquín'): bool
    {
        try {
            $usuario = new Usuario(
                0,
                $nombre,
                $email,
                $password,
                $rol
            );
            
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
                    'id' => $usuario->getId(),
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

    public function getUserById($userId): ?Usuario
    {
        try {
            return $this->usuarioRepository->findById($userId);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario por ID: " . $e->getMessage());
        }
    }
    
    public function getRolOptions(): array
    {
        return RolEnum::getValues();
    }

    public function getUsuarioById($id) {
        try {
            return $this->usuarioRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        }
    }
}
