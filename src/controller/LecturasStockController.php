<?php

namespace controller;

use Exception;
use model\service\LecturasStockService;

require_once(__DIR__ . '/../model/service/LecturasStockService.php');

class LecturasStockController
{
    private LecturasStockService $lecturasStockService;

    public function __construct()
    {
        $this->lecturasStockService = new LecturasStockService();
    }

    public function index(): array
    {
        try {
            return ['error' => false, 'lecturas' => $this->lecturasStockService->getAllLecturas()];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de lectura inválido'];
            }
            
            $lectura = $this->lecturasStockService->getLecturaById($id);
            if ($lectura) {
                return ['error' => false, 'lectura' => $lectura];
            } else {
                return ['error' => true, 'mensaje' => 'Lectura no encontrada'];
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
                'lecturas' => $this->lecturasStockService->getLecturasByProducto($idProducto)
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
                'lecturas' => $this->lecturasStockService->getLecturasByBotiquin($idBotiquin)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByRegistrador($idUsuario): array
    {
        try {
            if (!is_numeric($idUsuario) || $idUsuario <= 0) {
                return ['error' => true, 'mensaje' => 'ID de usuario inválido'];
            }
            
            return [
                'error' => false, 
                'lecturas' => $this->lecturasStockService->getLecturasByRegistrador($idUsuario)
            ];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($idProducto, $idBotiquin, $cantidadDisponible, $fechaLectura, $registradoPor): array
    {
        try {
            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }
            
            if (!is_numeric($idBotiquin) || $idBotiquin <= 0) {
                return ['error' => true, 'mensaje' => 'ID de botiquín inválido'];
            }
            
            if (!is_numeric($cantidadDisponible) || $cantidadDisponible < 0) {
                return ['error' => true, 'mensaje' => 'Cantidad disponible inválida'];
            }

            if (empty($fechaLectura)) {
                $fechaLectura = date('Y-m-d H:i:s'); // Usar fecha actual si no se proporciona
            }
            
            if (!is_numeric($registradoPor) || $registradoPor <= 0) {
                return ['error' => true, 'mensaje' => 'ID de usuario registrador inválido'];
            }

            $resultado = $this->lecturasStockService->createLectura(
                $idProducto,
                $idBotiquin,
                $cantidadDisponible,
                $fechaLectura,
                $registradoPor
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Lectura de stock creada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear la lectura de stock'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($idLectura, $idProducto, $idBotiquin, $cantidadDisponible, $fechaLectura, $registradoPor): array
    {
        try {
            if (!is_numeric($idLectura) || $idLectura <= 0) {
                return ['error' => true, 'mensaje' => 'ID de lectura inválido'];
            }
            
            if (!is_numeric($idProducto) || $idProducto <= 0) {
                return ['error' => true, 'mensaje' => 'ID de producto inválido'];
            }
            
            if (!is_numeric($idBotiquin) || $idBotiquin <= 0) {
                return ['error' => true, 'mensaje' => 'ID de botiquín inválido'];
            }
            
            if (!is_numeric($cantidadDisponible) || $cantidadDisponible < 0) {
                return ['error' => true, 'mensaje' => 'Cantidad disponible inválida'];
            }
            
            if (empty($fechaLectura)) {
                return ['error' => true, 'mensaje' => 'Fecha de lectura inválida'];
            }
            
            if (!is_numeric($registradoPor) || $registradoPor <= 0) {
                return ['error' => true, 'mensaje' => 'ID de usuario registrador inválido'];
            }

            $resultado = $this->lecturasStockService->updateLectura(
                $idLectura,
                $idProducto,
                $idBotiquin,
                $cantidadDisponible,
                $fechaLectura,
                $registradoPor
            );
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Lectura de stock actualizada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar la lectura de stock'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($idLectura): array
    {
        try {
            if (!is_numeric($idLectura) || $idLectura <= 0) {
                return ['error' => true, 'mensaje' => 'ID de lectura inválido'];
            }

            $resultado = $this->lecturasStockService->deleteLectura($idLectura);
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Lectura de stock eliminada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar la lectura de stock'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
