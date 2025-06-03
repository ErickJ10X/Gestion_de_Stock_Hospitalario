<?php

namespace repositories;

use Models\Almacen;
use Models\Hospital;
use Models\Planta;
use Repositories\Interfaces\AlmacenRepositoryInterface;
use PDO;

class AlmacenRepository implements AlmacenRepositoryInterface {
    private ?PDO $db;

    public function __construct() {
        // Usar database.php
        require_once __DIR__ . '/../config/database.php';
        $this->db = getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query('
            SELECT a.*, 
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo,
                   p.nombre as planta_nombre, p.activo as planta_activo
            FROM almacenes a
            JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            ORDER BY a.tipo, h.nombre, p.nombre
        ');
        
        return $this->hydrateResults($stmt);
    }

    public function findById(int $id): ?Almacen {
        $stmt = $this->db->prepare('
            SELECT a.*, 
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo,
                   p.nombre as planta_nombre, p.activo as planta_activo
            FROM almacenes a
            JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            WHERE a.id_almacen = :id
        ');
        
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->hydrateAlmacen($row);
    }

    public function findByHospital(int $idHospital): array {
        $stmt = $this->db->prepare('
            SELECT a.*, 
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo,
                   p.nombre as planta_nombre, p.activo as planta_activo
            FROM almacenes a
            JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            WHERE a.id_hospital = :id_hospital
            ORDER BY a.tipo, p.nombre
        ');
        
        $stmt->execute(['id_hospital' => $idHospital]);
        
        return $this->hydrateResults($stmt);
    }

    public function findByPlanta(int $idPlanta): array {
        $stmt = $this->db->prepare('
            SELECT a.*, 
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo,
                   p.nombre as planta_nombre, p.activo as planta_activo
            FROM almacenes a
            JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            WHERE a.id_planta = :id_planta
            ORDER BY a.tipo
        ');
        
        $stmt->execute(['id_planta' => $idPlanta]);
        
        return $this->hydrateResults($stmt);
    }

    public function findByTipo(string $tipo): array {
        $stmt = $this->db->prepare('
            SELECT a.*, 
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo,
                   p.nombre as planta_nombre, p.activo as planta_activo
            FROM almacenes a
            JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            WHERE a.tipo = :tipo
            ORDER BY h.nombre, p.nombre
        ');
        
        $stmt->execute(['tipo' => $tipo]);
        
        return $this->hydrateResults($stmt);
    }

    public function findActive(): array {
        $stmt = $this->db->query('
            SELECT a.*, 
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo,
                   p.nombre as planta_nombre, p.activo as planta_activo
            FROM almacenes a
            JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            WHERE a.activo = 1
            ORDER BY a.tipo, h.nombre, p.nombre
        ');
        
        return $this->hydrateResults($stmt);
    }

    public function findActiveByHospital(int $idHospital): array {
        $stmt = $this->db->prepare('
            SELECT a.*, 
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo,
                   p.nombre as planta_nombre, p.activo as planta_activo
            FROM almacenes a
            JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            WHERE a.id_hospital = :id_hospital AND a.activo = 1
            ORDER BY a.tipo, p.nombre
        ');
        
        $stmt->execute(['id_hospital' => $idHospital]);
        
        return $this->hydrateResults($stmt);
    }

    public function findGeneralByHospital(int $idHospital): ?Almacen {
        $stmt = $this->db->prepare('
            SELECT a.*, 
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo,
                   p.nombre as planta_nombre, p.activo as planta_activo
            FROM almacenes a
            JOIN hospitales h ON a.id_hospital = h.id_hospital
            LEFT JOIN plantas p ON a.id_planta = p.id_planta
            WHERE a.id_hospital = :id_hospital AND a.tipo = "General" AND a.activo = 1
            LIMIT 1
        ');
        
        $stmt->execute(['id_hospital' => $idHospital]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->hydrateAlmacen($row);
    }

    public function save(Almacen $almacen): Almacen {
        $stmt = $this->db->prepare(
            'INSERT INTO almacenes (tipo, id_planta, id_hospital, activo) 
             VALUES (:tipo, :id_planta, :id_hospital, :activo)'
        );
        
        $stmt->execute([
            'tipo' => $almacen->getTipo(),
            'id_planta' => $almacen->getIdPlanta(),
            'id_hospital' => $almacen->getIdHospital(),
            'activo' => $almacen->isActivo() ? 1 : 0
        ]);
        
        $id = $this->db->lastInsertId();
        $almacen->setIdAlmacen((int)$id);
        
        return $almacen;
    }

    public function update(Almacen $almacen): bool {
        $stmt = $this->db->prepare(
            'UPDATE almacenes 
             SET tipo = :tipo,
                 id_planta = :id_planta,
                 id_hospital = :id_hospital,
                 activo = :activo 
             WHERE id_almacen = :id'
        );
        
        return $stmt->execute([
            'id' => $almacen->getIdAlmacen(),
            'tipo' => $almacen->getTipo(),
            'id_planta' => $almacen->getIdPlanta(),
            'id_hospital' => $almacen->getIdHospital(),
            'activo' => $almacen->isActivo() ? 1 : 0
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM almacenes WHERE id_almacen = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function activate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE almacenes SET activo = 1 WHERE id_almacen = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE almacenes SET activo = 0 WHERE id_almacen = :id');
        return $stmt->execute(['id' => $id]);
    }
    
    private function hydrateResults(\PDOStatement $stmt): array {
        $almacenes = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $almacenes[] = $this->hydrateAlmacen($row);
        }
        
        return $almacenes;
    }
    
    private function hydrateAlmacen(array $row): Almacen {
        $almacen = Almacen::fromArray($row);
        
        // Crear objeto Hospital
        $hospital = new Hospital(
            $row['id_hospital'],
            $row['hospital_nombre'] ?? '',
            $row['ubicacion'] ?? '',
            (bool)($row['hospital_activo'] ?? true)
        );
        
        // Asignar hospital
        $almacen->setHospital($hospital);
        
        // Si hay planta, crear y asignar objeto Planta
        if (!empty($row['id_planta'])) {
            $planta = new Planta(
                $row['id_planta'],
                $row['id_hospital'],
                $row['planta_nombre'] ?? '',
                (bool)($row['planta_activo'] ?? true)
            );
            $planta->setHospital($hospital);
            
            $almacen->setPlanta($planta);
        }
        
        return $almacen;
    }
}
