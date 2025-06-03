<?php

namespace repositories;

use repositories\interfaces\EtiquetaRepositoryInterface;
use models\Etiqueta;
use PDO;

class EtiquetaRepository implements EtiquetaRepositoryInterface {
    private PDO $db;

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $this->db = getConnection();
    }

    public function findById(int $id): ?Etiqueta {
        $stmt = $this->db->prepare("SELECT * FROM etiquetas WHERE id_etiqueta = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        
        return $this->createEtiquetaFromData($result);
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM etiquetas");
        return $this->hydrateEtiquetas($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findByReposicion(int $idReposicion): array {
        $stmt = $this->db->prepare("SELECT * FROM etiquetas WHERE id_reposicion = :id_reposicion");
        $stmt->bindParam(':id_reposicion', $idReposicion, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->hydrateEtiquetas($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findByProducto(int $idProducto): array {
        $stmt = $this->db->prepare("SELECT * FROM etiquetas WHERE id_producto = :id_producto");
        $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->hydrateEtiquetas($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findByTipo(string $tipo): array {
        $stmt = $this->db->prepare("SELECT * FROM etiquetas WHERE tipo = :tipo");
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        
        return $this->hydrateEtiquetas($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findByPrioridad(string $prioridad): array {
        $stmt = $this->db->prepare("SELECT * FROM etiquetas WHERE prioridad = :prioridad");
        $stmt->bindParam(':prioridad', $prioridad);
        $stmt->execute();
        
        return $this->hydrateEtiquetas($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findNoImpresas(): array {
        $stmt = $this->db->prepare("SELECT * FROM etiquetas WHERE impresa = 0");
        $stmt->execute();
        
        return $this->hydrateEtiquetas($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function save(Etiqueta $etiqueta): int {
        $stmt = $this->db->prepare("
            INSERT INTO etiquetas (id_producto, id_reposicion, tipo, prioridad, impresa) 
            VALUES (:id_producto, :id_reposicion, :tipo, :prioridad, :impresa)
        ");
        
        $this->bindEtiquetaParams($stmt, $etiqueta);
        
        $stmt->execute();
        $id = (int)$this->db->lastInsertId();
        $etiqueta->setId($id);
        
        return $id;
    }

    public function update(Etiqueta $etiqueta): bool {
        if ($etiqueta->getId() === null) {
            return false;
        }
        
        $stmt = $this->db->prepare("
            UPDATE etiquetas 
            SET id_producto = :id_producto,
                id_reposicion = :id_reposicion,
                tipo = :tipo,
                prioridad = :prioridad,
                impresa = :impresa
            WHERE id_etiqueta = :id_etiqueta
        ");
        
        $id = $etiqueta->getId();
        $stmt->bindParam(':id_etiqueta', $id, PDO::PARAM_INT);
        $this->bindEtiquetaParams($stmt, $etiqueta);
        
        return $stmt->execute();
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM etiquetas WHERE id_etiqueta = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    private function bindEtiquetaParams($stmt, Etiqueta $etiqueta): void {
        $idProducto = $etiqueta->getIdProducto();
        $idReposicion = $etiqueta->getIdReposicion();
        $tipo = $etiqueta->getTipo();
        $prioridad = $etiqueta->getPrioridad();
        $impresa = $etiqueta->isImpresa() ? 1 : 0;
        
        $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
        $stmt->bindParam(':id_reposicion', $idReposicion, PDO::PARAM_INT);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':prioridad', $prioridad);
        $stmt->bindParam(':impresa', $impresa, PDO::PARAM_INT);
    }

    private function createEtiquetaFromData(array $data): Etiqueta {
        $etiqueta = new Etiqueta(
            $data['id_producto'],
            $data['id_reposicion'],
            $data['tipo'],
            $data['prioridad'],
            (bool)$data['impresa']
        );
        $etiqueta->setId($data['id_etiqueta']);
        
        return $etiqueta;
    }

    private function hydrateEtiquetas(array $data): array {
        $etiquetas = [];
        foreach ($data as $row) {
            $etiquetas[] = $this->createEtiquetaFromData($row);
        }
        return $etiquetas;
    }
}
