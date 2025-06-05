<?php

namespace model\service;

use model\entity\CatalogoProducto;
use model\repository\CatalogoProductoRepository;
use InvalidArgumentException;

class CatalogoProductoService {
    private CatalogoProductoRepository $catalogoRepository;

    public function __construct(CatalogoProductoRepository $catalogoRepository = null) {
        $this->catalogoRepository = $catalogoRepository ?? new CatalogoProductoRepository();
    }

    public function getCatalogoById(int $id): ?CatalogoProducto {
        return $this->catalogoRepository->findById($id);
    }

    public function getAllCatalogos(): array {
        return $this->catalogoRepository->findAll();
    }

    public function getCatalogosByPlanta(int $idPlanta): array {
        return $this->catalogoRepository->findByPlanta($idPlanta);
    }

    public function getCatalogosByProducto(int $idProducto): array {
        return $this->catalogoRepository->findByProducto($idProducto);
    }

    public function createCatalogo(array $data): CatalogoProducto {
        $this->validateCatalogoData($data);
        
        $catalogo = new CatalogoProducto(
            null,
            $data['id_producto'],
            $data['id_planta'],
            $data['activo'] ?? true
        );
        
        return $this->catalogoRepository->save($catalogo);
    }

    public function updateCatalogo(int $id, array $data): CatalogoProducto {
        $catalogo = $this->catalogoRepository->findById($id);
        if (!$catalogo) {
            throw new InvalidArgumentException('Catálogo no encontrado');
        }
        
        if (isset($data['id_producto'])) {
            $catalogo->setIdProducto($data['id_producto']);
        }
        
        if (isset($data['id_planta'])) {
            $catalogo->setIdPlanta($data['id_planta']);
        }
        
        if (isset($data['activo'])) {
            $catalogo->setActivo($data['activo']);
        }
        
        return $this->catalogoRepository->save($catalogo);
    }

    public function deleteCatalogo(int $id): bool {
        return $this->catalogoRepository->delete($id);
    }

    public function desactivarCatalogo(int $id): bool {
        return $this->catalogoRepository->softDelete($id);
    }

    public function verificarProductoEnCatalogo(int $idProducto, int $idPlanta): bool {
        $catalogos = $this->catalogoRepository->findByPlanta($idPlanta);
        foreach ($catalogos as $catalogo) {
            if ($catalogo->getIdProducto() == $idProducto && $catalogo->isActivo()) {
                return true;
            }
        }
        return false;
    }

    private function validateCatalogoData(array $data): void {
        if (!isset($data['id_producto'])) {
            throw new InvalidArgumentException('El producto es obligatorio');
        }
        
        if (!isset($data['id_planta'])) {
            throw new InvalidArgumentException('La planta es obligatoria');
        }
    }
}
