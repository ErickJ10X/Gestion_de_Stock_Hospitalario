<?php

namespace model\repository;

use model\entity\Reposiciones;
use PDO;

class ReposicionesRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function mapToReposiciones($row): Reposiciones
    {
        return new Reposiciones(
            $row['id_reposicion'],
            $row['id_producto'],
            $row['desde_almacen'],
            $row['hasta_botiquin'],
            $row['cantidad_repuesta'],
            $row['fecha'],
            $row['urgente']
        );
    }

    public function mapToReposicionesList($rows): array
    {
        $reposicionesList = [];
        foreach ($rows as $row) {
            $reposicionesList[] = $this->mapToReposiciones($row);
        }
        return $reposicionesList;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reposiciones");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapToReposicionesList($rows);
    }

    public function findById($id): ?Reposiciones
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reposiciones WHERE id_reposicion = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToReposiciones($row) : null;
    }

    public function findByIdProducto($id_producto): ?Reposiciones
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reposiciones WHERE id_producto = :id_producto");
        $stmt->bindParam(':id_producto', $id_producto);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToReposiciones($row) : null;
    }

    public function findByIdBotiquin($id_botiquin): ?Reposiciones
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reposiciones WHERE hasta_botiquin = :id_botiquin");
        $stmt->bindParam(':id_botiquin', $id_botiquin);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToReposiciones($row) : null;
    }

    public function findByIdAlmacen($id_almacen): ?Reposiciones
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reposiciones WHERE desde_almacen = :id_almacen");
        $stmt->bindParam(':id_almacen', $id_almacen);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToReposiciones($row) : null;
    }

    public function findByUrgente($urgente): ?Reposiciones
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reposiciones WHERE urgente = :urgente");
        $stmt->bindParam(':urgente', $urgente);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToReposiciones($row) : null;
    }

    public function save(Reposiciones $reposicion): bool
    {
        $sql = "INSERT INTO reposiciones (id_producto, desde_almacen, hasta_botiquin, cantidad_repuesta, fecha, urgente) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $reposicion->getIdProducto(),
            $reposicion->getDesdeAlmacen(),
            $reposicion->getHastaBotiquin(),
            $reposicion->getCantidadRepuesta(),
            $reposicion->getFecha(),
            $reposicion->getUrgente()
        ]);
    }

    public function update(Reposiciones $reposicion): bool
    {
        $sql = "UPDATE reposiciones SET id_producto = ?, desde_almacen = ?, hasta_botiquin = ?, cantidad_repuesta = ?, fecha = ?, urgente = ? WHERE id_reposicion = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $reposicion->getIdProducto(),
            $reposicion->getDesdeAlmacen(),
            $reposicion->getHastaBotiquin(),
            $reposicion->getCantidadRepuesta(),
            $reposicion->getFecha(),
            $reposicion->getUrgente(),
            $reposicion->getIdReposicion()
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM reposiciones WHERE id_reposicion = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}