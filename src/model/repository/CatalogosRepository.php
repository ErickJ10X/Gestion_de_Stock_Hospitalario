<?php

namespace model\repository;

require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../entity/Catalogos_productos.php');

use model\entity\Catalogos_productos;
use PDO;

class CatalogosRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function mapToCatalogos($row): Catalogos_productos
    {
        return new Catalogos_productos(
            $row['id_catalogo'],
            $row['id_producto'],
            $row['id_planta']
        );
    }

    public function mapToCatalogosArray($rows): array
    {
        $catalogos = [];
        foreach ($rows as $row) {
            $catalogos[] = $this->mapToCatalogos($row);
        }
        return $catalogos;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM catalogos_productos");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapToCatalogosArray($rows);
    }

    public function findById($id): ?Catalogos_productos
    {
        $stmt = $this->pdo->prepare("SELECT * FROM catalogos_productos WHERE id_catalogo = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToCatalogos($row) : null;
    }

    public function findByProducto($idProducto): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM catalogos_productos WHERE id_producto = :id_producto");
        $stmt->bindParam(':id_producto', $idProducto);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapToCatalogosArray($rows);
    }

    public function findByPlanta($idPlanta): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM catalogos_productos WHERE id_planta = :id_planta");
        $stmt->bindParam(':id_planta', $idPlanta);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapToCatalogosArray($rows);
    }

    public function save(Catalogos_productos $catalogo): bool
    {
        $sql = "INSERT INTO catalogos_productos (id_catalogo, id_producto, id_planta) VALUES (:id_catalogo, :id_producto, :id_planta)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_catalogo' => $catalogo->getIdCatalogo(),
            ':id_producto' => $catalogo->getIdProducto(),
            ':id_planta' => $catalogo->getIdPlanta()
        ]);
    }

    public function update(Catalogos_productos $catalogo): bool
    {
        $sql = "UPDATE catalogos_productos SET id_producto = :id_producto, id_planta = :id_planta WHERE id_catalogo = :id_catalogo";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_catalogo' => $catalogo->getIdCatalogo(),
            ':id_producto' => $catalogo->getIdProducto(),
            ':id_planta' => $catalogo->getIdPlanta()
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM catalogos_productos WHERE id_catalogo = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
