<?php

require_once(__DIR__ . '/../model/service/HospitalService.php');
require_once(__DIR__ . '/../model/entity/Hospital.php');
include_once(__DIR__ . '/../util/session.php');

use model\service\HospitalService;
use model\entity\Hospital;

class HospitalController
{
    private HospitalService $hospitalService;
    private Session $session;

    public function __construct()
    {
        $this->hospitalService = new HospitalService();
        $this->session = new Session();
    }

    public function getAllHospitales(): array
    {
        try {
            return $this->hospitalService->getAllHospitales();
        } catch (Exception $e) {
            $this->handleError("Error al obtener hospitales", $e->getMessage());
            return [];
        }
    }

    public function getHospitalById($id): ?Hospital
    {
        try {
            if (empty($id)) {
                throw new Exception("El ID del hospital es requerido");
            }
            
            $hospital = $this->hospitalService->getHospitalById($id);
            
            if (!$hospital) {
                throw new Exception("Hospital no encontrado");
            }
            
            return $hospital;
        } catch (Exception $e) {
            $this->handleError("Error al obtener hospital", $e->getMessage());
            return null;
        }
    }

    public function createHospital($nombre): bool
    {
        try {
            if (empty($nombre)) {
                throw new Exception("El nombre del hospital es requerido");
            }
            
            if (!$this->hospitalService->createHospital($nombre)) {
                throw new Exception("No se pudo crear el hospital");
            }
            
            $this->session->setMessage("success", "Hospital creado correctamente");
            return true;
        } catch (Exception $e) {
            $this->handleError("Error al crear hospital", $e->getMessage());
            return false;
        }
    }

    public function updateHospital($id, $nombre): bool
    {
        try {
            if (empty($id) || empty($nombre)) {
                throw new Exception("El ID y nombre del hospital son requeridos");
            }
            
            $hospital = $this->hospitalService->getHospitalById($id);
            if (!$hospital) {
                throw new Exception("Hospital no encontrado");
            }
            
            if (!$this->hospitalService->updateHospital($id, $nombre)) {
                throw new Exception("No se pudo actualizar el hospital");
            }
            
            $this->session->setMessage("success", "Hospital actualizado correctamente");
            return true;
        } catch (Exception $e) {
            $this->handleError("Error al actualizar hospital", $e->getMessage());
            return false;
        }
    }

    public function deleteHospital($id): bool
    {
        try {
            if (empty($id)) {
                throw new Exception("El ID del hospital es requerido");
            }
            
            $hospital = $this->hospitalService->getHospitalById($id);
            if (!$hospital) {
                throw new Exception("Hospital no encontrado");
            }
            
            if (!$this->hospitalService->deleteHospital($id)) {
                throw new Exception("No se pudo eliminar el hospital");
            }
            
            $this->session->setMessage("success", "Hospital eliminado correctamente");
            return true;
        } catch (Exception $e) {
            $this->handleError("Error al eliminar hospital", $e->getMessage());
            return false;
        }
    }

    private function handleError($title, $message): void
    {
        $this->session->setMessage("error", $title . ": " . $message);
    }
}
