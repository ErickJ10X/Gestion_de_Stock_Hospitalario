<?php

namespace model\service;

require_once(__DIR__ . '/../../model/entity/Producto.php');
require_once(__DIR__ . '/../../model/repository/ProductoRepository.php');

use model\entity\Producto;
use model\repository\ProductoRepository;
use InvalidArgumentException;

class ProductoService {
    private ProductoRepository $productoRepository;

    public function __construct(ProductoRepository $productoRepository = null) {
        $this->productoRepository = $productoRepository ?? new ProductoRepository();
    }

    public function getProductoById(int $id): ?Producto {
        return $this->productoRepository->findById($id);
    }

    public function getProductoByCodigo(string $codigo): ?Producto {
        return $this->productoRepository->findByCodigo($codigo);
    }

    public function getAllProductos(): array {
        return $this->productoRepository->findAll();
    }

    public function getActiveProductos(): array {
        return $this->productoRepository->findActive();
    }

    public function getProductosByCatalogo(int $idPlanta): array {
        return $this->productoRepository->findByCatalogo($idPlanta);
    }

    public function buscarProductos(string $termino): array {
        return $this->productoRepository->buscarPorNombreOCodigo($termino);
    }

    public function createProducto(array $data): Producto {
        $this->validateProductoData($data);
        $this->checkCodigoUnique($data['codigo']);
        
        $producto = new Producto(
            null,
            $data['codigo'],
            $data['nombre'],
            $data['descripcion'] ?? '',
            $data['unidad_medida'],
            $data['activo'] ?? true
        );
        
        return $this->productoRepository->save($producto);
    }

    public function updateProducto(int $id, array $data): Producto {
        $producto = $this->productoRepository->findById($id);
        if (!$producto) {
            throw new InvalidArgumentException('Producto no encontrado');
        }
        
        if (isset($data['codigo']) && $data['codigo'] !== $producto->getCodigo()) {
            $this->checkCodigoUnique($data['codigo']);
            $producto->setCodigo($data['codigo']);
        }
        
        if (isset($data['nombre'])) {
            $producto->setNombre($data['nombre']);
        }
        
        if (isset($data['descripcion'])) {
            $producto->setDescripcion($data['descripcion']);
        }
        
        if (isset($data['unidad_medida'])) {
            $producto->setUnidadMedida($data['unidad_medida']);
        }
        
        if (isset($data['activo'])) {
            $producto->setActivo($data['activo']);
        }
        
        return $this->productoRepository->save($producto);
    }

    public function deleteProducto(int $id): bool {
        // Aquí se podrían agregar validaciones adicionales antes de eliminar
        return $this->productoRepository->delete($id);
    }

    public function desactivarProducto(int $id): bool {
        return $this->productoRepository->softDelete($id);
    }

    private function validateProductoData(array $data): void {
        if (!isset($data['codigo']) || empty($data['codigo'])) {
            throw new InvalidArgumentException('El código es obligatorio');
        }
        
        if (!isset($data['nombre']) || empty($data['nombre'])) {
            throw new InvalidArgumentException('El nombre es obligatorio');
        }
        
        if (!isset($data['unidad_medida']) || empty($data['unidad_medida'])) {
            throw new InvalidArgumentException('La unidad de medida es obligatoria');
        }
    }

    private function checkCodigoUnique(string $codigo, ?int $id = null): void {
        $producto = $this->productoRepository->findByCodigo($codigo);
        if ($producto && ($id === null || $producto->getIdProducto() !== $id)) {
            throw new InvalidArgumentException('El código ya está en uso');
        }
    }
}
