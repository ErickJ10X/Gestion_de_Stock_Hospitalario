<?php

namespace model\repository;
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../entity/Hospitales.php');

use model\entity\Hospitales;
use PDO;

class HospitalesRepository
{
    private ?PDO $pdo;
    
    public function __construct()
    {
        $this->pdo = getConnection();
    }
    
    private function mapToHospital($row): Hospitales
    {
        return new Hospitales(
            $row['id'],
            $row['nombre']
        );
    }
    
    private function mapToHospitalArray(array $rows): array
    {
        $hospitales = [];
        foreach ($rows as $row) {
            $hospitales[] = $this->mapToHospital($row);
        }
        return $hospitales;
    }

    public function findAll(): array
    {
        $sql = "SELECT id, nombre FROM hospitales";
        $stmt = $this->pdo->query($sql);
        return $this->mapToHospitalArray($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findById($id): ?Hospitales
    {
        $sql = "SELECT id, nombre FROM hospitales WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToHospital($row) : null;
    }

    public function save(Hospitales $hospital): bool
    {
        $sql = "INSERT INTO hospitales (nombre) VALUES (?)";
        return $this->pdo->prepare($sql)->execute([
            $hospital->nombre
        ]);
    }

    public function update(Hospitales $hospital): bool
    {
        $sql = "UPDATE hospitales SET nombre = ? WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([
            $hospital->nombre,
            $hospital->id_hospital
        ]);
    }
    
    public function deleteById($id): bool
    {
        $sql = "DELETE FROM hospitales WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$id]);
    }
}
