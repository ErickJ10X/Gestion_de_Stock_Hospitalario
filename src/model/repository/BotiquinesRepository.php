<?php

namespace model\repository;

require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../entity/Botiquines.php');

use model\entity\Botiquines;
use PDO;

class BotiquinesRepository
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM botiquines";
        $stmt = $this->pdo->query($sql);
        $result = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Botiquines(
                $row['id'],
                $row['nombre'],
                $row['planta_id']
            );
        }
        
        return $result;
    }

    public function findById($id): ?Botiquines
    {
        $sql = "SELECT * FROM botiquines WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new Botiquines(
            $row['id'],
            $row['nombre'],
            $row['planta_id']
        );
    }

    public function save(Botiquines $botiquin): bool
    {
        $sql = "INSERT INTO botiquines (nombre, planta_id) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $botiquin->getNombre(),
            $botiquin->getIdPlanta()
        ]);
    }

    public function update(Botiquines $botiquin): bool
    {
        $sql = "UPDATE botiquines SET nombre = ?, planta_id = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $botiquin->getNombre(),
            $botiquin->getIdPlanta(),
            $botiquin->getIdBotiquines()
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM botiquines WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
