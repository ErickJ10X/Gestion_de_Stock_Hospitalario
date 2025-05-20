<?php
require_once(__DIR__ . '/../service/userService.php');
include_once(__DIR__ . '/../util/session.php');

class UserController
{
    private UserService $userService;
    private Session $session;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->session = new Session();
    }

    public function login($username, $password): array|bool
    {
        if (empty($username) || empty($password)) {
            throw new Exception("El nombre de usuario y la contraseña son obligatorios");
        }

        $ip = $this->getClientIp();
        $user = $this->userService->verifyLogin($username, $password);

        if (!$user) {
            throw new Exception("Nombre de usuario o contraseña incorrectos");
        }

        session_regenerate_id(true);

        $_SESSION['id'] = $user['id'];
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol'] = $user['rol'];

        $this->session->set('id', $user['id']);
        $this->session->set('usuario', $user['usuario']);
        $this->session->set('rol', $user['rol']);

        return $user;
    }

    public function register($name, $email, $password): void
    {
        if (empty($username) || empty($password)) {
            throw new Exception("El nombre de usuario y la contraseña son obligatorios");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido");
        }

        $verification = $this->userService->verifyUserAndEmailExist($username, $email);

        if ($verification['usernameExists']) {
            throw new Exception("El nombre de usuario ya existe");
        }

        if ($verification['emailExists']) {
            throw new Exception("El email ya está en uso");
        }

        $passwordValidation = $this->validatePassword($password);
        if ($passwordValidation !== true) {
            throw new Exception(implode(", ", $passwordValidation));
        }

        $this->userService->createUser($name, $email, $password);
    }

    public function updateProfile($name, $surname, $username, $email, $password): void
    {
        $currentUsername = $_SESSION['usuario'];
        $userId = $_SESSION['id'];

        if (empty($name) || empty($surname) || empty($username) || empty($email)) {
            throw new Exception("Todos los campos son obligatorios excepto la contraseña");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido");
        }

        $verification = $this->userService->verifyUserAndEmailExist($username, $email, $userId);

        if ($verification['usernameExists']) {
            throw new Exception("El nombre de usuario ya existe");
        }

        if ($verification['emailExists']) {
            throw new Exception("El email ya está en uso");
        }

        if (!empty($password)) {
            $passwordValidation = $this->validatePassword($password);
            if ($passwordValidation !== true) {
                throw new Exception(implode(", ", $passwordValidation));
            }
        }

        $this->userService->updateUser($name, $surname, $username, $email, $password, $userId);

        if ($username !== $currentUsername) {
            $_SESSION['usuario'] = $username;
            $this->session->set('usuario', $username);
        }
    }

    public function deleteUser($username): void
    {
        if (empty($username)) {
            throw new Exception("El nombre de usuario es obligatorio");
        }

        if ($username === $this->session->get('usuario')) {
            throw new Exception("No puedes eliminar tu propio usuario");
        }

        if (!$this->userService->verifyUserExist($username)) {
            throw new Exception("El usuario no existe");
        }

        $this->userService->deleteUser($username);
    }

    public function getAllUsers(): false|PDOStatement
    {
        return $this->userService->getAllUsers();
    }

    public function validatePassword($password): array|bool
    {
        $errors = [];
        if (strlen($password) < 8) {
            return array_merge($errors, ["La contraseña debe tener al menos 8 caracteres."]);
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return array_merge($errors, ["La contraseña debe tener al menos una letra mayúscula."]);
        }
        if (!preg_match('/[a-z]/', $password)) {
            return array_merge($errors, ["La contraseña debe tener al menos una letra minúscula."]);
        }
        if (!preg_match('/[0-9]/', $password)) {
            return array_merge($errors, ["La contraseña debe tener al menos un número."]);
        }
        if (!preg_match('/[\W_]/', $password)) {
            return array_merge($errors, ["La contraseña debe tener al menos un carácter especial."]);
        }
        return true;
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