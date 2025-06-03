<?php

namespace repositories;

use Models\Producto;
use Repositories\Interfaces\ProductoRepositoryInterface;
use PDO;

class ProductoRepository implements ProductoRepositoryInterface {
    private $db;

    public function __construct() {
        // Usar database.php
        require_once __DIR__ . '/../config/database.php';
        $this->db = getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query('SELECT * FROM productos ORDER BY nombre');
        $productos = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = Producto::fromArray($row);
        }
        
        return $productos;
    }

    public function findById(int $id): ?Producto {
        $stmt = $this->db->prepare('SELECT * FROM productos WHERE id_producto = :id');
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? Producto::fromArray($row) : null;
    }

    public function findByCodigo(string $codigo): ?Producto {
        $stmt = $this->db->prepare('SELECT * FROM productos WHERE codigo = :codigo');
        $stmt->execute(['codigo' => $codigo]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? Producto::fromArray($row) : null;
    }

    public function findActive(): array {
        $stmt = $this->db->query('SELECT * FROM productos WHERE activo = 1 ORDER BY nombre');
        $productos = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = Producto::fromArray($row);
        }
        
        return $productos;
    }

    public function save(Producto $producto): Producto {
        $stmt = $this->db->prepare(
            'INSERT INTO productos (codigo, nombre, descripcion, unidad_medida, activo) 
             VALUES (:codigo, :nombre, :descripcion, :unidad_medida, :activo)'
        );
        
        $stmt->execute([
            'codigo' => $producto->getCodigo(),
            'nombre' => $producto->getNombre(),
            'descripcion' => $producto->getDescripcion(),
            'unidad_medida' => $producto->getUnidadMedida(),
            'activo' => $producto->isActivo() ? 1 : 0
        ]);
        
        $id = $this->db->lastInsertId();
        $producto->setIdProducto((int)$id);
        
        return $producto;
    }

    public function update(Producto $producto): bool {
        $stmt = $this->db->prepare(
            'UPDATE productos 
             SET codigo = :codigo, 
                 nombre = :nombre, 
                 descripcion = :descripcion,
                 unidad_medida = :unidad_medida,
                 activo = :activo 
             WHERE id_producto = :id'
        );
        
        return $stmt->execute([
            'id' => $producto->getIdProducto(),
            'codigo' => $producto->getCodigo(),
            'nombre' => $producto->getNombre(),
            'descripcion' => $producto->getDescripcion(),
            'unidad_medida' => $producto->getUnidadMedida(),
            'activo' => $producto->isActivo() ? 1 : 0
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM productos WHERE id_producto = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function activate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE productos SET activo = 1 WHERE id_producto = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE productos SET activo = 0 WHERE id_producto = :id');
        return $stmt->execute(['id' => $id]);
    }
}
