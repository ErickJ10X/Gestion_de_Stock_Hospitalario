<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\Almacen;
use model\entity\Hospital;
use model\entity\Planta;
use PDO;

class AlmacenRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?Almacen {
        $stmt = $this->conexion->prepare("
            SELECT a.*, h.nombre as hospital_nombre, p.nombre as planta_nombre
            FROM almacenes a
            LEFT JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            WHERE a.id_almacen = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        $almacen = Almacen::fromArray($data);
        
        // Establecer relaciones
        if ($data['id_hospital']) {
            $hospital = new Hospital($data['id_hospital'], $data['hospital_nombre'] ?? '');
            $almacen->setHospital($hospital);
        }
        
        if ($data['id_planta']) {
            $planta = new Planta($data['id_planta'], $data['id_hospital'], $data['planta_nombre'] ?? '');
            $almacen->setPlanta($planta);
        }
        
        return $almacen;
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("
            SELECT a.*, h.nombre as hospital_nombre, p.nombre as planta_nombre
            FROM almacenes a
            LEFT JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
        ");
        
        $almacenes = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $almacen = Almacen::fromArray($data);
            
            // Establecer relaciones
            if ($data['id_hospital']) {
                $hospital = new Hospital($data['id_hospital'], $data['hospital_nombre'] ?? '');
                $almacen->setHospital($hospital);
            }
            
            if ($data['id_planta']) {
                $planta = new Planta($data['id_planta'], $data['id_hospital'], $data['planta_nombre'] ?? '');
                $almacen->setPlanta($planta);
            }
            
            $almacenes[] = $almacen;
        }
        
        return $almacenes;
    }

    public function findByHospital(int $idHospital): array {
        $stmt = $this->conexion->prepare("
            SELECT a.*, h.nombre as hospital_nombre, p.nombre as planta_nombre
            FROM almacenes a
            LEFT JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            WHERE a.id_hospital = :id_hospital
        ");
        $stmt->bindParam(':id_hospital', $idHospital);
        $stmt->execute();
        
        $almacenes = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $almacen = Almacen::fromArray($data);
            
            // Establecer relaciones
            if ($data['id_hospital']) {
                $hospital = new Hospital($data['id_hospital'], $data['hospital_nombre'] ?? '');
                $almacen->setHospital($hospital);
            }
            
            if ($data['id_planta']) {
                $planta = new Planta($data['id_planta'], $data['id_hospital'], $data['planta_nombre'] ?? '');
                $almacen->setPlanta($planta);
            }
            
            $almacenes[] = $almacen;
        }
        
        return $almacenes;
    }

    public function save(Almacen $almacen): Almacen {
        if ($almacen->getIdAlmacen() === null) {
            return $this->insert($almacen);
        } else {
            return $this->update($almacen);
        }
    }

    private function insert(Almacen $almacen): Almacen {
        $stmt = $this->conexion->prepare("
            INSERT INTO almacenes (tipo, id_planta, id_hospital, activo)
            VALUES (:tipo, :id_planta, :id_hospital, :activo)
        ");
        
        $stmt->bindValue(':tipo', $almacen->getTipo());
        $stmt->bindValue(':id_planta', $almacen->getIdPlanta());
        $stmt->bindValue(':id_hospital', $almacen->getIdHospital());
        $stmt->bindValue(':activo', $almacen->isActivo(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $almacen->setIdAlmacen($this->conexion->lastInsertId());
        
        return $almacen;
    }

    private function update(Almacen $almacen): Almacen {
        $stmt = $this->conexion->prepare("
            UPDATE almacenes
            SET tipo = :tipo,
                id_planta = :id_planta,
                id_hospital = :id_hospital,
                activo = :activo
            WHERE id_almacen = :id_almacen
        ");
        
        $stmt->bindValue(':tipo', $almacen->getTipo());
        $stmt->bindValue(':id_planta', $almacen->getIdPlanta());
        $stmt->bindValue(':id_hospital', $almacen->getIdHospital());
        $stmt->bindValue(':activo', $almacen->isActivo(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_almacen', $almacen->getIdAlmacen());
        
        $stmt->execute();
        
        return $almacen;
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM almacenes WHERE id_almacen = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function softDelete(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE almacenes SET activo = 0 WHERE id_almacen = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
