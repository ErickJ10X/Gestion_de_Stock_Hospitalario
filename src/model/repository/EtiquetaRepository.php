<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\Etiqueta;
use model\entity\Producto;
use model\entity\Reposicion;
use PDO;

class EtiquetaRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?Etiqueta {
        $stmt = $this->conexion->prepare("
            SELECT e.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   r.fecha as reposicion_fecha, r.urgente as reposicion_urgente
            FROM etiquetas e
            LEFT JOIN productos p ON e.id_producto = p.id_producto
            LEFT JOIN reposiciones r ON e.id_reposicion = r.id_reposicion
            WHERE e.id_etiqueta = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        return $this->createEtiquetaFromData($data);
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("
            SELECT e.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   r.fecha as reposicion_fecha, r.urgente as reposicion_urgente
            FROM etiquetas e
            LEFT JOIN productos p ON e.id_producto = p.id_producto
            LEFT JOIN reposiciones r ON e.id_reposicion = r.id_reposicion
        ");
        
        $etiquetas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etiquetas[] = $this->createEtiquetaFromData($data);
        }
        
        return $etiquetas;
    }

    public function findByReposicion(int $idReposicion): array {
        $stmt = $this->conexion->prepare("
            SELECT e.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   r.fecha as reposicion_fecha, r.urgente as reposicion_urgente
            FROM etiquetas e
            LEFT JOIN productos p ON e.id_producto = p.id_producto
            LEFT JOIN reposiciones r ON e.id_reposicion = r.id_reposicion
            WHERE e.id_reposicion = :id_reposicion
        ");
        $stmt->bindParam(':id_reposicion', $idReposicion);
        $stmt->execute();
        
        $etiquetas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etiquetas[] = $this->createEtiquetaFromData($data);
        }
        
        return $etiquetas;
    }

    public function findNoImpresas(): array {
        $stmt = $this->conexion->query("
            SELECT e.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo,
                   r.fecha as reposicion_fecha, r.urgente as reposicion_urgente
            FROM etiquetas e
            LEFT JOIN productos p ON e.id_producto = p.id_producto
            LEFT JOIN reposiciones r ON e.id_reposicion = r.id_reposicion
            WHERE e.impresa = 0
        ");
        
        $etiquetas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etiquetas[] = $this->createEtiquetaFromData($data);
        }
        
        return $etiquetas;
    }

    public function save(Etiqueta $etiqueta): Etiqueta {
        if ($etiqueta->getId() === null) {
            return $this->insert($etiqueta);
        } else {
            return $this->update($etiqueta);
        }
    }

    private function insert(Etiqueta $etiqueta): Etiqueta {
        $stmt = $this->conexion->prepare("
            INSERT INTO etiquetas (id_producto, id_reposicion, tipo, prioridad, impresa)
            VALUES (:id_producto, :id_reposicion, :tipo, :prioridad, :impresa)
        ");
        
        $stmt->bindValue(':id_producto', $etiqueta->getIdProducto());
        $stmt->bindValue(':id_reposicion', $etiqueta->getIdReposicion());
        $stmt->bindValue(':tipo', $etiqueta->getTipo());
        $stmt->bindValue(':prioridad', $etiqueta->getPrioridad());
        $stmt->bindValue(':impresa', $etiqueta->isImpresa(), PDO::PARAM_BOOL);
        
        $stmt->execute();
        $etiqueta->setId($this->conexion->lastInsertId());
        
        return $etiqueta;
    }

    private function update(Etiqueta $etiqueta): Etiqueta {
        $stmt = $this->conexion->prepare("
            UPDATE etiquetas
            SET id_producto = :id_producto,
                id_reposicion = :id_reposicion,
                tipo = :tipo,
                prioridad = :prioridad,
                impresa = :impresa
            WHERE id_etiqueta = :id_etiqueta
        ");
        
        $stmt->bindValue(':id_producto', $etiqueta->getIdProducto());
        $stmt->bindValue(':id_reposicion', $etiqueta->getIdReposicion());
        $stmt->bindValue(':tipo', $etiqueta->getTipo());
        $stmt->bindValue(':prioridad', $etiqueta->getPrioridad());
        $stmt->bindValue(':impresa', $etiqueta->isImpresa(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id_etiqueta', $etiqueta->getId());
        
        $stmt->execute();
        
        return $etiqueta;
    }

    public function marcarComoImpresa(int $id): bool {
        $stmt = $this->conexion->prepare("UPDATE etiquetas SET impresa = 1 WHERE id_etiqueta = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM etiquetas WHERE id_etiqueta = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    private function createEtiquetaFromData(array $data): Etiqueta {
        $etiqueta = new Etiqueta(
            $data['id_producto'],
            $data['id_reposicion'],
            $data['tipo'],
            $data['prioridad'],
            (bool)$data['impresa']
        );
        
        $etiqueta->setId($data['id_etiqueta'] ?? null);
        
        return $etiqueta;
    }
}
