<?php

namespace model\service;

require_once(__DIR__ . '/../../model/entity/Hospital.php');
require_once(__DIR__ . '/../../model/repository/HospitalRepository.php');

use model\entity\Hospital;
use InvalidArgumentException;
use model\repository\HospitalRepository;

class HospitalService {
    private HospitalRepository $hospitalRepository;

    public function __construct() {
        $this->hospitalRepository = new HospitalRepository();
    }

    public function getHospitalById(int $id): ?Hospital {
        return $this->hospitalRepository->findById($id);
    }

    public function getAllHospitales(): array {
        return $this->hospitalRepository->findAll();
    }

    public function getActiveHospitales(): array {
        return $this->hospitalRepository->findActive();
    }

    public function createHospital(array $data): Hospital {
        $this->validateHospitalData($data);
        
        $hospital = new Hospital(
            null,
            $data['nombre'],
            $data['ubicacion'] ?? '',
            $data['activo'] ?? true
        );
        
        return $this->hospitalRepository->save($hospital);
    }

    public function updateHospital(int $id, array $data): Hospital {
        $hospital = $this->hospitalRepository->findById($id);
        if (!$hospital) {
            throw new InvalidArgumentException('Hospital no encontrado');
        }
        
        if (isset($data['nombre'])) {
            $hospital->setNombre($data['nombre']);
        }
        
        if (isset($data['ubicacion'])) {
            $hospital->setUbicacion($data['ubicacion']);
        }
        
        if (isset($data['activo'])) {
            $hospital->setActivo($data['activo']);
        }
        
        return $this->hospitalRepository->save($hospital);
    }

    public function deleteHospital(int $id): bool {
        // En un sistema real, podría ser necesario comprobar si hay dependencias antes de eliminar
        return $this->hospitalRepository->delete($id);
    }

    public function desactivarHospital(int $id): bool {
        return $this->hospitalRepository->softDelete($id);
    }

    private function validateHospitalData(array $data): void {
        if (!isset($data['nombre']) || empty($data['nombre'])) {
            throw new InvalidArgumentException('El nombre del hospital es obligatorio');
        }
    }
}
