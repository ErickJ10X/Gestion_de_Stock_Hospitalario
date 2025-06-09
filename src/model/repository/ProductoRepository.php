<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../model/entity/Producto.php';

use model\entity\Producto;
use PDO;

class ProductoRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?Producto {
        $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE id_producto = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? Producto::fromArray($data) : null;
    }

    public function findByCodigo(string $codigo): ?Producto {
        $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? Producto::fromArray($data) : null;
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("SELECT * FROM productos");
        
        $productos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = Producto::fromArray($data);
        }
        
        return $productos;
    }

    public function findActive(): array {
        $stmt = $this->conexion->query("SELECT * FROM productos WHERE activo = 1");
        
        $productos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = Producto::fromArray($data);
        }
        
        return $productos;
    }

    public function save(Producto $producto): Producto {
        if ($producto->getIdProducto() === null) {
            return $this->insert($producto);
        } else {
            return $this->update($producto);
        }
    }

    private function insert(Producto $producto): Producto {
        $stmt = $this->conexion->prepare("
            INSERT INTO productos (codigo, nombre, descripcion, unidad_medida, activo)
            VALUES (:codigo, :nombre, :descripcion, :unidad_medida, :activo)
        ");
        
        $stmt->bindValue(':codigo', $producto->getCodigo());
        $stmt->bindValue(':nombre', $producto->getNombre());
        $stmt->bindValue(':descripcion', $producto->getDescripcion());
        $stmt->bindValue(':unidad_medida', $producto->getUnidadMedida());
        $stmt->bindValue(':activo', $producto->isActivo(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $producto->setIdProducto($this->conexion->lastInsertId());
        
        return $producto;
    }

    private function update(Producto $producto): Producto {
        $stmt = $this->conexion->prepare("
            UPDATE productos
            SET codigo = :codigo, 
                nombre = :nombre,
                descripcion = :descripcion,
                unidad_medida = :unidad_medida,
                activo = :activo
            WHERE id_producto = :id_producto
        ");
        
        $stmt->bindValue(':codigo', $producto->getCodigo());
        $stmt->bindValue(':nombre', $producto->getNombre());
        $stmt->bindValue(':descripcion', $producto->getDescripcion());
        $stmt->bindValue(':unidad_medida', $producto->getUnidadMedida());
        $stmt->bindValue(':activo', $producto->isActivo(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_producto', $producto->getIdProducto());
        
        $stmt->execute();
        
        return $producto;
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id_producto = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function softDelete(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE productos SET activo = 0 WHERE id_producto = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function findByCatalogo(int $idPlanta): array {
        $stmt = $this->conexion->prepare("
            SELECT p.*
            FROM productos p
            JOIN catalogo_productos cp ON p.id_producto = cp.id_producto
            WHERE cp.id_planta = :id_planta AND cp.activo = 1 AND p.activo = 1
        ");
        $stmt->bindParam(':id_planta', $idPlanta);
        $stmt->execute();
        
        $productos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = Producto::fromArray($data);
        }
        
        return $productos;
    }

    public function buscarPorNombreOCodigo(string $termino): array {
        $termino = "%$termino%";
        $stmt = $this->conexion->prepare("
            SELECT * FROM productos 
            WHERE (nombre LIKE :termino OR codigo LIKE :termino) AND activo = 1
        ");
        $stmt->bindParam(':termino', $termino);
        $stmt->execute();
        
        $productos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = Producto::fromArray($data);
        }
        
        return $productos;
    }
}
