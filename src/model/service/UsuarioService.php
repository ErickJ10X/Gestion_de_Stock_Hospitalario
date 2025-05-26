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
            $usuario->setIdRol($rol);
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

    public function createUser($nombre, $email, $password, $rol = RolEnum::USUARIO_BOTIQUIN, $activo = true): bool
    {
        try {
            // Asegurarse de que el rol es un entero
            $rol = (int)$rol;

            // Verifica que el rol sea válido
            if (!RolEnum::isValid($rol)) {
                throw new Exception("El rol especificado no es válido");
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
                    'rol' => $usuario->getIdRol()
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
}