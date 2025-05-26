<?php
namespace controller;

use model\service\UsuarioService;
use model\service\Usuario_UbicacionService;
use model\enum\RolEnum;
use util\Session;
use Exception;

require_once(__DIR__ . '/../model/service/UsuarioService.php');
require_once(__DIR__ . '/../model/service/Usuario_UbicacionService.php');
require_once(__DIR__ . '/../model/entity/Usuario.php');
require_once(__DIR__ . '/../model/enum/RolEnum.php');
include_once(__DIR__ . '/../util/Session.php');

class UsuarioController
{
    private UsuarioService $userService;
    private Usuario_UbicacionService $userUbicacionService;
    private Session $session;

    public function __construct()
    {
        $this->userService = new UsuarioService();
        $this->userUbicacionService = new Usuario_UbicacionService();
        $this->session = new Session();
    }

    public function login($email, $password): array|bool
    {
        if (empty($email) || empty($password)) {
            throw new Exception("El email y la contraseña son obligatorios");
        }

        $ip = $this->getClientIp();
        $user = $this->userService->verifyLogin($email, $password);

        if (!$user) {
            throw new Exception("Email o contraseña incorrectos");
        }

        $ubicaciones = $this->userUbicacionService->getUbicacionesByUsuario($user['id']);
        $user['ubicaciones'] = $ubicaciones;

        session_regenerate_id(true);

        $_SESSION['id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['ubicaciones'] = $ubicaciones;

        $this->session->set('id', $user['id']);
        $this->session->set('nombre', $user['nombre']);
        $this->session->set('email', $user['email']);
        $this->session->set('rol', $user['rol']);
        $this->session->set('ubicaciones', $ubicaciones);

        return $user;
    }

    public function register($nombre, $email, $password, $rol = null): void
    {
        if (empty($nombre) || empty($email) || empty($password)) {
            throw new Exception("El nombre, email y contraseña son obligatorios");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido");
        }

        if ($rol === null || $this->session->getUserData('rol') !== RolEnum::ADMINISTRADOR) {
            $rol = RolEnum::USUARIO_BOTIQUIN;
        }

        if (!RolEnum::isValid($rol)) {
            $rol = RolEnum::USUARIO_BOTIQUIN;
        }

        $existingUser = $this->userService->getUserByEmail($email);
        if ($existingUser) {
            throw new Exception("El email ya está registrado");
        }

        $passwordValidation = $this->validatePassword($password);
        if ($passwordValidation !== true) {
            throw new Exception(implode(", ", $passwordValidation));
        }

        if (!$this->userService->createUser($nombre, $email, $password, $rol)) {
            throw new Exception("Error al crear el usuario");
        }
    }

    public function updateProfile($id, $nombre, $email, $password = null, $rol = null, $ubicaciones = null): void
    {
        if (empty($id) || empty($nombre) || empty($email)) {
            throw new Exception("El ID, nombre y email son obligatorios");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido");
        }

        $currentUser = $this->userService->getUserById($id);
        if (!$currentUser) {
            throw new Exception("Usuario no encontrado");
        }

        if ($email !== $currentUser->getEmail()) {
            $existingUser = $this->userService->getUserByEmail($email);
            if ($existingUser && $existingUser->getId() !== $id) {
                throw new Exception("El email ya está en uso por otro usuario");
            }
        }

        if (!empty($password)) {
            $passwordValidation = $this->validatePassword($password);
            if ($passwordValidation !== true) {
                throw new Exception(implode(", ", $passwordValidation));
            }
        }

        if ($rol === null) {
            $rol = $currentUser->getRolValue();
        } elseif (!RolEnum::isValid($rol)) {
            throw new Exception("El rol especificado no es válido");
        }

        if (!$this->userService->updateUser($id, $nombre, $email, $password, $rol)) {
            throw new Exception("Error al actualizar el usuario");
        }

        if ($ubicaciones !== null) {
            $this->userUbicacionService->deleteUbicacionesByUsuario($id);

            foreach ($ubicaciones as $ubicacion) {
                if (!isset($ubicacion['tipo']) || !isset($ubicacion['id'])) {
                    continue;
                }
                $this->userUbicacionService->createUsuarioUbicacion(
                    $id,
                    $ubicacion['tipo'],
                    $ubicacion['id']
                );
            }


            if ((int)$id === (int)$this->session->get('id')) {
                $userUbicaciones = $this->userUbicacionService->getUbicacionesByUsuario($id);
                $_SESSION['ubicaciones'] = $userUbicaciones;
                $this->session->set('ubicaciones', $userUbicaciones);
            }
        }

        if ((int)$id === (int)$this->session->get('id')) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['email'] = $email;
            $_SESSION['rol'] = $rol;

            $this->session->set('nombre', $nombre);
            $this->session->set('email', $email);
            $this->session->set('rol', $rol);
        }
    }

    public function deleteUser($id): void
    {
        if (empty($id)) {
            throw new Exception("El ID de usuario es obligatorio");
        }

        if ((int)$id === (int)$this->session->get('id')) {
            throw new Exception("No puedes eliminar tu propio usuario");
        }

        $user = $this->userService->getUserById($id);
        if (!$user) {
            throw new Exception("El usuario no existe");
        }

        $this->userUbicacionService->deleteUbicacionesByUsuario($id);

        if (!$this->userService->deleteUser($id)) {
            throw new Exception("Error al eliminar el usuario");
        }
    }

    public function getAllUsers(): array
    {
        return $this->userService->getAllUsuarios();
    }

    public function getRolOptions(): array
    {
        return $this->userService->getRolOptions();
    }

    public function validatePassword($password): array|bool
    {
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = "La contraseña debe tener al menos 8 caracteres.";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "La contraseña debe tener al menos una letra mayúscula.";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "La contraseña debe tener al menos una letra minúscula.";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "La contraseña debe tener al menos un número.";
        }
        if (!preg_match('/[\W_]/', $password)) {
            $errors[] = "La contraseña debe tener al menos un carácter especial.";
        }

        return empty($errors) ? true : $errors;
    }

    public function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return 'unknown';
    }

    public function getUserById($id) {
        try {
            $usuario = $this->userService->getUserById($id);
            if ($usuario) {
                $ubicaciones = $this->userUbicacionService->getUbicacionesByUsuario($id);
                $usuario->setUbicaciones($ubicaciones);
            }
            return $usuario;
        } catch (Exception $e) {
            throw new Exception("Error al obtener el usuario: " . $e->getMessage());
        }
    }

    public function asignarUbicacion($idUsuario, $tipoUbicacion, $idUbicacion): bool {
        try {
            return $this->userUbicacionService->createUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
        } catch (Exception $e) {
            throw new Exception("Error al asignar ubicación al usuario: " . $e->getMessage());
        }
    }

    public function eliminarUbicacion($idUsuario, $tipoUbicacion, $idUbicacion): bool {
        try {
            return $this->userUbicacionService->deleteUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
        } catch (Exception $e) {
            throw new Exception("Error al eliminar ubicación del usuario: " . $e->getMessage());
        }
    }

    public function getUbicacionesUsuario($idUsuario): array {
        try {
            return $this->userUbicacionService->getUbicacionesByUsuario($idUsuario);
        } catch (Exception $e) {
            throw new Exception("Error al obtener las ubicaciones del usuario: " . $e->getMessage());
        }
    }

    public function getRolIdByName($rolName): int
    {
        $roles = RolEnum::getKeyValues();
        foreach ($roles as $id => $name) {
            if ($name === $rolName) {
                return $id;
            }
        }
        return RolEnum::USUARIO_BOTIQUIN;
    }

    public function isValidRolId($rolId): bool
    {
        $rolId = (int)$rolId;
        $roles = RolEnum::getKeyValues();
        return array_key_exists($rolId, $roles);
    }

    public function isValidRol(string $rol): bool
    {
        return RolEnum::isValid($rol);
    }
}
