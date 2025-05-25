<?php

namespace model\repository;

use model\entity\Roles;
use PDO;

class RolesRepository
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function mapToRol(array $row): Roles
    {
        return new Roles(
            $row['id_rol'],
            $row['nombre']
        );
    }

    public function mapToRolesArray(array $rows): array
    {
        $roles = [];
        foreach ($rows as $row) {
            $roles[] = $this->mapToRol($row);
        }
        return $roles;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM roles";
        return $this->mapToRolesArray($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findById(int $id): ?Roles
    {
        $sql = "SELECT * FROM roles WHERE id_rol = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->mapToRol($result);
    }

    public function save(Roles $rol): void
    {
        $sql = "INSERT INTO roles (nombre) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$rol->getNombre()]);
    }

    public function update(Roles $rol): void
    {
        $sql = "UPDATE roles SET nombre = ? WHERE id_rol = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$rol->getNombre(), $rol->getIdRol()]);
    }

    public function delete(int $id): void
    {
        $sql = "DELETE FROM roles WHERE id_rol = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
    }
}
