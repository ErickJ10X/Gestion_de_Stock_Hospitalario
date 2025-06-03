<?php

namespace services;

use Models\Hospital;
use Repositories\Interfaces\HospitalRepositoryInterface;
use Services\Interfaces\HospitalServiceInterface;

class HospitalService implements HospitalServiceInterface {
    private $hospitalRepository;

    public function __construct(HospitalRepositoryInterface $hospitalRepository) {
        $this->hospitalRepository = $hospitalRepository;
    }

    public function getAllHospitales(): array {
        return $this->hospitalRepository->findAll();
    }

    public function getHospitalById(int $id): ?Hospital {
        return $this->hospitalRepository->findById($id);
    }

    public function getActiveHospitales(): array {
        return $this->hospitalRepository->findActive();
    }

    public function createHospital(Hospital $hospital): Hospital {
        // Validaciones básicas
        if (empty($hospital->getNombre())) {
            throw new \InvalidArgumentException('El nombre del hospital no puede estar vacío');
        }
        
        return $this->hospitalRepository->save($hospital);
    }

    public function updateHospital(Hospital $hospital): bool {
        // Validaciones básicas
        if (empty($hospital->getNombre())) {
            throw new \InvalidArgumentException('El nombre del hospital no puede estar vacío');
        }
        
        if ($hospital->getIdHospital() === null) {
            throw new \InvalidArgumentException('No se puede actualizar un hospital sin ID');
        }
        
        // Verificar que el hospital existe
        $existingHospital = $this->hospitalRepository->findById($hospital->getIdHospital());
        if ($existingHospital === null) {
            throw new \InvalidArgumentException('El hospital no existe');
        }
        
        return $this->hospitalRepository->update($hospital);
    }

    public function deleteHospital(int $id): bool {
        // Verificar que el hospital existe
        $existingHospital = $this->hospitalRepository->findById($id);
        if ($existingHospital === null) {
            throw new \InvalidArgumentException('El hospital no existe');
        }
        
        return $this->hospitalRepository->delete($id);
    }

    public function activateHospital(int $id): bool {
        // Verificar que el hospital existe
        $existingHospital = $this->hospitalRepository->findById($id);
        if ($existingHospital === null) {
            throw new \InvalidArgumentException('El hospital no existe');
        }
        
        return $this->hospitalRepository->activate($id);
    }

    public function deactivateHospital(int $id): bool {
        // Verificar que el hospital existe
        $existingHospital = $this->hospitalRepository->findById($id);
        if ($existingHospital === null) {
            throw new \InvalidArgumentException('El hospital no existe');
        }
        
        return $this->hospitalRepository->deactivate($id);
    }
}
