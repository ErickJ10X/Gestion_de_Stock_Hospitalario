<?php

namespace repository;
require_once(__DIR__ . '/../../config/database.php');
class UsuarioRepository
{
    private ?\PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT id, usuario, rol FROM usuarios";
        return $this->pdo->prepare($sql)->execute()->fetchAll();
    }

    public function findById($id): array
    {
        $sql = "SELECT id, usuario, rol FROM usuarios WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$id])->fetch();
    }

    public function verifyEmailExist($email): array
    {
        $sql = "SELECT email FROM usuarios WHERE email = ?";
        return $this->pdo->prepare($sql)->execute($email)->fetchAll();
    }

    public function save($nombre, $email, $contrasena, $rol): bool
    {
        $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol) VALUES (?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([$nombre, $email, password_hash($contrasena, PASSWORD_BCRYPT), $rol]);
    }

}