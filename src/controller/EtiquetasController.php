<?php

namespace controller;

use Exception;
use model\service\EtiquetasService;

require_once(__DIR__ . '/../model/service/EtiquetasService.php');

class EtiquetasController
{
    private EtiquetasService $etiquetasService;

    public function __construct()
    {
        $this->etiquetasService = new EtiquetasService();
    }

    public function index(): array
    {
        try {
            return ['error' => false, 'etiquetas' => $this->etiquetasService->getAllEtiquetas()];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de etiqueta inválido'];
            }
            
            $etiqueta = $this->etiquetasService->getEtiquetaById($id);
            if ($etiqueta) {
                return ['error' => false, 'etiqueta' => $etiqueta];
            } else {
                return ['error' => true, 'mensaje' => 'Etiqueta no encontrada'];
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
                'etiquetas' => $this->etiquetasService->getEtiquetasByProducto($idProducto)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByReposicion($idReposicion): array
    {
        try {
            if (!is_numeric($idReposicion) || $idReposicion <= 0) {
                return ['error' => true, 'mensaje' => 'ID de reposición inválido'];
            }
            
            return [
                'error' => false, 
                'etiquetas' => $this->etiquetasService->getEtiquetasByReposicion($idReposicion)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByTipo($tipo): array
    {
        try {
            if (empty(trim($tipo))) {
                return ['error' => true, 'mensaje' => 'El tipo de etiqueta es requerido'];
            }
            
            return [
                'error' => false, 
                'etiquetas' => $this->etiquetasService->getEtiquetasByTipo($tipo)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByPrioridad($prioridad): array
    {
        try {
            if (empty(trim($prioridad))) {
                return ['error' => true, 'mensaje' => 'La prioridad de la etiqueta es requerida'];
            }
            
            return [
                'error' => false, 
                'etiquetas' => $this->etiquetasService->getEtiquetasByPrioridad($prioridad)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByImpresa($impresa): array
    {
        try {
            $impresaValue = filter_var($impresa, FILTER_VALIDATE_BOOLEAN);
            
            return [
                'error' => false, 
                'etiquetas' => $this->etiquetasService->getEtiquetasByImpresa($impresaValue)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($idProducto, $idReposicion, $tipo, $prioridad, $impresa = false): array
    {
        try {
            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }

            if (!is_numeric($idReposicion) || $idReposicion <= 0) {
                return ['error' => true, 'mensaje' => 'ID de reposición inválido'];
            }

            if (empty(trim($tipo))) {
                return ['error' => true, 'mensaje' => 'El tipo de etiqueta es requerido'];
            }

            if (empty(trim($prioridad))) {
                return ['error' => true, 'mensaje' => 'La prioridad de la etiqueta es requerida'];
            }

            $impresaValue = filter_var($impresa, FILTER_VALIDATE_BOOLEAN);
            
            $resultado = $this->etiquetasService->createEtiqueta(
                $idProducto,
                $idReposicion,
                $tipo,
                $prioridad,
                $impresaValue
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Etiqueta creada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear la etiqueta'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($idEtiqueta, $idProducto, $idReposicion, $tipo, $prioridad, $impresa): array
    {
        try {
            if (!is_numeric($idEtiqueta) || $idEtiqueta <= 0) {
                return ['error' => true, 'mensaje' => 'ID de etiqueta inválido'];
            }

            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }

            if (!is_numeric($idReposicion) || $idReposicion <= 0) {
                return ['error' => true, 'mensaje' => 'ID de reposición inválido'];
            }

            if (empty(trim($tipo))) {
                return ['error' => true, 'mensaje' => 'El tipo de etiqueta es requerido'];
            }

            if (empty(trim($prioridad))) {
                return ['error' => true, 'mensaje' => 'La prioridad de la etiqueta es requerida'];
            }

            $impresaValue = filter_var($impresa, FILTER_VALIDATE_BOOLEAN);
            
            $resultado = $this->etiquetasService->updateEtiqueta(
                $idEtiqueta,
                $idProducto,
                $idReposicion,
                $tipo,
                $prioridad,
                $impresaValue
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Etiqueta actualizada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar la etiqueta'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function marcarComoImpresa($idEtiqueta, $impresa = true): array
    {
        try {
            if (!is_numeric($idEtiqueta) || $idEtiqueta <= 0) {
                return ['error' => true, 'mensaje' => 'ID de etiqueta inválido'];
            }

            $impresaValue = filter_var($impresa, FILTER_VALIDATE_BOOLEAN);
            
            $resultado = $this->etiquetasService->marcarEtiquetaImpresa($idEtiqueta, $impresaValue);
            
            if ($resultado) {
                $estado = $impresaValue ? 'impresa' : 'no impresa';
                return ['error' => false, 'mensaje' => "Etiqueta marcada como $estado correctamente"];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar el estado de la etiqueta'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($idEtiqueta): array
    {
        try {
            if (!is_numeric($idEtiqueta) || $idEtiqueta <= 0) {
                return ['error' => true, 'mensaje' => 'ID de etiqueta inválido'];
            }

            $resultado = $this->etiquetasService->deleteEtiqueta($idEtiqueta);
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Etiqueta eliminada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar la etiqueta'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
