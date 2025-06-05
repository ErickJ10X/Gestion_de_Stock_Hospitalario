<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\CatalogoProducto;
use model\entity\Producto;
use model\entity\Planta;
use PDO;

class CatalogoProductoRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?CatalogoProducto {
        $stmt = $this->conexion->prepare("
            SELECT cp.*, 
                   p.codigo as producto_codigo, p.nombre as producto_nombre, p.descripcion as producto_descripcion, p.unidad_medida,
                   pl.nombre as planta_nombre, pl.id_hospital
            FROM catalogo_productos cp
            LEFT JOIN productos p ON cp.id_producto = p.id_producto
            LEFT JOIN plantas pl ON cp.id_planta = pl.id_planta
            WHERE cp.id_catalogo = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        $catalogo = CatalogoProducto::fromArray($data);
        
        // Establecer relaciones
        if ($data['id_producto']) {
            $producto = new Producto();
            $producto->setIdProducto($data['id_producto']);
            $producto->setCodigo($data['producto_codigo'] ?? '');
            $producto->setNombre($data['producto_nombre'] ?? '');
            $producto->setDescripcion($data['producto_descripcion'] ?? '');
            $producto->setUnidadMedida($data['unidad_medida'] ?? '');
            
            $catalogo->setProducto($producto);
        }
        
        if ($data['id_planta']) {
            $planta = new Planta();
            $planta->setIdPlanta($data['id_planta']);
            $planta->setIdHospital($data['id_hospital'] ?? null);
            $planta->setNombre($data['planta_nombre'] ?? '');
            
            $catalogo->setPlanta($planta);
        }
        
        return $catalogo;
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("
            SELECT cp.*, 
                   p.codigo as producto_codigo, p.nombre as producto_nombre, p.descripcion as producto_descripcion, p.unidad_medida,
                   pl.nombre as planta_nombre, pl.id_hospital
            FROM catalogo_productos cp
            LEFT JOIN productos p ON cp.id_producto = p.id_producto
            LEFT JOIN plantas pl ON cp.id_planta = pl.id_planta
        ");
        
        $catalogos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $catalogo = CatalogoProducto::fromArray($data);
            
            // Establecer relaciones
            if ($data['id_producto']) {
                $producto = new Producto();
                $producto->setIdProducto($data['id_producto']);
                $producto->setCodigo($data['producto_codigo'] ?? '');
                $producto->setNombre($data['producto_nombre'] ?? '');
                $producto->setDescripcion($data['producto_descripcion'] ?? '');
                $producto->setUnidadMedida($data['unidad_medida'] ?? '');
                
                $catalogo->setProducto($producto);
            }
            
            if ($data['id_planta']) {
                $planta = new Planta();
                $planta->setIdPlanta($data['id_planta']);
                $planta->setIdHospital($data['id_hospital'] ?? null);
                $planta->setNombre($data['planta_nombre'] ?? '');
                
                $catalogo->setPlanta($planta);
            }
            
            $catalogos[] = $catalogo;
        }
        
        return $catalogos;
    }

    public function findByPlanta(int $idPlanta): array {
        $stmt = $this->conexion->prepare("
            SELECT cp.*, 
                   p.codigo as producto_codigo, p.nombre as producto_nombre, p.descripcion as producto_descripcion, p.unidad_medida,
                   pl.nombre as planta_nombre, pl.id_hospital
            FROM catalogo_productos cp
            LEFT JOIN productos p ON cp.id_producto = p.id_producto
            LEFT JOIN plantas pl ON cp.id_planta = pl.id_planta
            WHERE cp.id_planta = :id_planta
        ");
        $stmt->bindParam(':id_planta', $idPlanta);
        $stmt->execute();
        
        $catalogos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $catalogo = CatalogoProducto::fromArray($data);
            
            // Establecer relaciones
            if ($data['id_producto']) {
                $producto = new Producto();
                $producto->setIdProducto($data['id_producto']);
                $producto->setCodigo($data['producto_codigo'] ?? '');
                $producto->setNombre($data['producto_nombre'] ?? '');
                $producto->setDescripcion($data['producto_descripcion'] ?? '');
                $producto->setUnidadMedida($data['unidad_medida'] ?? '');
                
                $catalogo->setProducto($producto);
            }
            
            if ($data['id_planta']) {
                $planta = new Planta();
                $planta->setIdPlanta($data['id_planta']);
                $planta->setIdHospital($data['id_hospital'] ?? null);
                $planta->setNombre($data['planta_nombre'] ?? '');
                
                $catalogo->setPlanta($planta);
            }
            
            $catalogos[] = $catalogo;
        }
        
        return $catalogos;
    }

    public function findByProducto(int $idProducto): array {
        $stmt = $this->conexion->prepare("
            SELECT cp.*, 
                   p.codigo as producto_codigo, p.nombre as producto_nombre, p.descripcion as producto_descripcion, p.unidad_medida,
                   pl.nombre as planta_nombre, pl.id_hospital
            FROM catalogo_productos cp
            LEFT JOIN productos p ON cp.id_producto = p.id_producto
            LEFT JOIN plantas pl ON cp.id_planta = pl.id_planta
            WHERE cp.id_producto = :id_producto
        ");
        $stmt->bindParam(':id_producto', $idProducto);
        $stmt->execute();
        
        $catalogos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $catalogo = CatalogoProducto::fromArray($data);
            
            // Establecer relaciones
            if ($data['id_producto']) {
                $producto = new Producto();
                $producto->setIdProducto($data['id_producto']);
                $producto->setCodigo($data['producto_codigo'] ?? '');
                $producto->setNombre($data['producto_nombre'] ?? '');
                $producto->setDescripcion($data['producto_descripcion'] ?? '');
                $producto->setUnidadMedida($data['unidad_medida'] ?? '');
                
                $catalogo->setProducto($producto);
            }
            
            if ($data['id_planta']) {
                $planta = new Planta();
                $planta->setIdPlanta($data['id_planta']);
                $planta->setIdHospital($data['id_hospital'] ?? null);
                $planta->setNombre($data['planta_nombre'] ?? '');
                
                $catalogo->setPlanta($planta);
            }
            
            $catalogos[] = $catalogo;
        }
        
        return $catalogos;
    }

    public function save(CatalogoProducto $catalogo): CatalogoProducto {
        if ($catalogo->getIdCatalogo() === null) {
            return $this->insert($catalogo);
        } else {
            return $this->update($catalogo);
        }
    }

    private function insert(CatalogoProducto $catalogo): CatalogoProducto {
        $stmt = $this->conexion->prepare("
            INSERT INTO catalogo_productos (id_producto, id_planta, activo)
            VALUES (:id_producto, :id_planta, :activo)
        ");
        
        $stmt->bindValue(':id_producto', $catalogo->getIdProducto());
        $stmt->bindValue(':id_planta', $catalogo->getIdPlanta());
        $stmt->bindValue(':activo', $catalogo->isActivo(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $catalogo->setIdCatalogo($this->conexion->lastInsertId());
        
        return $catalogo;
    }

    private function update(CatalogoProducto $catalogo): CatalogoProducto {
        $stmt = $this->conexion->prepare("
            UPDATE catalogo_productos
            SET id_producto = :id_producto,
                id_planta = :id_planta,
                activo = :activo
            WHERE id_catalogo = :id_catalogo
        ");
        
        $stmt->bindValue(':id_producto', $catalogo->getIdProducto());
        $stmt->bindValue(':id_planta', $catalogo->getIdPlanta());
        $stmt->bindValue(':activo', $catalogo->isActivo(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_catalogo', $catalogo->getIdCatalogo());
        
        $stmt->execute();
        
        return $catalogo;
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM catalogo_productos WHERE id_catalogo = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function softDelete(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE catalogo_productos SET activo = 0 WHERE id_catalogo = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
