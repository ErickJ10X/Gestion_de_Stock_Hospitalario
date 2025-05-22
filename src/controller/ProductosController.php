<?php

namespace controller;

require_once(__DIR__ . '/../model/service/ProductoService.php');
require_once(__DIR__ . '/../util/Session.php');

use model\service\ProductoService;
use util\Session;
use Exception;

class ProductosController
{
    private ProductoService $productosService;
    private Session $session;

    public function __construct()
    {
        $this->productosService = new ProductoService();
        $this->session = new Session();
    }

    public function getAllProductos(): array
    {
        try {
            return $this->productosService->getAllProductos();
        } catch (Exception $e) {
            $this->handleError("Error al obtener productos", $e->getMessage());
            return [];
        }
    }
    
    public function getProductoById($id): ?object
    {
        try {
            if (empty($id)) {
                throw new Exception("El ID del producto es requerido");
            }

            $producto = $this->productosService->getProductoById($id);

            if (!$producto) {
                throw new Exception("Producto no encontrado");
            }

            return $producto;
        } catch (Exception $e) {
            $this->handleError("Error al obtener producto", $e->getMessage());
            return null;
        }
    }
    
    public function createProducto($codigo, $nombre, $descripcion, $unidad_medida): bool
    {
        try {
            if (empty($codigo) || empty($nombre) || empty($descripcion) || empty($unidad_medida)) {
                throw new Exception("Todos los campos del producto son requeridos");
            }

            return $this->productosService->createProducto($codigo, $nombre, $descripcion, $unidad_medida);
        } catch (Exception $e) {
            $this->handleError("Error al crear producto", $e->getMessage());
            return false;
        }
    }
    
    public function updateProducto($id, $codigo, $nombre, $descripcion, $unidad_medida): bool
    {
        try {
            if (empty($id) || empty($codigo) || empty($nombre) || empty($descripcion) || empty($unidad_medida)) {
                throw new Exception("Todos los campos del producto son requeridos para la actualización");
            }

            return $this->productosService->updateProducto($id, $codigo, $nombre, $descripcion, $unidad_medida);
        } catch (Exception $e) {
            $this->handleError("Error al actualizar producto", $e->getMessage());
            return false;
        }
    }
    
    public function deleteProducto($id): bool
    {
        try {
            if (empty($id)) {
                throw new Exception("El ID del producto es requerido");
            }

            return $this->productosService->deleteProducto($id);
        } catch (Exception $e) {
            $this->handleError("Error al eliminar producto", $e->getMessage());
            return false;
        }
    }
    
    private function handleError($message, $error): void
    {
        $this->session->setMessage("error", $message . ": " . $error);
    }
}
