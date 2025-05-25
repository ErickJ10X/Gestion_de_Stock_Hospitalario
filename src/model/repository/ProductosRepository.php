<?php

namespace model\repository;
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../entity/Productos.php');

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
            $row['id_producto'] ?? null,
            $row['codigo'] ?? '',
            $row['nombre'] ?? '',
            $row['descripcion'] ?? '',
            $row['unidad_medida'] ?? ''
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
        $sql = "SELECT * FROM productos WHERE id_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToProducto($row) : null;
    }

    public function save(Productos $producto): bool
    {
        $sql = "INSERT INTO productos (codigo, nombre, descripcion, unidad_medida) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $producto->getCodigo(),
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getUnidadMedida()
        ]);
    }

    public function update(Productos $producto): bool
    {
        $sql = "UPDATE productos SET codigo = ?, nombre = ?, descripcion = ?, unidad_medida = ? WHERE id_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $producto->getCodigo(),
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getUnidadMedida(),
            $producto->getIdProducto()
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM productos WHERE id_producto = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
