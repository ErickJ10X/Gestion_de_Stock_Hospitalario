<?php

namespace controller;

use Exception;
use model\service\CatalogosService;

require_once(__DIR__ . '/../model/service/CatalogosService.php');

class CatalogosController
{
    private CatalogosService $catalogosService;

    public function __construct()
    {
        $this->catalogosService = new CatalogosService();
    }

    public function index(): array
    {
        try {
            return ['error' => false, 'catalogos' => $this->catalogosService->getAllCatalogos()];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            $catalogo = $this->catalogosService->getCatalogoById($id);
            if ($catalogo) {
                return ['error' => false, 'catalogo' => $catalogo];
            } else {
                return ['error' => true, 'mensaje' => 'Catálogo no encontrado'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByProducto($idProducto): array
    {
        try {
            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }
            
            return [
                'error' => false, 
                'catalogos' => $this->catalogosService->getCatalogosByProducto($idProducto)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByPlanta($idPlanta): array
    {
        try {
            if (!is_numeric($idPlanta) || $idPlanta <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }
            
            return [
                'error' => false, 
                'catalogos' => $this->catalogosService->getCatalogosByPlanta($idPlanta)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($idProducto, $idPlanta): array
    {
        try {
            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }

            if (!is_numeric($idPlanta) || $idPlanta <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }

            // Verificar si ya existe el producto en el catálogo de la planta
            if ($this->catalogosService->existeProductoEnPlanta($idProducto, $idPlanta)) {
                return ['error' => true, 'mensaje' => 'El producto ya existe en el catálogo de esta planta'];
            }

            $resultado = $this->catalogosService->createCatalogo($idProducto, $idPlanta);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Catálogo creado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear el catálogo'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($idCatalogo, $idProducto, $idPlanta): array
    {
        try {
            if (!is_numeric($idCatalogo) || $idCatalogo <= 0) {
                return ['error' => true, 'mensaje' => 'ID de catálogo inválido'];
            }

            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }

            if (!is_numeric($idPlanta) || $idPlanta <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }

            $resultado = $this->catalogosService->updateCatalogo($idCatalogo, $idProducto, $idPlanta);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Catálogo actualizado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar el catálogo'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($idCatalogo): array
    {
        try {
            if (!is_numeric($idCatalogo) || $idCatalogo <= 0) {
                return ['error' => true, 'mensaje' => 'ID de catálogo inválido'];
            }

            $resultado = $this->catalogosService->deleteCatalogo($idCatalogo);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Catálogo eliminado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar el catálogo'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
