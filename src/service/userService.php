<?php
require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../repository/usuarioRepository.php');

class UserService{

    private \repository\UsuarioRepository $usuarioRepository;

    public function __construct(){
        $this->usuarioRepository = new \repository\UsuarioRepository();
    }

    public function getAllUsers(): array
    {
        try {
            return $this->usuarioRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al cargar los usuarios: " . $e->getMessage());
        }
    }

    public function deleteUser($username): bool{
        try {
            $sql = "DELETE FROM usuarios WHERE usuario = ?";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([$username]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el usuario: " . $e->getMessage());
        }
    }

    public function updateUser($name, $surname, $username, $email, $password, $userId): bool{
        try {
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, usuario = ?, email = ?, contrasena = ? WHERE id = ?";
                $stmt = $this->conexion->prepare($sql);
                return $stmt->execute([$name, $surname, $username, $email, $hashedPassword, $userId]);
            } else {
                $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, usuario = ?, email = ? WHERE id = ?";
                $stmt = $this->conexion->prepare($sql);
                return $stmt->execute([$name, $surname, $username, $email, $userId]);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }

    public function verifyUserAndEmailExist($username, $email, $excludeUserId = null): array {
        try {
            $sql = "SELECT usuario, email FROM usuarios WHERE (usuario = ? OR email = ?)";
            $params = [$username, $email];

            if ($excludeUserId !== null) {
                $sql .= " AND id != ?";
                $params[] = $excludeUserId;
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);

            $result = [
                'usernameExists' => false,
                'emailExists' => false
            ];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['usuario'] === $username) {
                    $result['usernameExists'] = true;
                }
                if ($row['email'] === $email) {
                    $result['emailExists'] = true;
                }
            }

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar usuario y email: " . $e->getMessage());
        }
    }

    public function verifyUserExist($usuario): bool{
        try {
            $sql = "SELECT COUNT(id) AS count FROM usuarios WHERE usuario = ? LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$usuario]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar el usuario: " . $e->getMessage());
        }
    }

    public function verifyEmailExist($email): false|PDOStatement{
        try {
            $sql = "SELECT email FROM usuarios WHERE email = ? LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$email]);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar el email: " . $e->getMessage());
        }
    }

    public function createUser($name,$email,$password): bool{
        try {
            return $this->usuarioRepository->save($name, $email, password_hash($password, PASSWORD_DEFAULT), 'usuario');
        } catch (PDOException $e) {
            throw new Exception("Error al crear el usuario: " . $e->getMessage());
        }
    }

    public function verifyLogin($usuario, $contrasena): false|array{
        try {
            $sql = "SELECT id, usuario, contrasena, rol FROM usuarios WHERE usuario = ? LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$usuario]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($contrasena, $user['contrasena'])) {
                if (password_needs_rehash($user['contrasena'], PASSWORD_DEFAULT)) {
                    $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
                    $stmt = $this->conexion->prepare($sql);
                    $stmt->execute([$hashedPassword, $user['id']]);
                }
                return [
                    'id' => $user['id'],
                    'usuario' => $user['usuario'],
                    'rol' => $user['rol']
                ];
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar el login: " . $e->getMessage());
        }
    }

    public function getUserByUsername($username): array|false {
        try {
            $sql = "SELECT nombre, apellido, usuario, email, rol FROM usuarios WHERE usuario = ? LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario: " . $e->getMessage());
        }
    }

    public function getUserById($userId): array|false {
        try {
            return $this->usuarioRepository->findById($userId);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario por ID: " . $e->getMessage());
        }
    }
}

