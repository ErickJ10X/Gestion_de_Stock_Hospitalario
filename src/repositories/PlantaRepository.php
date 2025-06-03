<?php

namespace repositories;

use Models\Planta;
use Models\Hospital;
use Repositories\Interfaces\PlantaRepositoryInterface;
use PDO;

class PlantaRepository implements PlantaRepositoryInterface {
    private $db;

    public function __construct() {
        // Usar la función getConnection() del archivo database.php
        require_once __DIR__ . '/../../config/database.php';
        $this->db = getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query('
            SELECT p.*, h.nombre as hospital_nombre, h.ubicacion as hospital_ubicacion, h.activo as hospital_activo
            FROM plantas p
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            ORDER BY p.nombre
        ');
        
        return $this->hydrateResults($stmt);
    }

    public function findById(int $id): ?Planta {
        $stmt = $this->db->prepare('
            SELECT p.*, h.nombre as hospital_nombre, h.ubicacion as hospital_ubicacion, h.activo as hospital_activo
            FROM plantas p
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE p.id_planta = :id
        ');
        
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->hydratePlanta($row);
    }

    public function findByHospital(int $idHospital): array {
        $stmt = $this->db->prepare('
            SELECT p.*, h.nombre as hospital_nombre, h.ubicacion as hospital_ubicacion, h.activo as hospital_activo
            FROM plantas p
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE p.id_hospital = :id_hospital
            ORDER BY p.nombre
        ');
        
        $stmt->execute(['id_hospital' => $idHospital]);
        
        return $this->hydrateResults($stmt);
    }

    public function findActive(): array {
        $stmt = $this->db->query('
            SELECT p.*, h.nombre as hospital_nombre, h.ubicacion as hospital_ubicacion, h.activo as hospital_activo
            FROM plantas p
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE p.activo = 1
            ORDER BY p.nombre
        ');
        
        return $this->hydrateResults($stmt);
    }

    public function findActiveByHospital(int $idHospital): array {
        $stmt = $this->db->prepare('
            SELECT p.*, h.nombre as hospital_nombre, h.ubicacion as hospital_ubicacion, h.activo as hospital_activo
            FROM plantas p
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE p.id_hospital = :id_hospital AND p.activo = 1
            ORDER BY p.nombre
        ');
        
        $stmt->execute(['id_hospital' => $idHospital]);
        
        return $this->hydrateResults($stmt);
    }

    public function save(Planta $planta): Planta {
        $stmt = $this->db->prepare(
            'INSERT INTO plantas (id_hospital, nombre, activo) 
             VALUES (:id_hospital, :nombre, :activo)'
        );
        
        $stmt->execute([
            'id_hospital' => $planta->getIdHospital(),
            'nombre' => $planta->getNombre(),
            'activo' => $planta->isActivo() ? 1 : 0
        ]);
        
        $id = $this->db->lastInsertId();
        $planta->setIdPlanta((int)$id);
        
        return $planta;
    }

    public function update(Planta $planta): bool {
        $stmt = $this->db->prepare(
            'UPDATE plantas 
             SET id_hospital = :id_hospital,
                 nombre = :nombre, 
                 activo = :activo 
             WHERE id_planta = :id'
        );
        
        return $stmt->execute([
            'id' => $planta->getIdPlanta(),
            'id_hospital' => $planta->getIdHospital(),
            'nombre' => $planta->getNombre(),
            'activo' => $planta->isActivo() ? 1 : 0
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM plantas WHERE id_planta = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function activate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE plantas SET activo = 1 WHERE id_planta = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE plantas SET activo = 0 WHERE id_planta = :id');
        return $stmt->execute(['id' => $id]);
    }
    
    private function hydrateResults(\PDOStatement $stmt): array {
        $plantas = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $plantas[] = $this->hydratePlanta($row);
        }
        
        return $plantas;
    }
    
    private function hydratePlanta(array $row): Planta {
        $planta = Planta::fromArray($row);
        
        // Crear y asignar el objeto Hospital
        $hospital = new Hospital(
            $row['id_hospital'],
            $row['hospital_nombre'] ?? '',
            $row['hospital_ubicacion'] ?? '',
            (bool)($row['hospital_activo'] ?? true)
        );
        
        $planta->setHospital($hospital);
        
        return $planta;
    }
}
