<?php

namespace controller;

use Exception;
use model\service\ProductoService;

require_once(__DIR__ . '/../model/service/ProductoService.php');

class ProductoController
{
    private ProductoService $productoService;

    public function __construct()
    {
        $this->productoService = new ProductoService();
    }

    public function index(): array
    {
        try {
            return ['error' => false, 'productos' => $this->productoService->getAllProductos()];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }
            
            $producto = $this->productoService->getProductoById($id);
            if ($producto) {
                return ['error' => false, 'producto' => $producto];
            } else {
                return ['error' => true, 'mensaje' => 'Producto no encontrado'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($codigo, $nombre, $descripcion, $unidad_medida): array
    {
        try {
            if (empty(trim($codigo))) {
                return ['error' => true, 'mensaje' => 'El código del producto es obligatorio'];
            }

            if (empty(trim($nombre))) {
                return ['error' => true, 'mensaje' => 'El nombre del producto es obligatorio'];
            }

            if (empty(trim($unidad_medida))) {
                return ['error' => true, 'mensaje' => 'La unidad de medida es obligatoria'];
            }

            $resultado = $this->productoService->createProducto(
                $codigo,
                $nombre,
                $descripcion,
                $unidad_medida
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Producto creado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear el producto'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($id, $codigo, $nombre, $descripcion, $unidad_medida): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }

            if (empty(trim($codigo))) {
                return ['error' => true, 'mensaje' => 'El código del producto es obligatorio'];
            }

            if (empty(trim($nombre))) {
                return ['error' => true, 'mensaje' => 'El nombre del producto es obligatorio'];
            }

            if (empty(trim($unidad_medida))) {
                return ['error' => true, 'mensaje' => 'La unidad de medida es obligatoria'];
            }

            // Verificar que el producto existe
            $productoExistente = $this->productoService->getProductoById($id);
            if (!$productoExistente) {
                return ['error' => true, 'mensaje' => 'El producto no existe'];
            }

            $resultado = $this->productoService->updateProducto(
                $id,
                $codigo,
                $nombre,
                $descripcion,
                $unidad_medida
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Producto actualizado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar el producto'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }

            // Verificar que el producto existe
            $productoExistente = $this->productoService->getProductoById($id);
            if (!$productoExistente) {
                return ['error' => true, 'mensaje' => 'El producto no existe'];
            }

            $resultado = $this->productoService->deleteProducto($id);
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Producto eliminado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar el producto'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
