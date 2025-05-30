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
            $row['id_hospital'],
            $row['nombre'],
            $row['ubicacion']
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
        $sql = "SELECT id_hospital, nombre, ubicacion FROM hospitales";
        $stmt = $this->pdo->query($sql);
        return $this->mapToHospitalArray($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findById($id): ?Hospitales
    {
        $sql = "SELECT id_hospital, nombre, ubicacion FROM hospitales WHERE id_hospital = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? $this->mapToHospital($row) : null;
    }

    public function save(Hospitales $hospital): bool
    {
        $sql = "INSERT INTO hospitales (nombre, ubicacion) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$hospital->getNombre(), $hospital->getUbicacion()]);
        
        if ($result) {
            $hospital->setIdHospital((int)$this->pdo->lastInsertId());
            return true;
        }
        
        return false;
    }

    public function update(Hospitales $hospital): bool
    {
        $sql = "UPDATE hospitales SET nombre = ?, ubicacion = ? WHERE id_hospital = ?";
        return $this->pdo->prepare($sql)->execute([
            $hospital->getNombre(), 
            $hospital->getUbicacion(), 
            $hospital->getIdHospital()
        ]);
    }
    
    public function deleteById($id): bool
    {
        $sql = "DELETE FROM hospitales WHERE id_hospital = ?";
        return $this->pdo->prepare($sql)->execute([$id]);
    }
}
