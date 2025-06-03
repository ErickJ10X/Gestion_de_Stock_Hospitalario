<?php

namespace services;

use Models\Producto;
use Repositories\Interfaces\ProductoRepositoryInterface;
use Services\Interfaces\ProductoServiceInterface;

class ProductoService implements ProductoServiceInterface {
    private $productoRepository;

    public function __construct(ProductoRepositoryInterface $productoRepository) {
        $this->productoRepository = $productoRepository;
    }

    public function getAllProductos(): array {
        return $this->productoRepository->findAll();
    }

    public function getProductoById(int $id): ?Producto {
        return $this->productoRepository->findById($id);
    }
    
    public function getProductoByCodigo(string $codigo): ?Producto {
        return $this->productoRepository->findByCodigo($codigo);
    }

    public function getActiveProductos(): array {
        return $this->productoRepository->findActive();
    }

    public function createProducto(Producto $producto): Producto {
        // Validaciones básicas
        $this->validateProducto($producto);
        
        // Verificar que el código sea único
        $existingProducto = $this->productoRepository->findByCodigo($producto->getCodigo());
        if ($existingProducto !== null) {
            throw new \InvalidArgumentException('Ya existe un producto con el mismo código');
        }
        
        return $this->productoRepository->save($producto);
    }

    public function updateProducto(Producto $producto): bool {
        // Validaciones básicas
        $this->validateProducto($producto);
        
        if ($producto->getIdProducto() === null) {
            throw new \InvalidArgumentException('No se puede actualizar un producto sin ID');
        }
        
        // Verificar que el producto existe
        $existingProducto = $this->productoRepository->findById($producto->getIdProducto());
        if ($existingProducto === null) {
            throw new \InvalidArgumentException('El producto no existe');
        }
        
        // Verificar que el código sea único (excepto para este producto)
        $productoCodigo = $this->productoRepository->findByCodigo($producto->getCodigo());
        if ($productoCodigo !== null && $productoCodigo->getIdProducto() !== $producto->getIdProducto()) {
            throw new \InvalidArgumentException('Ya existe otro producto con el mismo código');
        }
        
        return $this->productoRepository->update($producto);
    }

    public function deleteProducto(int $id): bool {
        // Verificar que el producto existe
        $existingProducto = $this->productoRepository->findById($id);
        if ($existingProducto === null) {
            throw new \InvalidArgumentException('El producto no existe');
        }
        
        return $this->productoRepository->delete($id);
    }

    public function activateProducto(int $id): bool {
        // Verificar que el producto existe
        $existingProducto = $this->productoRepository->findById($id);
        if ($existingProducto === null) {
            throw new \InvalidArgumentException('El producto no existe');
        }
        
        return $this->productoRepository->activate($id);
    }

    public function deactivateProducto(int $id): bool {
        // Verificar que el producto existe
        $existingProducto = $this->productoRepository->findById($id);
        if ($existingProducto === null) {
            throw new \InvalidArgumentException('El producto no existe');
        }
        
        return $this->productoRepository->deactivate($id);
    }
    
    private function validateProducto(Producto $producto): void {
        if (empty($producto->getCodigo())) {
            throw new \InvalidArgumentException('El código del producto no puede estar vacío');
        }
        
        if (empty($producto->getNombre())) {
            throw new \InvalidArgumentException('El nombre del producto no puede estar vacío');
        }
        
        if (empty($producto->getUnidadMedida())) {
            throw new \InvalidArgumentException('La unidad de medida del producto no puede estar vacía');
        }
    }
}
