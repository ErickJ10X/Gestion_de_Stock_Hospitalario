<?php

namespace repository;
require_once(__DIR__ . '/../../config/database.php');

class PlantasRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM plantas";
        return $this->pdo->prepare($sql)->execute()->fetchAll();
    }

    public function findById($id): array|bool
    {
        $sql = "SELECT * FROM plantas WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$id])->fetch();
    }

    public function findByHospitalId($hospitalId): array
    {
        $sql = "SELECT * FROM plantas WHERE hospital_id = ?";
        return $this->pdo->prepare($sql)->execute([$hospitalId])->fetchAll();
    }

    public function save($nombre): bool
    {
        $sql = "INSERT INTO plantas (nombre) VALUES (?)";
        return $this->pdo->prepare($sql)->execute([$nombre]);
    }

    public function update($id, $nombre, $hospital_id): bool
    {
        $sql = "UPDATE plantas SET nombre = ?, hospital_id = ? WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$nombre, $hospital_id, $id]);
    }

}