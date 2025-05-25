<?php

namespace model\service;

use Exception;
use model\entity\Catalogos_productos;
use model\repository\CatalogosRepository;
use PDOException;

require_once(__DIR__ . '/../repository/CatalogosRepository.php');
require_once(__DIR__ . '/../entity/Catalogos_productos.php');

class CatalogosService
{
    private CatalogosRepository $catalogosRepository;

    public function __construct()
    {
        $this->catalogosRepository = new CatalogosRepository();
    }

    public function getAllCatalogos(): array
    {
        try {
            return $this->catalogosRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los catálogos de productos: " . $e->getMessage());
        }
    }

    public function getCatalogoById(int $id): ?Catalogos_productos
    {
        try {
            return $this->catalogosRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el catálogo con ID {$id}: " . $e->getMessage());
        }
    }

    public function getCatalogosByProducto(int $idProducto): array
    {
        try {
            return $this->catalogosRepository->findByProducto($idProducto);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener catálogos del producto {$idProducto}: " . $e->getMessage());
        }
    }

    public function getCatalogosByPlanta(int $idPlanta): array
    {
        try {
            return $this->catalogosRepository->findByPlanta($idPlanta);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener catálogos de la planta {$idPlanta}: " . $e->getMessage());
        }
    }

    public function createCatalogo(int $idProducto, int $idPlanta): bool
    {
        try {
            $catalogo = new Catalogos_productos(0, $idProducto, $idPlanta);
            return $this->catalogosRepository->save($catalogo);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el catálogo de productos: " . $e->getMessage());
        }
    }

    public function existeProductoEnPlanta(int $idProducto, int $idPlanta): bool
    {
        try {
            $catalogosPorProducto = $this->catalogosRepository->findByProducto($idProducto);
            foreach ($catalogosPorProducto as $catalogo) {
                if ($catalogo->getIdPlanta() === $idPlanta) {
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar si existe el producto en el catálogo de la planta: " . $e->getMessage());
        }
    }

    public function updateCatalogo(int $idCatalogo, int $idProducto, int $idPlanta): bool
    {
        try {
            $catalogoExistente = $this->catalogosRepository->findById($idCatalogo);
            if (!$catalogoExistente) {
                throw new Exception("No se encontró el catálogo con ID: " . $idCatalogo);
            }
            
            $catalogo = new Catalogos_productos($idCatalogo, $idProducto, $idPlanta);
            return $this->catalogosRepository->update($catalogo);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el catálogo de productos: " . $e->getMessage());
        }
    }

    public function deleteCatalogo(int $idCatalogo): bool
    {
        try {
            $catalogo = $this->catalogosRepository->findById($idCatalogo);
            if (!$catalogo) {
                throw new Exception("No se encontró el catálogo con ID: " . $idCatalogo);
            }
            
            return $this->catalogosRepository->delete($idCatalogo);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el catálogo de productos: " . $e->getMessage());
        }
    }
}
