<?php

namespace repositories;

use models\Reposicion;
use PDO;
use repositories\interfaces\ReposicionRepositoryInterface;

class ReposicionRepository implements ReposicionRepositoryInterface {
    private PDO $db;

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        $this->db = getConnection();
    }

    public function findById(int $id): ?Reposicion {
        $stmt = $this->db->prepare("SELECT * FROM reposiciones WHERE id_reposicion = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        
        return $this->createReposicionFromData($result);
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM reposiciones ORDER BY fecha DESC");
        return $this->hydrateReposiciones($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findByBotiquin(int $idBotiquin): array {
        $stmt = $this->db->prepare("SELECT * FROM reposiciones WHERE hacia_botiquin = :id_botiquin ORDER BY fecha DESC");
        $stmt->bindParam(':id_botiquin', $idBotiquin, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->hydrateReposiciones($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findByAlmacen(int $idAlmacen): array {
        $stmt = $this->db->prepare("SELECT * FROM reposiciones WHERE desde_almacen = :id_almacen ORDER BY fecha DESC");
        $stmt->bindParam(':id_almacen', $idAlmacen, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->hydrateReposiciones($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findByProducto(int $idProducto): array {
        $stmt = $this->db->prepare("SELECT * FROM reposiciones WHERE id_producto = :id_producto ORDER BY fecha DESC");
        $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->hydrateReposiciones($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findUrgentes(): array {
        $stmt = $this->db->prepare("SELECT * FROM reposiciones WHERE urgente = 1 ORDER BY fecha DESC");
        $stmt->execute();
        
        return $this->hydrateReposiciones($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function save(Reposicion $reposicion): int {
        $stmt = $this->db->prepare("
            INSERT INTO reposiciones (id_producto, desde_almacen, hacia_botiquin, cantidad_repuesta, fecha, urgente) 
            VALUES (:id_producto, :desde_almacen, :hacia_botiquin, :cantidad_repuesta, :fecha, :urgente)
        ");
        
        $this->bindReposicionParams($stmt, $reposicion);
        
        $stmt->execute();
        $id = (int)$this->db->lastInsertId();
        $reposicion->setId($id);
        
        return $id;
    }

    public function update(Reposicion $reposicion): bool {
        if ($reposicion->getId() === null) {
            return false;
        }
        
        $stmt = $this->db->prepare("
            UPDATE reposiciones 
            SET id_producto = :id_producto,
                desde_almacen = :desde_almacen,
                hacia_botiquin = :hacia_botiquin,
                cantidad_repuesta = :cantidad_repuesta,
                fecha = :fecha,
                urgente = :urgente
            WHERE id_reposicion = :id_reposicion
        ");
        
        $id = $reposicion->getId();
        $stmt->bindParam(':id_reposicion', $id, PDO::PARAM_INT);
        $this->bindReposicionParams($stmt, $reposicion);
        
        return $stmt->execute();
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM reposiciones WHERE id_reposicion = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    private function bindReposicionParams($stmt, Reposicion $reposicion): void {
        $idProducto = $reposicion->getIdProducto();
        $desdeAlmacen = $reposicion->getDesdeAlmacen();
        $haciaBotiquin = $reposicion->getHaciaBotiquin();
        $cantidadRepuesta = $reposicion->getCantidadRepuesta();
        $fecha = $reposicion->getFecha()->format('Y-m-d H:i:s');
        $urgente = $reposicion->isUrgente() ? 1 : 0;
        
        $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
        $stmt->bindParam(':desde_almacen', $desdeAlmacen, PDO::PARAM_INT);
        $stmt->bindParam(':hacia_botiquin', $haciaBotiquin, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad_repuesta', $cantidadRepuesta, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':urgente', $urgente, PDO::PARAM_INT);
    }

    private function createReposicionFromData(array $data): Reposicion {
        $reposicion = new Reposicion(
            $data['id_producto'],
            $data['desde_almacen'],
            $data['hacia_botiquin'],
            $data['cantidad_repuesta'],
            new \DateTime($data['fecha']),
            (bool)$data['urgente']
        );
        $reposicion->setId($data['id_reposicion']);
        
        return $reposicion;
    }

    private function hydrateReposiciones(array $data): array {
        $reposiciones = [];
        foreach ($data as $row) {
            $reposiciones[] = $this->createReposicionFromData($row);
        }
        return $reposiciones;
    }
}
