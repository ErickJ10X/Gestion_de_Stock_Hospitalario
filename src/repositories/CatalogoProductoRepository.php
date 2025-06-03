<?php

namespace repositories;

use Models\CatalogoProducto;
use Models\Producto;
use Models\Planta;
use Models\Hospital;
use Repositories\Interfaces\CatalogoProductoRepositoryInterface;
use PDO;

class CatalogoProductoRepository implements CatalogoProductoRepositoryInterface {
    private $db;

    public function __construct() {
        // Usar database.php
        require_once __DIR__ . '/../config/database.php';
        $this->db = getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query('
            SELECT c.*, 
                   p.codigo, p.nombre as producto_nombre, p.descripcion, p.unidad_medida, p.activo as producto_activo,
                   pl.nombre as planta_nombre, pl.activo as planta_activo, pl.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM catalogo_productos c
            JOIN productos p ON c.id_producto = p.id_producto
            JOIN plantas pl ON c.id_planta = pl.id_planta
            JOIN hospitales h ON pl.id_hospital = h.id_hospital
        ');
        
        return $this->hydrateResults($stmt);
    }

    public function findById(int $id): ?CatalogoProducto {
        $stmt = $this->db->prepare('
            SELECT c.*, 
                   p.codigo, p.nombre as producto_nombre, p.descripcion, p.unidad_medida, p.activo as producto_activo,
                   pl.nombre as planta_nombre, pl.activo as planta_activo, pl.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM catalogo_productos c
            JOIN productos p ON c.id_producto = p.id_producto
            JOIN plantas pl ON c.id_planta = pl.id_planta
            JOIN hospitales h ON pl.id_hospital = h.id_hospital
            WHERE c.id_catalogo = :id
        ');
        
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->hydrateCatalogoProducto($row);
    }

    public function findByPlanta(int $idPlanta): array {
        $stmt = $this->db->prepare('
            SELECT c.*, 
                   p.codigo, p.nombre as producto_nombre, p.descripcion, p.unidad_medida, p.activo as producto_activo,
                   pl.nombre as planta_nombre, pl.activo as planta_activo, pl.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM catalogo_productos c
            JOIN productos p ON c.id_producto = p.id_producto
            JOIN plantas pl ON c.id_planta = pl.id_planta
            JOIN hospitales h ON pl.id_hospital = h.id_hospital
            WHERE c.id_planta = :id_planta
            ORDER BY p.nombre
        ');
        
        $stmt->execute(['id_planta' => $idPlanta]);
        
        return $this->hydrateResults($stmt);
    }

    public function findByProducto(int $idProducto): array {
        $stmt = $this->db->prepare('
            SELECT c.*, 
                   p.codigo, p.nombre as producto_nombre, p.descripcion, p.unidad_medida, p.activo as producto_activo,
                   pl.nombre as planta_nombre, pl.activo as planta_activo, pl.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM catalogo_productos c
            JOIN productos p ON c.id_producto = p.id_producto
            JOIN plantas pl ON c.id_planta = pl.id_planta
            JOIN hospitales h ON pl.id_hospital = h.id_hospital
            WHERE c.id_producto = :id_producto
            ORDER BY pl.nombre
        ');
        
        $stmt->execute(['id_producto' => $idProducto]);
        
        return $this->hydrateResults($stmt);
    }

    public function findByPlantaAndProducto(int $idPlanta, int $idProducto): ?CatalogoProducto {
        $stmt = $this->db->prepare('
            SELECT c.*, 
                   p.codigo, p.nombre as producto_nombre, p.descripcion, p.unidad_medida, p.activo as producto_activo,
                   pl.nombre as planta_nombre, pl.activo as planta_activo, pl.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM catalogo_productos c
            JOIN productos p ON c.id_producto = p.id_producto
            JOIN plantas pl ON c.id_planta = pl.id_planta
            JOIN hospitales h ON pl.id_hospital = h.id_hospital
            WHERE c.id_planta = :id_planta AND c.id_producto = :id_producto
        ');
        
        $stmt->execute([
            'id_planta' => $idPlanta,
            'id_producto' => $idProducto
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->hydrateCatalogoProducto($row);
    }

    public function findActive(): array {
        $stmt = $this->db->query('
            SELECT c.*, 
                   p.codigo, p.nombre as producto_nombre, p.descripcion, p.unidad_medida, p.activo as producto_activo,
                   pl.nombre as planta_nombre, pl.activo as planta_activo, pl.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM catalogo_productos c
            JOIN productos p ON c.id_producto = p.id_producto
            JOIN plantas pl ON c.id_planta = pl.id_planta
            JOIN hospitales h ON pl.id_hospital = h.id_hospital
            WHERE c.activo = 1
            ORDER BY p.nombre
        ');
        
        return $this->hydrateResults($stmt);
    }

    public function findActiveByPlanta(int $idPlanta): array {
        $stmt = $this->db->prepare('
            SELECT c.*, 
                   p.codigo, p.nombre as producto_nombre, p.descripcion, p.unidad_medida, p.activo as producto_activo,
                   pl.nombre as planta_nombre, pl.activo as planta_activo, pl.id_hospital,
                   h.nombre as hospital_nombre, h.ubicacion, h.activo as hospital_activo
            FROM catalogo_productos c
            JOIN productos p ON c.id_producto = p.id_producto
            JOIN plantas pl ON c.id_planta = pl.id_planta
            JOIN hospitales h ON pl.id_hospital = h.id_hospital
            WHERE c.id_planta = :id_planta AND c.activo = 1
            ORDER BY p.nombre
        ');
        
        $stmt->execute(['id_planta' => $idPlanta]);
        
        return $this->hydrateResults($stmt);
    }

    public function save(CatalogoProducto $catalogoProducto): CatalogoProducto {
        $stmt = $this->db->prepare(
            'INSERT INTO catalogo_productos (id_producto, id_planta, activo) 
             VALUES (:id_producto, :id_planta, :activo)'
        );
        
        $stmt->execute([
            'id_producto' => $catalogoProducto->getIdProducto(),
            'id_planta' => $catalogoProducto->getIdPlanta(),
            'activo' => $catalogoProducto->isActivo() ? 1 : 0
        ]);
        
        $id = $this->db->lastInsertId();
        $catalogoProducto->setIdCatalogo((int)$id);
        
        return $catalogoProducto;
    }

    public function update(CatalogoProducto $catalogoProducto): bool {
        $stmt = $this->db->prepare(
            'UPDATE catalogo_productos 
             SET id_producto = :id_producto,
                 id_planta = :id_planta,
                 activo = :activo 
             WHERE id_catalogo = :id'
        );
        
        return $stmt->execute([
            'id' => $catalogoProducto->getIdCatalogo(),
            'id_producto' => $catalogoProducto->getIdProducto(),
            'id_planta' => $catalogoProducto->getIdPlanta(),
            'activo' => $catalogoProducto->isActivo() ? 1 : 0
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM catalogo_productos WHERE id_catalogo = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function activate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE catalogo_productos SET activo = 1 WHERE id_catalogo = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE catalogo_productos SET activo = 0 WHERE id_catalogo = :id');
        return $stmt->execute(['id' => $id]);
    }
    
    private function hydrateResults(\PDOStatement $stmt): array {
        $catalogos = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $catalogos[] = $this->hydrateCatalogoProducto($row);
        }
        
        return $catalogos;
    }
    
    private function hydrateCatalogoProducto(array $row): CatalogoProducto {
        $catalogoProducto = CatalogoProducto::fromArray($row);
        
        // Crear y asignar el objeto Producto
        $producto = new Producto(
            $row['id_producto'],
            $row['codigo'] ?? '',
            $row['producto_nombre'] ?? '',
            $row['descripcion'] ?? '',
            $row['unidad_medida'] ?? '',
            (bool)($row['producto_activo'] ?? true)
        );
        
        // Crear objeto Hospital
        $hospital = new Hospital(
            $row['id_hospital'],
            $row['hospital_nombre'] ?? '',
            $row['ubicacion'] ?? '',
            (bool)($row['hospital_activo'] ?? true)
        );
        
        // Crear y asignar el objeto Planta
        $planta = new Planta(
            $row['id_planta'],
            $row['id_hospital'],
            $row['planta_nombre'] ?? '',
            (bool)($row['planta_activo'] ?? true)
        );
        $planta->setHospital($hospital);
        
        // Asignar objetos relacionados
        $catalogoProducto->setProducto($producto);
        $catalogoProducto->setPlanta($planta);
        
        return $catalogoProducto;
    }
}
