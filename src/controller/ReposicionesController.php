<?php

namespace controller;

use Exception;
use model\service\ReposicionesService;

require_once(__DIR__ . '/../model/service/ReposicionesService.php');

class ReposicionesController
{
    private ReposicionesService $reposicionesService;

    public function __construct()
    {
        $this->reposicionesService = new ReposicionesService();
    }

    public function index(): array
    {
        try {
            return ['error' => false, 'reposiciones' => $this->reposicionesService->getAllReposiciones()];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de reposición inválido'];
            }
            
            $reposicion = $this->reposicionesService->getReposicionById($id);
            if ($reposicion) {
                return ['error' => false, 'reposicion' => $reposicion];
            } else {
                return ['error' => true, 'mensaje' => 'Reposición no encontrada'];
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
                'reposiciones' => $this->reposicionesService->getReposicionesByProducto($idProducto)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByBotiquin($idBotiquin): array
    {
        try {
            if (!is_numeric($idBotiquin) || $idBotiquin <= 0) {
                return ['error' => true, 'mensaje' => 'ID de botiquín inválido'];
            }
            
            return [
                'error' => false, 
                'reposiciones' => $this->reposicionesService->getReposicionesByBotiquin($idBotiquin)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByAlmacen($idAlmacen): array
    {
        try {
            if (!is_numeric($idAlmacen) || $idAlmacen <= 0) {
                return ['error' => true, 'mensaje' => 'ID de almacén inválido'];
            }
            
            return [
                'error' => false, 
                'reposiciones' => $this->reposicionesService->getReposicionesByAlmacen($idAlmacen)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByUrgente($urgente = true): array
    {
        try {
            $urgenteValue = filter_var($urgente, FILTER_VALIDATE_BOOLEAN);
            
            return [
                'error' => false, 
                'reposiciones' => $this->reposicionesService->getReposicionesByUrgente($urgenteValue)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($idProducto, $desdeAlmacen, $hastaBotiquin, $cantidadRepuesta, $fecha = null, $urgente = false): array
    {
        try {
            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }

            if (!is_numeric($desdeAlmacen) || $desdeAlmacen <= 0) {
                return ['error' => true, 'mensaje' => 'ID de almacén origen inválido'];
            }

            if (!is_numeric($hastaBotiquin) || $hastaBotiquin <= 0) {
                return ['error' => true, 'mensaje' => 'ID de botiquín destino inválido'];
            }

            if (!is_numeric($cantidadRepuesta) || $cantidadRepuesta <= 0) {
                return ['error' => true, 'mensaje' => 'Cantidad repuesta inválida'];
            }

            if ($fecha === null) {
                $fecha = date('Y-m-d H:i:s'); // Usar fecha actual si no se proporciona
            }

            $urgenteValue = filter_var($urgente, FILTER_VALIDATE_BOOLEAN);

            $resultado = $this->reposicionesService->createReposicion(
                $idProducto,
                $desdeAlmacen,
                $hastaBotiquin,
                $cantidadRepuesta,
                $fecha,
                $urgenteValue
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Reposición creada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear la reposición'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($idReposicion, $idProducto, $desdeAlmacen, $hastaBotiquin, $cantidadRepuesta, $fecha, $urgente): array
    {
        try {
            if (!is_numeric($idReposicion) || $idReposicion <= 0) {
                return ['error' => true, 'mensaje' => 'ID de reposición inválido'];
            }

            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }

            if (!is_numeric($desdeAlmacen) || $desdeAlmacen <= 0) {
                return ['error' => true, 'mensaje' => 'ID de almacén origen inválido'];
            }

            if (!is_numeric($hastaBotiquin) || $hastaBotiquin <= 0) {
                return ['error' => true, 'mensaje' => 'ID de botiquín destino inválido'];
            }

            if (!is_numeric($cantidadRepuesta) || $cantidadRepuesta <= 0) {
                return ['error' => true, 'mensaje' => 'Cantidad repuesta inválida'];
            }

            if (empty($fecha)) {
                return ['error' => true, 'mensaje' => 'Fecha de reposición inválida'];
            }

            $urgenteValue = filter_var($urgente, FILTER_VALIDATE_BOOLEAN);

            $resultado = $this->reposicionesService->updateReposicion(
                $idReposicion,
                $idProducto,
                $desdeAlmacen,
                $hastaBotiquin,
                $cantidadRepuesta,
                $fecha,
                $urgenteValue
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Reposición actualizada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar la reposición'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($idReposicion): array
    {
        try {
            if (!is_numeric($idReposicion) || $idReposicion <= 0) {
                return ['error' => true, 'mensaje' => 'ID de reposición inválido'];
            }
            
            $resultado = $this->reposicionesService->deleteReposicion($idReposicion);
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Reposición eliminada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar la reposición'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
