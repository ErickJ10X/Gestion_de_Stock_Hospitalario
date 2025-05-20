<?php

namespace repository;
require_once(__DIR__ . '/../../config/database.php');

class HospitalesRepository
{
    private $pdo;
    public function __construct()
    {

        $this->pdo =getConnection(); ;
    }

    public function findAll(): array{
        $sql = "SELECT * FROM hospitales";
        return $this->pdo->prepare($sql)->execute()->fetchAll();
    }

    public function findById($id): array|bool{
        $sql = "SELECT * FROM hospitales WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$id])->fetch();
    }

    public function save($nombre): bool{
        $sql = "INSERT INTO hospitales (nombre) VALUES (?)";
        return $this->pdo->prepare($sql)->execute([$nombre]);
    }

    public function update($id, $nombre): bool{
        $sql = "UPDATE hospitales SET nombre = ? WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$nombre, $id]);
    }

}