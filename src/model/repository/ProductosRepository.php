<?php

namespace model\repository;
require_once(__DIR__ . '/../../../config/database.php');

use model\entity\Productos;
use PDO;

class ProductosRepository
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function mapToProducto(array $row): Productos
    {
        return new Productos(
            $row['id_producto'],
            $row['codigo'],
            $row['nombre'],
            $row['descripcion'],
            $row['unidad_medida']
        );
    }

    private function mapToProductoArray(array $rows): array
    {
        $productos = [];
        foreach ($rows as $row) {
            $productos[] = $this->mapToProducto($row);
        }
        return $productos;
    }


    public function findAll(): array
    {
        $sql = "SELECT * FROM productos";
        return $this->mapToProductoArray($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }
    public function findById($id): ?Productos
    {
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $this->mapToProducto($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function save($producto): bool
    {
        $sql = "INSERT INTO productos (codigo, nombre, descripcion, unidad_medida) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $producto->getCodigo(),
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getUnidadMedida()
        ]);
        return $stmt->rowCount() > 0;
    }
    public function update($producto): bool
    {
        $sql = "UPDATE productos SET codigo = ?, nombre = ?, descripcion = ?, unidad_medida = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $producto->getCodigo(),
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getUnidadMedida(),
            $producto->getId()
        ]);
        return $stmt->rowCount() > 0;
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
