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

    public function mapToBotiquines(array $row): Botiquines
    {
        return new Botiquines(
            $row['id_botiquin'],
            $row['nombre'],
            $row['id_planta']
        );
    }

    public function mapToBotiquinesArray(array $rows): array
    {
        $botiquines = [];
        foreach ($rows as $row) {
            $botiquines[] = $this->mapToBotiquines($row);
        }
        return $botiquines;
    }
    public function findAll(): array
    {
        $sql = "SELECT * FROM botiquines";
        $stmt = $this->pdo->query($sql);
        return $this->mapToBotiquinesArray($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findById($id): ?Botiquines
    {
        $sql = "SELECT * FROM botiquines WHERE id_botiquin = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $this->mapToBotiquines($stmt->fetch(PDO::FETCH_ASSOC));

    }

    public function save(Botiquines $botiquin): bool
    {
        $sql = "INSERT INTO botiquines (nombre, id_planta) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$botiquin->getNombre(), $botiquin->getIdPlanta()]);
    }

    public function update(Botiquines $botiquin): bool
    {
        $sql = "UPDATE botiquines SET nombre = ?, id_planta = ? WHERE id_botiquin = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$botiquin->getNombre(), $botiquin->getIdPlanta(), $botiquin->getIdBotiquines()]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM botiquines WHERE id_botiquin = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
