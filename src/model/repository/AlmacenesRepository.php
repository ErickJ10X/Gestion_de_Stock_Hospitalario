<?php

namespace model\repository;

require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../entity/Almacenes.php');

use model\entity\Almacenes;
use PDO;

class AlmacenesRepository
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM almacenes";
        $stmt = $this->pdo->query($sql);
        $result = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Almacenes(
                $row['id'],
                $row['planta_id']
            );
        }
        
        return $result;
    }

    public function findById($id): ?Almacenes
    {
        $sql = "SELECT * FROM almacenes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new Almacenes(
            $row['id'],
            $row['planta_id']
        );
    }

    public function save(Almacenes $almacen): bool
    {
        $sql = "INSERT INTO almacenes (planta_id) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $almacen->getIdPlanta()
        ]);
    }

    public function update(Almacenes $almacen): bool
    {
        $sql = "UPDATE almacenes SET planta_id = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $almacen->getIdPlanta(),
            $almacen->getIdAlmacen()
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM almacenes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
