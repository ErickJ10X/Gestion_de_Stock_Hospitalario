<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\Hospital;
use PDO;

class HospitalRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?Hospital {
        $stmt = $this->conexion->prepare("SELECT * FROM hospitales WHERE id_hospital = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        return Hospital::fromArray($data);
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("SELECT * FROM hospitales");
        
        $hospitales = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hospitales[] = Hospital::fromArray($data);
        }
        
        return $hospitales;
    }

    public function findActive(): array {
        $stmt = $this->conexion->query("SELECT * FROM hospitales WHERE activo = 1");
        
        $hospitales = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hospitales[] = Hospital::fromArray($data);
        }
        
        return $hospitales;
    }

    public function save(Hospital $hospital): Hospital {
        if ($hospital->getIdHospital() === null) {
            return $this->insert($hospital);
        } else {
            return $this->update($hospital);
        }
    }

    private function insert(Hospital $hospital): Hospital {
        $stmt = $this->conexion->prepare("
            INSERT INTO hospitales (nombre, ubicacion, activo)
            VALUES (:nombre, :ubicacion, :activo)
        ");
        
        $stmt->bindValue(':nombre', $hospital->getNombre());
        $stmt->bindValue(':ubicacion', $hospital->getUbicacion());
        $stmt->bindValue(':activo', $hospital->isActivo(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $hospital->setIdHospital($this->conexion->lastInsertId());
        
        return $hospital;
    }

    private function update(Hospital $hospital): Hospital {
        $stmt = $this->conexion->prepare("
            UPDATE hospitales
            SET nombre = :nombre,
                ubicacion = :ubicacion,
                activo = :activo
            WHERE id_hospital = :id_hospital
        ");
        
        $stmt->bindValue(':nombre', $hospital->getNombre());
        $stmt->bindValue(':ubicacion', $hospital->getUbicacion());
        $stmt->bindValue(':activo', $hospital->isActivo(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_hospital', $hospital->getIdHospital());
        
        $stmt->execute();
        
        return $hospital;
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM hospitales WHERE id_hospital = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function softDelete(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE hospitales SET activo = 0 WHERE id_hospital = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
