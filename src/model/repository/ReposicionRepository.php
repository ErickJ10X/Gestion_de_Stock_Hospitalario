<?php

namespace model\repository;

require_once __DIR__ . '/../entity/Reposicion.php';
require_once __DIR__ . '/../../../config/Database.php';

use model\entity\Reposicion;
use config\Database;
use DateTime;
use PDO;
use PDOException;

class ReposicionRepository {
    private PDO $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function findById(int $id): ?Reposicion {
        $sql = "SELECT * FROM reposiciones WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->createReposicionFromRow($row);
    }

    public function findAll(): array {
        $sql = "SELECT * FROM reposiciones ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }
        
        return $reposiciones;
    }

    /**
     * Busca reposiciones dentro de un rango de fechas
     * 
     * @param DateTime $fechaDesde Fecha inicial
     * @param DateTime $fechaHasta Fecha final
     * @return array Lista de reposiciones
     */
    public function findByFechas(DateTime $fechaDesde, DateTime $fechaHasta): array {
        $sql = "SELECT * FROM reposiciones WHERE fecha >= :fecha_desde AND fecha <= :fecha_hasta ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        
        $fechaDesdeStr = $fechaDesde->format('Y-m-d 00:00:00');
        $fechaHastaStr = $fechaHasta->format('Y-m-d 23:59:59');
        
        $stmt->bindParam(':fecha_desde', $fechaDesdeStr);
        $stmt->bindParam(':fecha_hasta', $fechaHastaStr);
        $stmt->execute();
        
        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }
        
        return $reposiciones;
    }

    public function findByAlmacen(int $idAlmacen): array {
        $sql = "SELECT * FROM reposiciones WHERE desde_almacen = :id_almacen ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_almacen', $idAlmacen);
        $stmt->execute();
        
        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }
        
        return $reposiciones;
    }

    public function findByBotiquin(int $idBotiquin): array {
        $sql = "SELECT * FROM reposiciones WHERE hacia_botiquin = :id_botiquin ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_botiquin', $idBotiquin);
        $stmt->execute();
        
        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }
        
        return $reposiciones;
    }

    public function findUrgentes(): array {
        $sql = "SELECT * FROM reposiciones WHERE urgente = TRUE ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
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
        $sql = "INSERT INTO reposiciones (id_producto, desde_almacen, hacia_botiquin, cantidad_repuesta, fecha, urgente, notas)
                VALUES (:id_producto, :desde_almacen, :hacia_botiquin, :cantidad_repuesta, :fecha, :urgente, :notas)";
        
        $stmt = $this->conn->prepare($sql);
        
        $idProducto = $reposicion->getIdProducto();
        $desdeAlmacen = $reposicion->getDesdeAlmacen();
        $haciaBotiquin = $reposicion->getHaciaBotiquin();
        $cantidadRepuesta = $reposicion->getCantidadRepuesta();
        $fecha = $reposicion->getFecha()->format('Y-m-d H:i:s');
        $urgente = $reposicion->isUrgente() ? 1 : 0;
        $notas = $reposicion->getNotas();
        
        $stmt->bindParam(':id_producto', $idProducto);
        $stmt->bindParam(':desde_almacen', $desdeAlmacen);
        $stmt->bindParam(':hacia_botiquin', $haciaBotiquin);
        $stmt->bindParam(':cantidad_repuesta', $cantidadRepuesta);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':urgente', $urgente);
        $stmt->bindParam(':notas', $notas);
        
        $stmt->execute();
        
        $id = $this->conn->lastInsertId();
        $reposicion->setId($id);
        
        return $reposicion;
    }

    private function update(Reposicion $reposicion): Reposicion {
        $sql = "UPDATE reposiciones 
                SET id_producto = :id_producto, 
                    desde_almacen = :desde_almacen,
                    hacia_botiquin = :hacia_botiquin,
                    cantidad_repuesta = :cantidad_repuesta,
                    fecha = :fecha,
                    urgente = :urgente,
                    notas = :notas
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        
        $id = $reposicion->getId();
        $idProducto = $reposicion->getIdProducto();
        $desdeAlmacen = $reposicion->getDesdeAlmacen();
        $haciaBotiquin = $reposicion->getHaciaBotiquin();
        $cantidadRepuesta = $reposicion->getCantidadRepuesta();
        $fecha = $reposicion->getFecha()->format('Y-m-d H:i:s');
        $urgente = $reposicion->isUrgente() ? 1 : 0;
        $notas = $reposicion->getNotas();
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':id_producto', $idProducto);
        $stmt->bindParam(':desde_almacen', $desdeAlmacen);
        $stmt->bindParam(':hacia_botiquin', $haciaBotiquin);
        $stmt->bindParam(':cantidad_repuesta', $cantidadRepuesta);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':urgente', $urgente);
        $stmt->bindParam(':notas', $notas);
        
        $stmt->execute();
        
        return $reposicion;
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM reposiciones WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    private function createReposicionFromRow(array $row): Reposicion {
        return new Reposicion(
            (int)$row['id'],
            (int)$row['id_producto'],
            (int)$row['desde_almacen'],
            (int)$row['hacia_botiquin'],
            (float)$row['cantidad_repuesta'],
            new DateTime($row['fecha']),
            (bool)$row['urgente'],
            $row['notas'] ?? ''
        );
    }
}
