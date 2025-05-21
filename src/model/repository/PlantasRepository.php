<?php

namespace model\repository;
require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../entity/Planta.php');

use model\entity\Planta;

class PlantasRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }
    
    private function mapToPlanta($row): Planta
    {
        return new Planta(
            $row['id'],
            $row['nombre'],
            $row['hospital_id']
        );
    }
    
    private function mapToPlantaArray(array $rows): array
    {
        $plantas = [];
        foreach ($rows as $row) {
            $plantas[] = $this->mapToPlanta($row);
        }
        return $plantas;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM plantas";
        $stmt = $this->pdo->query($sql);
        return $this->mapToPlantaArray($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function findById($id): ?Planta
    {
        $sql = "SELECT * FROM plantas WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->mapToPlanta($row) : null;
    }

    public function findByHospitalId($hospitalId): array
    {
        $sql = "SELECT * FROM plantas WHERE hospital_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$hospitalId]);
        return $this->mapToPlantaArray($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function save(Planta $planta): bool
    {
        $sql = "INSERT INTO plantas (nombre, hospital_id) VALUES (?, ?)";
        return $this->pdo->prepare($sql)->execute([
            $planta->getNombre(),
            $planta->getHospitalId()
        ]);
    }

    public function update(Planta $planta): bool
    {
        $sql = "UPDATE plantas SET nombre = ?, hospital_id = ? WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([
            $planta->getNombre(),
            $planta->getHospitalId(),
            $planta->getId()
        ]);
    }
    
    public function deleteById($id): bool
    {
        $sql = "DELETE FROM plantas WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$id]);
    }
}
