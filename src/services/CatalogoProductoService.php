<?php

namespace services;

use Models\CatalogoProducto;
use Repositories\Interfaces\CatalogoProductoRepositoryInterface;
use Repositories\Interfaces\ProductoRepositoryInterface;
use Repositories\Interfaces\PlantaRepositoryInterface;
use Services\Interfaces\CatalogoProductoServiceInterface;

class CatalogoProductoService implements CatalogoProductoServiceInterface {
    private $catalogoProductoRepository;
    private $productoRepository;
    private $plantaRepository;

    public function __construct(
        CatalogoProductoRepositoryInterface $catalogoProductoRepository,
        ProductoRepositoryInterface $productoRepository,
        PlantaRepositoryInterface $plantaRepository
    ) {
        $this->catalogoProductoRepository = $catalogoProductoRepository;
        $this->productoRepository = $productoRepository;
        $this->plantaRepository = $plantaRepository;
    }

    public function getAllCatalogoProductos(): array {
        return $this->catalogoProductoRepository->findAll();
    }

    public function getCatalogoProductoById(int $id): ?CatalogoProducto {
        return $this->catalogoProductoRepository->findById($id);
    }

    public function getCatalogoProductosByPlanta(int $idPlanta): array {
        // Verificar que la planta existe
        $planta = $this->plantaRepository->findById($idPlanta);
        if ($planta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->catalogoProductoRepository->findByPlanta($idPlanta);
    }

    public function getCatalogoProductosByProducto(int $idProducto): array {
        // Verificar que el producto existe
        $producto = $this->productoRepository->findById($idProducto);
        if ($producto === null) {
            throw new \InvalidArgumentException('El producto no existe');
        }
        
        return $this->catalogoProductoRepository->findByProducto($idProducto);
    }

    public function getCatalogoProductoByPlantaAndProducto(int $idPlanta, int $idProducto): ?CatalogoProducto {
        // Verificaciones
        $planta = $this->plantaRepository->findById($idPlanta);
        if ($planta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        $producto = $this->productoRepository->findById($idProducto);
        if ($producto === null) {
            throw new \InvalidArgumentException('El producto no existe');
        }
        
        return $this->catalogoProductoRepository->findByPlantaAndProducto($idPlanta, $idProducto);
    }

    public function getActiveCatalogoProductos(): array {
        return $this->catalogoProductoRepository->findActive();
    }

    public function getActiveCatalogoProductosByPlanta(int $idPlanta): array {
        // Verificar que la planta existe
        $planta = $this->plantaRepository->findById($idPlanta);
        if ($planta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->catalogoProductoRepository->findActiveByPlanta($idPlanta);
    }

    public function createCatalogoProducto(CatalogoProducto $catalogoProducto): CatalogoProducto {
        // Validaciones
        $this->validateCatalogoProducto($catalogoProducto);
        
        // Verificar que no exista ya esta combinación de producto y planta
        $existing = $this->catalogoProductoRepository->findByPlantaAndProducto(
            $catalogoProducto->getIdPlanta(),
            $catalogoProducto->getIdProducto()
        );
        
        if ($existing !== null) {
            throw new \InvalidArgumentException('Ya existe este producto en el catálogo de esta planta');
        }
        
        return $this->catalogoProductoRepository->save($catalogoProducto);
    }

    public function updateCatalogoProducto(CatalogoProducto $catalogoProducto): bool {
        // Validaciones
        $this->validateCatalogoProducto($catalogoProducto);
        
        if ($catalogoProducto->getIdCatalogo() === null) {
            throw new \InvalidArgumentException('No se puede actualizar un catálogo sin ID');
        }
        
        // Verificar que el catálogo existe
        $existingCatalogo = $this->catalogoProductoRepository->findById($catalogoProducto->getIdCatalogo());
        if ($existingCatalogo === null) {
            throw new \InvalidArgumentException('El catálogo no existe');
        }
        
        // Si se están cambiando planta o producto, verificar que la nueva combinación no exista ya
        if ($existingCatalogo->getIdPlanta() !== $catalogoProducto->getIdPlanta() ||
            $existingCatalogo->getIdProducto() !== $catalogoProducto->getIdProducto()) {
            
            $existing = $this->catalogoProductoRepository->findByPlantaAndProducto(
                $catalogoProducto->getIdPlanta(),
                $catalogoProducto->getIdProducto()
            );
            
            if ($existing !== null) {
                throw new \InvalidArgumentException('Ya existe este producto en el catálogo de esta planta');
            }
        }
        
        return $this->catalogoProductoRepository->update($catalogoProducto);
    }

    public function deleteCatalogoProducto(int $id): bool {
        // Verificar que el catálogo existe
        $existingCatalogo = $this->catalogoProductoRepository->findById($id);
        if ($existingCatalogo === null) {
            throw new \InvalidArgumentException('El catálogo no existe');
        }
        
        return $this->catalogoProductoRepository->delete($id);
    }

    public function activateCatalogoProducto(int $id): bool {
        // Verificar que el catálogo existe
        $existingCatalogo = $this->catalogoProductoRepository->findById($id);
        if ($existingCatalogo === null) {
            throw new \InvalidArgumentException('El catálogo no existe');
        }
        
        return $this->catalogoProductoRepository->activate($id);
    }

    public function deactivateCatalogoProducto(int $id): bool {
        // Verificar que el catálogo existe
        $existingCatalogo = $this->catalogoProductoRepository->findById($id);
        if ($existingCatalogo === null) {
            throw new \InvalidArgumentException('El catálogo no existe');
        }
        
        return $this->catalogoProductoRepository->deactivate($id);
    }
    
    private function validateCatalogoProducto(CatalogoProducto $catalogoProducto): void {
        if ($catalogoProducto->getIdProducto() === null) {
            throw new \InvalidArgumentException('El catálogo debe tener un producto asociado');
        }
        
        if ($catalogoProducto->getIdPlanta() === null) {
            throw new \InvalidArgumentException('El catálogo debe tener una planta asociada');
        }
        
        // Verificar que el producto existe
        $producto = $this->productoRepository->findById($catalogoProducto->getIdProducto());
        if ($producto === null) {
            throw new \InvalidArgumentException('El producto asociado no existe');
        }
        
        // Verificar que la planta existe
        $planta = $this->plantaRepository->findById($catalogoProducto->getIdPlanta());
        if ($planta === null) {
            throw new \InvalidArgumentException('La planta asociada no existe');
        }
    }
}
