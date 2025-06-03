<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\Reposicion;
use model\entity\Producto;
use model\entity\Almacen;
use model\entity\Botiquin;
use DateTime;
use PDO;

class ReposicionRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?Reposicion {
        $stmt = $this->conexion->prepare("
            SELECT r.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   a.tipo as almacen_tipo,
                   b.nombre as botiquin_nombre 
            FROM reposiciones r
            LEFT JOIN productos p ON r.id_producto = p.id_producto
            LEFT JOIN almacenes a ON r.desde_almacen = a.id_almacen
            LEFT JOIN botiquines b ON r.hacia_botiquin = b.id_botiquin
            WHERE r.id_reposicion = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        return $this->createReposicionFromData($data);
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("
            SELECT r.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   a.tipo as almacen_tipo,
                   b.nombre as botiquin_nombre 
            FROM reposiciones r
            LEFT JOIN productos p ON r.id_producto = p.id_producto
            LEFT JOIN almacenes a ON r.desde_almacen = a.id_almacen
            LEFT JOIN botiquines b ON r.hacia_botiquin = b.id_botiquin
            ORDER BY r.fecha DESC
        ");
        
        $reposiciones = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromData($data);
        }
        
        return $reposiciones;
    }
    
    public function findByProducto(int $idProducto): array {
        $stmt = $this->conexion->prepare("
            SELECT r.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   a.tipo as almacen_tipo,
                   b.nombre as botiquin_nombre 
            FROM reposiciones r
            LEFT JOIN productos p ON r.id_producto = p.id_producto
            LEFT JOIN almacenes a ON r.desde_almacen = a.id_almacen
            LEFT JOIN botiquines b ON r.hacia_botiquin = b.id_botiquin
            WHERE r.id_producto = :id_producto
            ORDER BY r.fecha DESC
        ");
        $stmt->bindParam(':id_producto', $idProducto);
        $stmt->execute();
        
        $reposiciones = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromData($data);
        }
        
        return $reposiciones;
    }
    
    public function findByBotiquin(int $idBotiquin): array {
        $stmt = $this->conexion->prepare("
            SELECT r.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   a.tipo as almacen_tipo,
                   b.nombre as botiquin_nombre 
            FROM reposiciones r
            LEFT JOIN productos p ON r.id_producto = p.id_producto
            LEFT JOIN almacenes a ON r.desde_almacen = a.id_almacen
            LEFT JOIN botiquines b ON r.hacia_botiquin = b.id_botiquin
            WHERE r.hacia_botiquin = :id_botiquin
            ORDER BY r.fecha DESC
        ");
        $stmt->bindParam(':id_botiquin', $idBotiquin);
        $stmt->execute();
        
        $reposiciones = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromData($data);
        }
        
        return $reposiciones;
    }
    
    public function findByAlmacen(int $idAlmacen): array {
        $stmt = $this->conexion->prepare("
            SELECT r.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   a.tipo as almacen_tipo,
                   b.nombre as botiquin_nombre 
            FROM reposiciones r
            LEFT JOIN productos p ON r.id_producto = p.id_producto
            LEFT JOIN almacenes a ON r.desde_almacen = a.id_almacen
            LEFT JOIN botiquines b ON r.hacia_botiquin = b.id_botiquin
            WHERE r.desde_almacen = :id_almacen
            ORDER BY r.fecha DESC
        ");
        $stmt->bindParam(':id_almacen', $idAlmacen);
        $stmt->execute();
        
        $reposiciones = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromData($data);
        }
        
        return $reposiciones;
    }

    public function findUrgentes(): array {
        $stmt = $this->conexion->query("
            SELECT r.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   a.tipo as almacen_tipo,
                   b.nombre as botiquin_nombre 
            FROM reposiciones r
            LEFT JOIN productos p ON r.id_producto = p.id_producto
            LEFT JOIN almacenes a ON r.desde_almacen = a.id_almacen
            LEFT JOIN botiquines b ON r.hacia_botiquin = b.id_botiquin
            WHERE r.urgente = 1
            ORDER BY r.fecha DESC
        ");
        
        $reposiciones = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromData($data);
        }
        
        return $reposiciones;
    }

    public function save(Reposicion $reposicion): Reposicion {
        if ($reposicion->getId() === null) {
            return $this->insert($reposicion);
        } else {
            return $this->update($reposicion);
        }
    }

    private function insert(Reposicion $reposicion): Reposicion {
        $stmt = $this->conexion->prepare("
            INSERT INTO reposiciones (id_producto, desde_almacen, hacia_botiquin, cantidad_repuesta, fecha, urgente)
            VALUES (:id_producto, :desde_almacen, :hacia_botiquin, :cantidad_repuesta, :fecha, :urgente)
        ");
        
        $fechaFormato = $reposicion->getFecha()->format('Y-m-d H:i:s');
        
        $stmt->bindValue(':id_producto', $reposicion->getIdProducto());
        $stmt->bindValue(':desde_almacen', $reposicion->getDesdeAlmacen());
        $stmt->bindValue(':hacia_botiquin', $reposicion->getHaciaBotiquin());
        $stmt->bindValue(':cantidad_repuesta', $reposicion->getCantidadRepuesta());
        $stmt->bindValue(':fecha', $fechaFormato);
        $stmt->bindValue(':urgente', $reposicion->isUrgente(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $reposicion->setId($this->conexion->lastInsertId());
        
        return $reposicion;
    }

    private function update(Reposicion $reposicion): Reposicion {
        $stmt = $this->conexion->prepare("
            UPDATE reposiciones
            SET id_producto = :id_producto,
                desde_almacen = :desde_almacen,
                hacia_botiquin = :hacia_botiquin,
                cantidad_repuesta = :cantidad_repuesta,
                fecha = :fecha,
                urgente = :urgente
            WHERE id_reposicion = :id_reposicion
        ");
        
        $fechaFormato = $reposicion->getFecha()->format('Y-m-d H:i:s');
        
        $stmt->bindValue(':id_producto', $reposicion->getIdProducto());
        $stmt->bindValue(':desde_almacen', $reposicion->getDesdeAlmacen());
        $stmt->bindValue(':hacia_botiquin', $reposicion->getHaciaBotiquin());
        $stmt->bindValue(':cantidad_repuesta', $reposicion->getCantidadRepuesta());
        $stmt->bindValue(':fecha', $fechaFormato);
        $stmt->bindValue(':urgente', $reposicion->isUrgente(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_reposicion', $reposicion->getId());
        
        $stmt->execute();
        
        return $reposicion;
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM reposiciones WHERE id_reposicion = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    private function createReposicionFromData(array $data): Reposicion {
        $reposicion = new Reposicion(
            $data['id_producto'],
            $data['desde_almacen'],
            $data['hacia_botiquin'],
            $data['cantidad_repuesta'],
            new DateTime($data['fecha']),
            (bool)$data['urgente']
        );
        
        $reposicion->setId($data['id_reposicion']);
        
        // Aquí se podrían añadir más datos relacionados si fuera necesario
        
        return $reposicion;
    }
}
