<?php

namespace model\service;

use model\entity\Planta;
use model\repository\PlantaRepository;
use InvalidArgumentException;

class PlantaService {
    private PlantaRepository $plantaRepository;

    public function __construct(PlantaRepository $plantaRepository = null) {
        $this->plantaRepository = $plantaRepository ?? new PlantaRepository();
    }

    public function getPlantaById(int $id): ?Planta {
        return $this->plantaRepository->findById($id);
    }

    public function getAllPlantas(): array {
        return $this->plantaRepository->findAll();
    }

    public function getPlantasByHospital(int $idHospital): array {
        return $this->plantaRepository->findByHospital($idHospital);
    }

    public function getActivePlantas(): array {
        return $this->plantaRepository->findActive();
    }

    public function createPlanta(array $data): Planta {
        $this->validatePlantaData($data);
        
        $planta = new Planta(
            null,
            $data['id_hospital'],
            $data['nombre'],
            $data['activo'] ?? true
        );
        
        return $this->plantaRepository->save($planta);
    }

    public function updatePlanta(int $id, array $data): Planta {
        $planta = $this->plantaRepository->findById($id);
        if (!$planta) {
            throw new InvalidArgumentException('Planta no encontrada');
        }
        
        if (isset($data['id_hospital'])) {
            $planta->setIdHospital($data['id_hospital']);
        }
        
        if (isset($data['nombre'])) {
            $planta->setNombre($data['nombre']);
        }
        
        if (isset($data['activo'])) {
            $planta->setActivo($data['activo']);
        }
        
        return $this->plantaRepository->save($planta);
    }

    public function deletePlanta(int $id): bool {
        return $this->plantaRepository->delete($id);
    }

    public function desactivarPlanta(int $id): bool {
        return $this->plantaRepository->softDelete($id);
    }

    private function validatePlantaData(array $data): void {
        if (!isset($data['id_hospital'])) {
            throw new InvalidArgumentException('El hospital es obligatorio');
        }
        
        if (!isset($data['nombre']) || empty($data['nombre'])) {
            throw new InvalidArgumentException('El nombre de la planta es obligatorio');
        }
    }
}
