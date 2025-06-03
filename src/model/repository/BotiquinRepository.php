<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\Botiquin;
use model\entity\Planta;
use PDO;

class BotiquinRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?Botiquin {
        $stmt = $this->conexion->prepare("
            SELECT b.*, p.nombre as planta_nombre, p.id_hospital
            FROM botiquines b
            LEFT JOIN plantas p ON b.id_planta = p.id_planta
            WHERE b.id_botiquin = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        $botiquin = Botiquin::fromArray($data);
        
        // Establecer relaciones
        if ($data['id_planta']) {
            $planta = new Planta($data['id_planta'], $data['id_hospital'] ?? null, $data['planta_nombre'] ?? '');
            $botiquin->setPlanta($planta);
        }
        
        return $botiquin;
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("
            SELECT b.*, p.nombre as planta_nombre, p.id_hospital
            FROM botiquines b
            LEFT JOIN plantas p ON b.id_planta = p.id_planta
        ");
        
        $botiquines = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $botiquin = Botiquin::fromArray($data);
            
            // Establecer relaciones
            if ($data['id_planta']) {
                $planta = new Planta($data['id_planta'], $data['id_hospital'] ?? null, $data['planta_nombre'] ?? '');
                $botiquin->setPlanta($planta);
            }
            
            $botiquines[] = $botiquin;
        }
        
        return $botiquines;
    }

    public function findByPlanta(int $idPlanta): array {
        $stmt = $this->conexion->prepare("
            SELECT b.*, p.nombre as planta_nombre, p.id_hospital
            FROM botiquines b
            LEFT JOIN plantas p ON b.id_planta = p.id_planta
            WHERE b.id_planta = :id_planta
        ");
        $stmt->bindParam(':id_planta', $idPlanta);
        $stmt->execute();
        
        $botiquines = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $botiquin = Botiquin::fromArray($data);
            
            // Establecer relaciones
            if ($data['id_planta']) {
                $planta = new Planta($data['id_planta'], $data['id_hospital'] ?? null, $data['planta_nombre'] ?? '');
                $botiquin->setPlanta($planta);
            }
            
            $botiquines[] = $botiquin;
        }
        
        return $botiquines;
    }

    public function findByHospital(int $idHospital): array {
        $stmt = $this->conexion->prepare("
            SELECT b.*, p.nombre as planta_nombre, p.id_hospital
            FROM botiquines b
            JOIN plantas p ON b.id_planta = p.id_planta
            WHERE p.id_hospital = :id_hospital
        ");
        $stmt->bindParam(':id_hospital', $idHospital);
        $stmt->execute();
        
        $botiquines = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $botiquin = Botiquin::fromArray($data);
            
            // Establecer relaciones
            if ($data['id_planta']) {
                $planta = new Planta($data['id_planta'], $data['id_hospital'] ?? null, $data['planta_nombre'] ?? '');
                $botiquin->setPlanta($planta);
            }
            
            $botiquines[] = $botiquin;
        }
        
        return $botiquines;
    }

    public function save(Botiquin $botiquin): Botiquin {
        if ($botiquin->getIdBotiquin() === null) {
            return $this->insert($botiquin);
        } else {
            return $this->update($botiquin);
        }
    }

    private function insert(Botiquin $botiquin): Botiquin {
        $stmt = $this->conexion->prepare("
            INSERT INTO botiquines (id_planta, nombre, activo)
            VALUES (:id_planta, :nombre, :activo)
        ");
        
        $stmt->bindValue(':id_planta', $botiquin->getIdPlanta());
        $stmt->bindValue(':nombre', $botiquin->getNombre());
        $stmt->bindValue(':activo', $botiquin->isActivo(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $botiquin->setIdBotiquin($this->conexion->lastInsertId());
        
        return $botiquin;
    }

    private function update(Botiquin $botiquin): Botiquin {
        $stmt = $this->conexion->prepare("
            UPDATE botiquines
            SET id_planta = :id_planta,
                nombre = :nombre,
                activo = :activo
            WHERE id_botiquin = :id_botiquin
        ");
        
        $stmt->bindValue(':id_planta', $botiquin->getIdPlanta());
        $stmt->bindValue(':nombre', $botiquin->getNombre());
        $stmt->bindValue(':activo', $botiquin->isActivo(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_botiquin', $botiquin->getIdBotiquin());
        
        $stmt->execute();
        
        return $botiquin;
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM botiquines WHERE id_botiquin = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function softDelete(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE botiquines SET activo = 0 WHERE id_botiquin = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
