<?php

namespace services;

use Models\Almacen;
use Repositories\Interfaces\AlmacenRepositoryInterface;
use Repositories\Interfaces\HospitalRepositoryInterface;
use Repositories\Interfaces\PlantaRepositoryInterface;
use Services\Interfaces\AlmacenServiceInterface;

class AlmacenService implements AlmacenServiceInterface {
    private $almacenRepository;
    private $hospitalRepository;
    private $plantaRepository;

    public function __construct(
        AlmacenRepositoryInterface $almacenRepository,
        HospitalRepositoryInterface $hospitalRepository,
        PlantaRepositoryInterface $plantaRepository
    ) {
        $this->almacenRepository = $almacenRepository;
        $this->hospitalRepository = $hospitalRepository;
        $this->plantaRepository = $plantaRepository;
    }

    public function getAllAlmacenes(): array {
        return $this->almacenRepository->findAll();
    }

    public function getAlmacenById(int $id): ?Almacen {
        return $this->almacenRepository->findById($id);
    }

    public function getAlmacenesByHospital(int $idHospital): array {
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->findById($idHospital);
        if ($hospital === null) {
            throw new \InvalidArgumentException('El hospital no existe');
        }
        
        return $this->almacenRepository->findByHospital($idHospital);
    }

    public function getAlmacenesByPlanta(int $idPlanta): array {
        // Verificar que la planta existe
        $planta = $this->plantaRepository->findById($idPlanta);
        if ($planta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->almacenRepository->findByPlanta($idPlanta);
    }

    public function getAlmacenesByTipo(string $tipo): array {
        // Validar tipo
        if (!in_array($tipo, ['General', 'Planta'])) {
            throw new \InvalidArgumentException('Tipo de almacén inválido. Valores permitidos: General, Planta');
        }
        
        return $this->almacenRepository->findByTipo($tipo);
    }

    public function getActiveAlmacenes(): array {
        return $this->almacenRepository->findActive();
    }

    public function getActiveAlmacenesByHospital(int $idHospital): array {
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->findById($idHospital);
        if ($hospital === null) {
            throw new \InvalidArgumentException('El hospital no existe');
        }
        
        return $this->almacenRepository->findActiveByHospital($idHospital);
    }

    public function getGeneralAlmacenByHospital(int $idHospital): ?Almacen {
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->findById($idHospital);
        if ($hospital === null) {
            throw new \InvalidArgumentException('El hospital no existe');
        }
        
        return $this->almacenRepository->findGeneralByHospital($idHospital);
    }

    public function createAlmacen(Almacen $almacen): Almacen {
        // Validaciones
        $this->validateAlmacen($almacen);
        
        // Para almacenes tipo General, verificar que no exista ya uno para ese hospital
        if ($almacen->getTipo() === 'General') {
            $existing = $this->almacenRepository->findGeneralByHospital($almacen->getIdHospital());
            if ($existing !== null) {
                throw new \InvalidArgumentException('Ya existe un almacén general para este hospital');
            }
            
            // Un almacén General no debe tener planta asociada
            $almacen->setIdPlanta(null);
        } else {
            // Un almacén Planta debe tener una planta asociada
            if ($almacen->getIdPlanta() === null) {
                throw new \InvalidArgumentException('Un almacén de tipo Planta debe tener una planta asociada');
            }
            
            // Verificar que la planta pertenece al hospital
            $planta = $this->plantaRepository->findById($almacen->getIdPlanta());
            if ($planta === null || $planta->getIdHospital() != $almacen->getIdHospital()) {
                throw new \InvalidArgumentException('La planta no pertenece al hospital especificado');
            }
            
            // Verificar que no exista ya un almacén para esta planta
            $existingPlantaAlmacenes = $this->almacenRepository->findByPlanta($almacen->getIdPlanta());
            if (count($existingPlantaAlmacenes) > 0) {
                throw new \InvalidArgumentException('Ya existe un almacén para esta planta');
            }
        }
        
        return $this->almacenRepository->save($almacen);
    }

    public function updateAlmacen(Almacen $almacen): bool {
        // Validaciones
        $this->validateAlmacen($almacen);
        
        if ($almacen->getIdAlmacen() === null) {
            throw new \InvalidArgumentException('No se puede actualizar un almacén sin ID');
        }
        
        // Verificar que el almacén existe
        $existingAlmacen = $this->almacenRepository->findById($almacen->getIdAlmacen());
        if ($existingAlmacen === null) {
            throw new \InvalidArgumentException('El almacén no existe');
        }
        
        // Realizar las mismas validaciones que en createAlmacen, pero excluyendo este almacén
        if ($almacen->getTipo() === 'General') {
            $existing = $this->almacenRepository->findGeneralByHospital($almacen->getIdHospital());
            if ($existing !== null && $existing->getIdAlmacen() !== $almacen->getIdAlmacen()) {
                throw new \InvalidArgumentException('Ya existe un almacén general para este hospital');
            }
            
            // Un almacén General no debe tener planta asociada
            $almacen->setIdPlanta(null);
        } else {
            // Un almacén Planta debe tener una planta asociada
            if ($almacen->getIdPlanta() === null) {
                throw new \InvalidArgumentException('Un almacén de tipo Planta debe tener una planta asociada');
            }
            
            // Verificar que la planta pertenece al hospital
            $planta = $this->plantaRepository->findById($almacen->getIdPlanta());
            if ($planta === null || $planta->getIdHospital() != $almacen->getIdHospital()) {
                throw new \InvalidArgumentException('La planta no pertenece al hospital especificado');
            }
            
            // Verificar que no exista ya un almacén para esta planta (excluyendo este)
            $existingPlantaAlmacenes = $this->almacenRepository->findByPlanta($almacen->getIdPlanta());
            foreach ($existingPlantaAlmacenes as $existingAlm) {
                if ($existingAlm->getIdAlmacen() !== $almacen->getIdAlmacen()) {
                    throw new \InvalidArgumentException('Ya existe un almacén para esta planta');
                }
            }
        }
        
        return $this->almacenRepository->update($almacen);
    }

    public function deleteAlmacen(int $id): bool {
        // Verificar que el almacén existe
        $existingAlmacen = $this->almacenRepository->findById($id);
        if ($existingAlmacen === null) {
            throw new \InvalidArgumentException('El almacén no existe');
        }
        
        // Aquí podrían añadirse más validaciones, como verificar que no hay stock en el almacén
        
        return $this->almacenRepository->delete($id);
    }

    public function activateAlmacen(int $id): bool {
        // Verificar que el almacén existe
        $existingAlmacen = $this->almacenRepository->findById($id);
        if ($existingAlmacen === null) {
            throw new \InvalidArgumentException('El almacén no existe');
        }
        
        return $this->almacenRepository->activate($id);
    }

    public function deactivateAlmacen(int $id): bool {
        // Verificar que el almacén existe
        $existingAlmacen = $this->almacenRepository->findById($id);
        if ($existingAlmacen === null) {
            throw new \InvalidArgumentException('El almacén no existe');
        }
        
        return $this->almacenRepository->deactivate($id);
    }
    
    private function validateAlmacen(Almacen $almacen): void {
        if (!in_array($almacen->getTipo(), ['General', 'Planta'])) {
            throw new \InvalidArgumentException('Tipo de almacén inválido. Valores permitidos: General, Planta');
        }
        
        if ($almacen->getIdHospital() === null) {
            throw new \InvalidArgumentException('El almacén debe tener un hospital asociado');
        }
        
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->findById($almacen->getIdHospital());
        if ($hospital === null) {
            throw new \InvalidArgumentException('El hospital asociado no existe');
        }
        
        // Si es de tipo Planta, verificar que la planta existe
        if ($almacen->getTipo() === 'Planta' && $almacen->getIdPlanta() !== null) {
            $planta = $this->plantaRepository->findById($almacen->getIdPlanta());
            if ($planta === null) {
                throw new \InvalidArgumentException('La planta asociada no existe');
            }
        }
    }
}
