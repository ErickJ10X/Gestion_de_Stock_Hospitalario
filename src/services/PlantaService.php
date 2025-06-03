<?php

namespace services;

use Models\Planta;
use Repositories\Interfaces\PlantaRepositoryInterface;
use Repositories\Interfaces\HospitalRepositoryInterface;
use Services\Interfaces\PlantaServiceInterface;

class PlantaService implements PlantaServiceInterface {
    private $plantaRepository;
    private $hospitalRepository;

    public function __construct(
        PlantaRepositoryInterface $plantaRepository,
        HospitalRepositoryInterface $hospitalRepository
    ) {
        $this->plantaRepository = $plantaRepository;
        $this->hospitalRepository = $hospitalRepository;
    }

    public function getAllPlantas(): array {
        return $this->plantaRepository->findAll();
    }

    public function getPlantaById(int $id): ?Planta {
        return $this->plantaRepository->findById($id);
    }

    public function getPlantasByHospital(int $idHospital): array {
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->findById($idHospital);
        if ($hospital === null) {
            throw new \InvalidArgumentException('El hospital no existe');
        }
        
        return $this->plantaRepository->findByHospital($idHospital);
    }

    public function getActivePlantas(): array {
        return $this->plantaRepository->findActive();
    }

    public function getActivePlantasByHospital(int $idHospital): array {
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->findById($idHospital);
        if ($hospital === null) {
            throw new \InvalidArgumentException('El hospital no existe');
        }
        
        return $this->plantaRepository->findActiveByHospital($idHospital);
    }

    public function createPlanta(Planta $planta): Planta {
        // Validaciones básicas
        if (empty($planta->getNombre())) {
            throw new \InvalidArgumentException('El nombre de la planta no puede estar vacío');
        }
        
        if ($planta->getIdHospital() === null) {
            throw new \InvalidArgumentException('La planta debe estar asociada a un hospital');
        }
        
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->findById($planta->getIdHospital());
        if ($hospital === null) {
            throw new \InvalidArgumentException('El hospital asociado no existe');
        }
        
        return $this->plantaRepository->save($planta);
    }

    public function updatePlanta(Planta $planta): bool {
        // Validaciones básicas
        if (empty($planta->getNombre())) {
            throw new \InvalidArgumentException('El nombre de la planta no puede estar vacío');
        }
        
        if ($planta->getIdPlanta() === null) {
            throw new \InvalidArgumentException('No se puede actualizar una planta sin ID');
        }
        
        if ($planta->getIdHospital() === null) {
            throw new \InvalidArgumentException('La planta debe estar asociada a un hospital');
        }
        
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->findById($planta->getIdHospital());
        if ($hospital === null) {
            throw new \InvalidArgumentException('El hospital asociado no existe');
        }
        
        // Verificar que la planta existe
        $existingPlanta = $this->plantaRepository->findById($planta->getIdPlanta());
        if ($existingPlanta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->plantaRepository->update($planta);
    }

    public function deletePlanta(int $id): bool {
        // Verificar que la planta existe
        $existingPlanta = $this->plantaRepository->findById($id);
        if ($existingPlanta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->plantaRepository->delete($id);
    }

    public function activatePlanta(int $id): bool {
        // Verificar que la planta existe
        $existingPlanta = $this->plantaRepository->findById($id);
        if ($existingPlanta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->plantaRepository->activate($id);
    }

    public function deactivatePlanta(int $id): bool {
        // Verificar que la planta existe
        $existingPlanta = $this->plantaRepository->findById($id);
        if ($existingPlanta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->plantaRepository->deactivate($id);
    }
}
