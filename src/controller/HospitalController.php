<?php

namespace controller;

use Exception;
use model\service\HospitalService;

require_once(__DIR__ . '/../model/service/HospitalService.php');

class HospitalController
{
    private HospitalService $hospitalService;

    public function __construct()
    {
        $this->hospitalService = new HospitalService();
    }

    public function index(): array
    {
        try {
            return ['error' => false, 'hospitales' => $this->hospitalService->getAllHospitales()];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }
            
            $hospital = $this->hospitalService->getHospitalById($id);
            if ($hospital) {
                return ['error' => false, 'hospital' => $hospital];
            } else {
                return ['error' => true, 'mensaje' => 'Hospital no encontrado'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($nombre, $ubicacion = ''): array
    {
        try {
            if (empty(trim($nombre))) {
                return ['error' => true, 'mensaje' => 'El nombre del hospital es obligatorio'];
            }

            $resultado = $this->hospitalService->createHospital($nombre, $ubicacion);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Hospital creado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear el hospital'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($id, $nombre, $ubicacion = null): array
    {
        try {
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }

            if (empty(trim($nombre))) {
                return ['error' => true, 'mensaje' => 'El nombre del hospital es obligatorio'];
            }

            $resultado = $this->hospitalService->updateHospital($id, $nombre, $ubicacion);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Hospital actualizado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar el hospital'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($id): array
    {
        try {
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }

            $resultado = $this->hospitalService->deleteHospital($id);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Hospital eliminado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar el hospital'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
