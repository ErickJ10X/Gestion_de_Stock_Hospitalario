<?php

namespace model\repository;

require_once __DIR__ . '/../entity/Plantas.php';
require_once __DIR__ . '/../../../config/database.php';

use PDO;
use model\entity\Plantas;

class PlantasRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function mapToPlantas($row): Plantas
    {
        $planta = new Plantas();
        $planta->setIdPlanta($row['id_planta']);
        $planta->setNombre($row['nombre']);
        $planta->setIdHospital($row['id_hospital']);
        return $planta;
    }
    public function mapToPlantasArray($rows): array
    {
        $plantas = [];
        foreach ($rows as $row) {
            $plantas[] = $this->mapToPlantas($row);
        }
        return $plantas;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM plantas";
        return $this->mapToPlantasArray($this->pdo->query($sql)->fetchAll());
    }

    public function findById($id): ?Plantas
    {
        $sql = "SELECT * FROM plantas WHERE id_planta = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToPlantas($row) : null;
    }

    public function findByHospitalId($id_hospital): array
    {
        $sql = "SELECT * FROM plantas WHERE id_hospital = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_hospital]);
        return $this->mapToPlantasArray($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function save(Plantas $planta): bool
    {
        $sql = "INSERT INTO plantas (nombre, id_hospital) VALUES (?, ?)";
        return $this->pdo->prepare($sql)->execute([$planta->getNombre(), $planta->getIdHospital()]);
    }

    public function update(Plantas $planta): bool
    {
        $sql = "UPDATE plantas SET nombre = ?, id_hospital = ? WHERE id_planta = ?";
        return $this->pdo->prepare($sql)->execute([$planta->getNombre(), $planta->getIdHospital(), $planta->getIdPlanta()]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM plantas WHERE id_planta = ?";
        return $this->pdo->prepare($sql)->execute([$id]);
    }
}
