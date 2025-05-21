<?php

use model\service\UserService;
use model\entity\Usuario;
use App\model\enum\RolEnum;

require_once(__DIR__ . '/../model/service/userService.php');
require_once(__DIR__ . '/../model/entity/Usuario.php');
require_once(__DIR__ . '/../model/enum/RolEnum.php');
include_once(__DIR__ . '/../util/session.php');

class UsuarioController
{
    private UserService $userService;
    private Session $session;

    public function __construct()
    {
        $this->userService = new UserService();
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

        session_regenerate_id(true);

        $_SESSION['id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['rol'] = $user['rol'];

        $this->session->set('id', $user['id']);
        $this->session->set('nombre', $user['nombre']);
        $this->session->set('email', $user['email']);
        $this->session->set('rol', $user['rol']);

        return $user;
    }

    public function register($nombre, $email, $password, $rol = RolEnum::USUARIO_BOTIQUIN->value): void
    {
        if (empty($nombre) || empty($email) || empty($password)) {
            throw new Exception("El nombre, email y contraseña son obligatorios");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido");
        }

        if (!RolEnum::isValid($rol)) {
            throw new Exception("El rol especificado no es válido");
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

    public function updateProfile($id, $nombre, $email, $password = null, $rol = null): void
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
}
