<?php

namespace controller;

use Exception;
use model\service\AlmacenesService;

require_once(__DIR__ . '/../model/service/AlmacenesService.php');

class AlmacenesController
{
    private AlmacenesService $almacenesService;

    public function __construct()
    {
        $this->almacenesService = new AlmacenesService();
    }

    public function index(): array
    {
        try {
            return $this->almacenesService->getAllAlmacenes();
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByPlanta($plantaId): array
    {
        try {
            if (!is_numeric($plantaId) || $plantaId <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }
            
            return $this->almacenesService->getAlmacenesByPlanta($plantaId);
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de almacén inválido'];
            }
            
            $almacen = $this->almacenesService->getAlmacenById($id);
            if ($almacen) {
                return ['error' => false, 'almacen' => $almacen];
            } else {
                return ['error' => true, 'mensaje' => 'Almacén no encontrado'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
    
    /**
     * Crea un nuevo almacén
     * @param string $tipo Tipo del almacén
     * @param int $plantaId ID de la planta a la que pertenece
     * @param int $hospitalId ID del hospital al que pertenece
     * @return array Resultado de la operación
     */
    public function store($tipo, $plantaId, $hospitalId): array
    {
        try {
            if (empty(trim($tipo))) {
                return ['error' => true, 'mensaje' => 'El tipo de almacén es obligatorio'];
            }

            if (!is_numeric($plantaId) || $plantaId <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }
            
            if (!is_numeric($hospitalId) || $hospitalId <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }

            $resultado = $this->almacenesService->createAlmacen($tipo, $plantaId, $hospitalId);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Almacén creado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear el almacén'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
    
    /**
     * Actualiza un almacén existente
     * @param int $id ID del almacén
     * @param string $tipo Nuevo tipo del almacén
     * @param int $plantaId Nuevo ID de la planta
     * @param int $hospitalId Nuevo ID del hospital
     * @return array Resultado de la operación
     */
    public function update($id, $tipo, $plantaId, $hospitalId): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de almacén inválido'];
            }

            if (empty(trim($tipo))) {
                return ['error' => true, 'mensaje' => 'El tipo de almacén es obligatorio'];
            }

            if (!is_numeric($plantaId) || $plantaId <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }
            
            if (!is_numeric($hospitalId) || $hospitalId <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }

            $resultado = $this->almacenesService->updateAlmacen($id, $tipo, $plantaId, $hospitalId);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Almacén actualizado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar el almacén'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
    
    /**
     * Elimina un almacén
     * @param int $id ID del almacén a eliminar
     * @return array Resultado de la operación
     */
    public function destroy($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de almacén inválido'];
            }

            $resultado = $this->almacenesService->deleteAlmacen($id);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Almacén eliminado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar el almacén'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
