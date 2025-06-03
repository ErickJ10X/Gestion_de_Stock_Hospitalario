<?php

namespace repositories;

use Models\Hospital;
use Repositories\Interfaces\HospitalRepositoryInterface;
use PDO;

class HospitalRepository implements HospitalRepositoryInterface {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $this->db = getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query('SELECT * FROM hospitales ORDER BY nombre');
        $hospitals = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hospitals[] = Hospital::fromArray($row);
        }
        
        return $hospitals;
    }

    public function findById(int $id): ?Hospital {
        $stmt = $this->db->prepare('SELECT * FROM hospitales WHERE id_hospital = :id');
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? Hospital::fromArray($row) : null;
    }

    public function findActive(): array {
        $stmt = $this->db->query('SELECT * FROM hospitales WHERE activo = 1 ORDER BY nombre');
        $hospitals = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hospitals[] = Hospital::fromArray($row);
        }
        
        return $hospitals;
    }

    public function save(Hospital $hospital): Hospital {
        $stmt = $this->db->prepare(
            'INSERT INTO hospitales (nombre, ubicacion, activo) 
             VALUES (:nombre, :ubicacion, :activo)'
        );
        
        $stmt->execute([
            'nombre' => $hospital->getNombre(),
            'ubicacion' => $hospital->getUbicacion(),
            'activo' => $hospital->isActivo() ? 1 : 0
        ]);
        
        $id = $this->db->lastInsertId();
        $hospital->setIdHospital((int)$id);
        
        return $hospital;
    }

    public function update(Hospital $hospital): bool {
        $stmt = $this->db->prepare(
            'UPDATE hospitales 
             SET nombre = :nombre, 
                 ubicacion = :ubicacion, 
                 activo = :activo 
             WHERE id_hospital = :id'
        );
        
        return $stmt->execute([
            'id' => $hospital->getIdHospital(),
            'nombre' => $hospital->getNombre(),
            'ubicacion' => $hospital->getUbicacion(),
            'activo' => $hospital->isActivo() ? 1 : 0
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM hospitales WHERE id_hospital = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function activate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE hospitales SET activo = 1 WHERE id_hospital = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE hospitales SET activo = 0 WHERE id_hospital = :id');
        return $stmt->execute(['id' => $id]);
    }
}
