<?php

namespace model\service;

require_once(__DIR__ . '/../entity/Productos.php');
require_once(__DIR__ . '/../repository/ProductosRepository.php');
require_once(__DIR__ . '/../../../config/database.php');

use model\entity\Productos;
use model\repository\ProductosRepository;
use PDOException;
use Exception;

class ProductoService
{
    private ProductosRepository $productoRepository;

    public function __construct()
    {
        $this->productoRepository = new ProductosRepository();
    }

    public function getAllProductos(): array
    {
        try {
            return $this->productoRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al cargar los productos: " . $e->getMessage());
        }
    }

    public function getProductoById($id): ?Productos
    {
        try {
            $producto = $this->productoRepository->findById($id);
            if (!$producto) {
                return null;
            }
            return $producto;
        } catch (PDOException $e) {
            throw new Exception("Error al cargar el producto: " . $e->getMessage());
        }
    }

    public function deleteProducto($id): bool
    {
        try {
            return $this->productoRepository->delete($id);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el producto: " . $e->getMessage());
        }
    }

    public function updateProducto($id, $codigo, $nombre, $descripcion, $unidad_medida): bool
    {
        try {
            $producto = new Productos(
                $id,
                $codigo,
                $nombre,
                $descripcion,
                $unidad_medida
            );

            return $this->productoRepository->update($producto);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el producto: " . $e->getMessage());
        }
    }

    public function createProducto($codigo, $nombre, $descripcion, $unidad_medida): bool
    {
        try {
            $producto = new Productos(
                null,
                $codigo,
                $nombre,
                $descripcion,
                $unidad_medida
            );

            return $this->productoRepository->save($producto);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el producto: " . $e->getMessage());
        }
    }
}
