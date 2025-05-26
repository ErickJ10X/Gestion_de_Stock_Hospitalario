<?php

namespace controller;

use Exception;
use model\service\PactosService;

require_once(__DIR__ . '/../model/service/PactosService.php');

class PactosController
{
    private PactosService $pactosService;

    public function __construct()
    {
        $this->pactosService = new PactosService();
    }

    public function index(): array
    {
        try {
            return ['error' => false, 'pactos' => $this->pactosService->getAllPactos()];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de pacto inválido'];
            }
            
            $pacto = $this->pactosService->getPactoById($id);
            if ($pacto) {
                return ['error' => false, 'pacto' => $pacto];
            } else {
                return ['error' => true, 'mensaje' => 'Pacto no encontrado'];
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
                'pactos' => $this->pactosService->getPactosByProducto($idProducto)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByTipoUbicacion($tipoUbicacion): array
    {
        try {
            if (empty(trim($tipoUbicacion))) {
                return ['error' => true, 'mensaje' => 'Tipo de ubicación inválido'];
            }
            
            return [
                'error' => false, 
                'pactos' => $this->pactosService->getPactosByTipoUbicacion($tipoUbicacion)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByDestino($idDestino): array
    {
        try {
            if (!is_numeric($idDestino) || $idDestino <= 0) {
                return ['error' => true, 'mensaje' => 'ID de destino inválido'];
            }
            
            return [
                'error' => false, 
                'pactos' => $this->pactosService->getPactosByDestino($idDestino)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByCantidadPactada($cantidadPactada): array
    {
        try {
            if (!is_numeric($cantidadPactada) || $cantidadPactada < 0) {
                return ['error' => true, 'mensaje' => 'Cantidad pactada inválida'];
            }
            
            return [
                'error' => false, 
                'pactos' => $this->pactosService->getPactosByCantidadPactada($cantidadPactada)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($idProducto, $tipoUbicacion, $idDestino, $cantidadPactada): array
    {
        try {
            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }
            
            if (empty(trim($tipoUbicacion))) {
                return ['error' => true, 'mensaje' => 'Tipo de ubicación inválido'];
            }
            
            if (!is_numeric($idDestino) || $idDestino <= 0) {
                return ['error' => true, 'mensaje' => 'ID de destino inválido'];
            }
            
            if (!is_numeric($cantidadPactada) || $cantidadPactada < 0) {
                return ['error' => true, 'mensaje' => 'Cantidad pactada inválida'];
            }

            $resultado = $this->pactosService->createPacto(
                $idProducto,
                $tipoUbicacion,
                $idDestino,
                $cantidadPactada
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Pacto creado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear el pacto'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($idPacto, $idProducto, $tipoUbicacion, $idDestino, $cantidadPactada): array
    {
        try {
            if (!is_numeric($idPacto) || $idPacto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de pacto inválido'];
            }
            
            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }
            
            if (empty(trim($tipoUbicacion))) {
                return ['error' => true, 'mensaje' => 'Tipo de ubicación inválido'];
            }
            
            if (!is_numeric($idDestino) || $idDestino <= 0) {
                return ['error' => true, 'mensaje' => 'ID de destino inválido'];
            }
            
            if (!is_numeric($cantidadPactada) || $cantidadPactada < 0) {
                return ['error' => true, 'mensaje' => 'Cantidad pactada inválida'];
            }

            $resultado = $this->pactosService->updatePacto(
                $idPacto,
                $idProducto,
                $tipoUbicacion,
                $idDestino,
                $cantidadPactada
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Pacto actualizado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar el pacto'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($idPacto): array
    {
        try {
            if (!is_numeric($idPacto) || $idPacto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de pacto inválido'];
            }

            $resultado = $this->pactosService->deletePacto($idPacto);
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Pacto eliminado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar el pacto'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
