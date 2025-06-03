<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\Planta;
use model\entity\Hospital;
use PDO;

class PlantaRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?Planta {
        $stmt = $this->conexion->prepare("
            SELECT p.*, h.nombre as hospital_nombre, h.ubicacion as hospital_ubicacion
            FROM plantas p
            LEFT JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE p.id_planta = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        $planta = Planta::fromArray($data);
        
        // Establecer relación con hospital si hay datos disponibles
        if (isset($data['hospital_nombre'])) {
            $hospital = new Hospital(
                $data['id_hospital'],
                $data['hospital_nombre'],
                $data['hospital_ubicacion'] ?? '',
                true
            );
            $planta->setHospital($hospital);
        }
        
        return $planta;
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("
            SELECT p.*, h.nombre as hospital_nombre, h.ubicacion as hospital_ubicacion
            FROM plantas p
            LEFT JOIN hospitales h ON p.id_hospital = h.id_hospital
        ");
        
        $plantas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $planta = Planta::fromArray($data);
            
            // Establecer relación con hospital si hay datos disponibles
            if (isset($data['hospital_nombre'])) {
                $hospital = new Hospital(
                    $data['id_hospital'],
                    $data['hospital_nombre'],
                    $data['hospital_ubicacion'] ?? '',
                    true
                );
                $planta->setHospital($hospital);
            }
            
            $plantas[] = $planta;
        }
        
        return $plantas;
    }
    
    public function findByHospital(int $idHospital): array {
        $stmt = $this->conexion->prepare("
            SELECT p.*, h.nombre as hospital_nombre, h.ubicacion as hospital_ubicacion
            FROM plantas p
            LEFT JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE p.id_hospital = :id_hospital
        ");
        $stmt->bindParam(':id_hospital', $idHospital);
        $stmt->execute();
        
        $plantas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $planta = Planta::fromArray($data);
            
            // Establecer relación con hospital si hay datos disponibles
            if (isset($data['hospital_nombre'])) {
                $hospital = new Hospital(
                    $data['id_hospital'],
                    $data['hospital_nombre'],
                    $data['hospital_ubicacion'] ?? '',
                    true
                );
                $planta->setHospital($hospital);
            }
            
            $plantas[] = $planta;
        }
        
        return $plantas;
    }

    public function findActive(): array {
        $stmt = $this->conexion->query("
            SELECT p.*, h.nombre as hospital_nombre, h.ubicacion as hospital_ubicacion
            FROM plantas p
            LEFT JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE p.activo = 1
        ");
        
        $plantas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $planta = Planta::fromArray($data);
            
            // Establecer relación con hospital si hay datos disponibles
            if (isset($data['hospital_nombre'])) {
                $hospital = new Hospital(
                    $data['id_hospital'],
                    $data['hospital_nombre'],
                    $data['hospital_ubicacion'] ?? '',
                    true
                );
                $planta->setHospital($hospital);
            }
            
            $plantas[] = $planta;
        }
        
        return $plantas;
    }

    public function save(Planta $planta): Planta {
        if ($planta->getIdPlanta() === null) {
            return $this->insert($planta);
        } else {
            return $this->update($planta);
        }
    }

    private function insert(Planta $planta): Planta {
        $stmt = $this->conexion->prepare("
            INSERT INTO plantas (id_hospital, nombre, activo)
            VALUES (:id_hospital, :nombre, :activo)
        ");
        
        $stmt->bindValue(':id_hospital', $planta->getIdHospital());
        $stmt->bindValue(':nombre', $planta->getNombre());
        $stmt->bindValue(':activo', $planta->isActivo(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $planta->setIdPlanta($this->conexion->lastInsertId());
        
        return $planta;
    }

    private function update(Planta $planta): Planta {
        $stmt = $this->conexion->prepare("
            UPDATE plantas
            SET id_hospital = :id_hospital,
                nombre = :nombre,
                activo = :activo
            WHERE id_planta = :id_planta
        ");
        
        $stmt->bindValue(':id_hospital', $planta->getIdHospital());
        $stmt->bindValue(':nombre', $planta->getNombre());
        $stmt->bindValue(':activo', $planta->isActivo(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_planta', $planta->getIdPlanta());
        
        $stmt->execute();
        
        return $planta;
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM plantas WHERE id_planta = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function softDelete(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE plantas SET activo = 0 WHERE id_planta = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
