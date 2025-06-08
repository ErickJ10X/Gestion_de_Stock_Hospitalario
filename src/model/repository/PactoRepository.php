<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../entity/Pacto.php';
require_once __DIR__ . '/../entity/Producto.php';

use model\entity\Pacto;
use model\entity\Producto;
use PDO;

class PactoRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?Pacto {
        $stmt = $this->conexion->prepare("
            SELECT p.*, pr.codigo, pr.nombre as producto_nombre, pr.descripcion, pr.unidad_medida
            FROM pactos p
            LEFT JOIN productos pr ON p.id_producto = pr.id_producto
            WHERE p.id_pacto = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        $pacto = new Pacto();
        $pacto->setIdPacto($data['id_pacto'])
              ->setIdProducto($data['id_producto'])
              ->setTipoUbicacion($data['tipo_ubicacion'])
              ->setIdDestino($data['id_destino'])
              ->setCantidadPactada($data['cantidad_pactada'])
              ->setActivo($data['activo']);
        
        // Establecer relación con producto si hay datos disponibles
        if (isset($data['producto_nombre'])) {
            $producto = new Producto();
            $producto->setIdProducto($data['id_producto'])
                     ->setCodigo($data['codigo'] ?? '')
                     ->setNombre($data['producto_nombre'])
                     ->setDescripcion($data['descripcion'] ?? '')
                     ->setUnidadMedida($data['unidad_medida'] ?? '');
            
            $pacto->setProducto($producto);
        }
        
        return $pacto;
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("
            SELECT p.*, pr.codigo, pr.nombre as producto_nombre, pr.descripcion, pr.unidad_medida
            FROM pactos p
            LEFT JOIN productos pr ON p.id_producto = pr.id_producto
        ");
        
        $pactos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pacto = new Pacto();
            $pacto->setIdPacto($data['id_pacto'])
                  ->setIdProducto($data['id_producto'])
                  ->setTipoUbicacion($data['tipo_ubicacion'])
                  ->setIdDestino($data['id_destino'])
                  ->setCantidadPactada($data['cantidad_pactada'])
                  ->setActivo($data['activo']);
            
            // Establecer relación con producto si hay datos disponibles
            if (isset($data['producto_nombre'])) {
                $producto = new Producto();
                $producto->setIdProducto($data['id_producto'])
                         ->setCodigo($data['codigo'] ?? '')
                         ->setNombre($data['producto_nombre'])
                         ->setDescripcion($data['descripcion'] ?? '')
                         ->setUnidadMedida($data['unidad_medida'] ?? '');
                
                $pacto->setProducto($producto);
            }
            
            $pactos[] = $pacto;
        }
        
        return $pactos;
    }
    
    public function findByProducto(int $idProducto): array {
        $stmt = $this->conexion->prepare("
            SELECT p.*, pr.codigo, pr.nombre as producto_nombre, pr.descripcion, pr.unidad_medida
            FROM pactos p
            LEFT JOIN productos pr ON p.id_producto = pr.id_producto
            WHERE p.id_producto = :id_producto
        ");
        $stmt->bindParam(':id_producto', $idProducto);
        $stmt->execute();
        
        $pactos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pacto = new Pacto();
            $pacto->setIdPacto($data['id_pacto'])
                  ->setIdProducto($data['id_producto'])
                  ->setTipoUbicacion($data['tipo_ubicacion'])
                  ->setIdDestino($data['id_destino'])
                  ->setCantidadPactada($data['cantidad_pactada'])
                  ->setActivo($data['activo']);
            
            // Establecer relación con producto si hay datos disponibles
            if (isset($data['producto_nombre'])) {
                $producto = new Producto();
                $producto->setIdProducto($data['id_producto'])
                         ->setCodigo($data['codigo'] ?? '')
                         ->setNombre($data['producto_nombre'])
                         ->setDescripcion($data['descripcion'] ?? '')
                         ->setUnidadMedida($data['unidad_medida'] ?? '');
                
                $pacto->setProducto($producto);
            }
            
            $pactos[] = $pacto;
        }
        
        return $pactos;
    }
    
    public function findByDestino(string $tipoUbicacion, int $idDestino): array {
        $stmt = $this->conexion->prepare("
            SELECT p.*, pr.codigo, pr.nombre as producto_nombre, pr.descripcion, pr.unidad_medida
            FROM pactos p
            LEFT JOIN productos pr ON p.id_producto = pr.id_producto
            WHERE p.tipo_ubicacion = :tipo_ubicacion AND p.id_destino = :id_destino
        ");
        $stmt->bindParam(':tipo_ubicacion', $tipoUbicacion);
        $stmt->bindParam(':id_destino', $idDestino);
        $stmt->execute();
        
        $pactos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pacto = new Pacto();
            $pacto->setIdPacto($data['id_pacto'])
                  ->setIdProducto($data['id_producto'])
                  ->setTipoUbicacion($data['tipo_ubicacion'])
                  ->setIdDestino($data['id_destino'])
                  ->setCantidadPactada($data['cantidad_pactada'])
                  ->setActivo($data['activo']);
            
            // Establecer relación con producto si hay datos disponibles
            if (isset($data['producto_nombre'])) {
                $producto = new Producto();
                $producto->setIdProducto($data['id_producto'])
                         ->setCodigo($data['codigo'] ?? '')
                         ->setNombre($data['producto_nombre'])
                         ->setDescripcion($data['descripcion'] ?? '')
                         ->setUnidadMedida($data['unidad_medida'] ?? '');
                
                $pacto->setProducto($producto);
            }
            
            $pactos[] = $pacto;
        }
        
        return $pactos;
    }

    public function save(Pacto $pacto): Pacto {
        if (!$pacto->getIdPacto() !== null) {
            return $this->insert($pacto);
        } else {
            return $this->update($pacto);
        }
    }

    private function insert(Pacto $pacto): Pacto {
        $stmt = $this->conexion->prepare("
            INSERT INTO pactos (id_producto, tipo_ubicacion, id_destino, cantidad_pactada, activo)
            VALUES (:id_producto, :tipo_ubicacion, :id_destino, :cantidad_pactada, :activo)
        ");
        
        $stmt->bindValue(':id_producto', $pacto->getIdProducto());
        $stmt->bindValue(':tipo_ubicacion', $pacto->getTipoUbicacion());
        $stmt->bindValue(':id_destino', $pacto->getIdDestino());
        $stmt->bindValue(':cantidad_pactada', $pacto->getCantidadPactada());
        $stmt->bindValue(':activo', $pacto->isActivo(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $pacto->setIdPacto($this->conexion->lastInsertId());
        
        return $pacto;
    }

    private function update(Pacto $pacto): Pacto {
        $stmt = $this->conexion->prepare("
            UPDATE pactos
            SET id_producto = :id_producto,
                tipo_ubicacion = :tipo_ubicacion,
                id_destino = :id_destino,
                cantidad_pactada = :cantidad_pactada,
                activo = :activo
            WHERE id_pacto = :id_pacto
        ");
        
        $stmt->bindValue(':id_producto', $pacto->getIdProducto());
        $stmt->bindValue(':tipo_ubicacion', $pacto->getTipoUbicacion());
        $stmt->bindValue(':id_destino', $pacto->getIdDestino());
        $stmt->bindValue(':cantidad_pactada', $pacto->getCantidadPactada());
        $stmt->bindValue(':activo', $pacto->isActivo(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_pacto', $pacto->getIdPacto());
        
        $stmt->execute();
        
        return $pacto;
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM pactos WHERE id_pacto = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function softDelete(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE pactos SET activo = 0 WHERE id_pacto = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
