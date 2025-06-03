<?php

namespace repositories;

use Models\Botiquin;
use Models\Planta;
use Models\Hospital;
use Repositories\Interfaces\BotiquinRepositoryInterface;
use PDO;

class BotiquinRepository implements BotiquinRepositoryInterface {
    private $db;

    public function __construct() {
        // Usar database.php
        require_once __DIR__ . '/../config/database.php';
        $this->db = getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query('
            SELECT b.*, 
                   p.nombre as planta_nombre, p.activo as planta_activo, p.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM botiquines b
            JOIN plantas p ON b.id_planta = p.id_planta
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            ORDER BY h.nombre, p.nombre, b.nombre
        ');
        
        return $this->hydrateResults($stmt);
    }

    public function findById(int $id): ?Botiquin {
        $stmt = $this->db->prepare('
            SELECT b.*, 
                   p.nombre as planta_nombre, p.activo as planta_activo, p.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM botiquines b
            JOIN plantas p ON b.id_planta = p.id_planta
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE b.id_botiquin = :id
        ');
        
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->hydrateBotiquin($row);
    }

    public function findByPlanta(int $idPlanta): array {
        $stmt = $this->db->prepare('
            SELECT b.*, 
                   p.nombre as planta_nombre, p.activo as planta_activo, p.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM botiquines b
            JOIN plantas p ON b.id_planta = p.id_planta
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE b.id_planta = :id_planta
            ORDER BY b.nombre
        ');
        
        $stmt->execute(['id_planta' => $idPlanta]);
        
        return $this->hydrateResults($stmt);
    }

    public function findByNombre(string $nombre, int $idPlanta): ?Botiquin {
        $stmt = $this->db->prepare('
            SELECT b.*, 
                   p.nombre as planta_nombre, p.activo as planta_activo, p.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM botiquines b
            JOIN plantas p ON b.id_planta = p.id_planta
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE b.nombre = :nombre AND b.id_planta = :id_planta
        ');
        
        $stmt->execute([
            'nombre' => $nombre,
            'id_planta' => $idPlanta
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->hydrateBotiquin($row);
    }

    public function findActive(): array {
        $stmt = $this->db->query('
            SELECT b.*, 
                   p.nombre as planta_nombre, p.activo as planta_activo, p.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM botiquines b
            JOIN plantas p ON b.id_planta = p.id_planta
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE b.activo = 1
            ORDER BY h.nombre, p.nombre, b.nombre
        ');
        
        return $this->hydrateResults($stmt);
    }

    public function findActiveByPlanta(int $idPlanta): array {
        $stmt = $this->db->prepare('
            SELECT b.*, 
                   p.nombre as planta_nombre, p.activo as planta_activo, p.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM botiquines b
            JOIN plantas p ON b.id_planta = p.id_planta
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE b.id_planta = :id_planta AND b.activo = 1
            ORDER BY b.nombre
        ');
        
        $stmt->execute(['id_planta' => $idPlanta]);
        
        return $this->hydrateResults($stmt);
    }

    public function save(Botiquin $botiquin): Botiquin {
        $stmt = $this->db->prepare(
            'INSERT INTO botiquines (id_planta, nombre, activo) 
             VALUES (:id_planta, :nombre, :activo)'
        );
        
        $stmt->execute([
            'id_planta' => $botiquin->getIdPlanta(),
            'nombre' => $botiquin->getNombre(),
            'activo' => $botiquin->isActivo() ? 1 : 0
        ]);
        
        $id = $this->db->lastInsertId();
        $botiquin->setIdBotiquin((int)$id);
        
        return $botiquin;
    }

    public function update(Botiquin $botiquin): bool {
        $stmt = $this->db->prepare(
            'UPDATE botiquines 
             SET id_planta = :id_planta,
                 nombre = :nombre,
                 activo = :activo 
             WHERE id_botiquin = :id'
        );
        
        return $stmt->execute([
            'id' => $botiquin->getIdBotiquin(),
            'id_planta' => $botiquin->getIdPlanta(),
            'nombre' => $botiquin->getNombre(),
            'activo' => $botiquin->isActivo() ? 1 : 0
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM botiquines WHERE id_botiquin = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function activate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE botiquines SET activo = 1 WHERE id_botiquin = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE botiquines SET activo = 0 WHERE id_botiquin = :id');
        return $stmt->execute(['id' => $id]);
    }
    
    private function hydrateResults(\PDOStatement $stmt): array {
        $botiquines = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $botiquines[] = $this->hydrateBotiquin($row);
        }
        
        return $botiquines;
    }
    
    private function hydrateBotiquin(array $row): Botiquin {
        $botiquin = Botiquin::fromArray($row);
        
        // Crear objeto Hospital
        $hospital = new Hospital(
            $row['id_hospital'],
            $row['hospital_nombre'] ?? '',
            $row['ubicacion'] ?? '',
            (bool)($row['hospital_activo'] ?? true)
        );
        
        // Crear y asignar objeto Planta
        $planta = new Planta(
            $row['id_planta'],
            $row['id_hospital'],
            $row['planta_nombre'] ?? '',
            (bool)($row['planta_activo'] ?? true)
        );
        $planta->setHospital($hospital);
        
        // Asignar planta al botiquín
        $botiquin->setPlanta($planta);
        
        return $botiquin;
    }
}
