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

    public function mapToAlmacenes(array $row): Almacenes
    {
        return new Almacenes(
            $row['id_almacen'],
            $row['id_planta'],
            $row['tipo'],
            $row['id_hospital']
        );
    }

    public function mapToAlmacenesArray(array $rows): array
    {
        $almacenes = [];
        foreach ($rows as $row) {
            $almacenes[] = $this->mapToAlmacenes($row);
        }
        return $almacenes;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM almacenes";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $this->mapToAlmacenesArray($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findById($id): ?Almacenes
    {
        $sql = "SELECT * FROM almacenes WHERE id_almacen = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->mapToAlmacenes($result);
    }

    public function save(Almacenes $almacen): bool
    {
        $sql = "INSERT INTO almacenes (id_planta, tipo, id_hospital) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$almacen->getIdPlanta(), $almacen->getTipo(), $almacen->getIdHospital()]);
    }

    public function update(Almacenes $almacen): bool
    {
        $sql = "UPDATE almacenes SET id_planta = ?, tipo = ?, id_hospital = ? WHERE id_almacen = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$almacen->getIdPlanta(), $almacen->getTipo(), $almacen->getIdHospital(), $almacen->getIdAlmacen()]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM almacenes WHERE id_almacen = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
