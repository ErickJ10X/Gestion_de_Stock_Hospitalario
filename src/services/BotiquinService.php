<?php

namespace services;

use Models\Botiquin;
use Repositories\Interfaces\BotiquinRepositoryInterface;
use Repositories\Interfaces\PlantaRepositoryInterface;
use Services\Interfaces\BotiquinServiceInterface;

class BotiquinService implements BotiquinServiceInterface {
    private $botiquinRepository;
    private $plantaRepository;

    public function __construct(
        BotiquinRepositoryInterface $botiquinRepository,
        PlantaRepositoryInterface $plantaRepository
    ) {
        $this->botiquinRepository = $botiquinRepository;
        $this->plantaRepository = $plantaRepository;
    }

    public function getAllBotiquines(): array {
        return $this->botiquinRepository->findAll();
    }

    public function getBotiquinById(int $id): ?Botiquin {
        return $this->botiquinRepository->findById($id);
    }

    public function getBotiquinesByPlanta(int $idPlanta): array {
        // Verificar que la planta existe
        $planta = $this->plantaRepository->findById($idPlanta);
        if ($planta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->botiquinRepository->findByPlanta($idPlanta);
    }

    public function getBotiquinByNombre(string $nombre, int $idPlanta): ?Botiquin {
        // Verificar que la planta existe
        $planta = $this->plantaRepository->findById($idPlanta);
        if ($planta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->botiquinRepository->findByNombre($nombre, $idPlanta);
    }

    public function getActiveBotiquines(): array {
        return $this->botiquinRepository->findActive();
    }

    public function getActiveBotiquinesByPlanta(int $idPlanta): array {
        // Verificar que la planta existe
        $planta = $this->plantaRepository->findById($idPlanta);
        if ($planta === null) {
            throw new \InvalidArgumentException('La planta no existe');
        }
        
        return $this->botiquinRepository->findActiveByPlanta($idPlanta);
    }

    public function createBotiquin(Botiquin $botiquin): Botiquin {
        // Validaciones
        $this->validateBotiquin($botiquin);
        
        // Verificar que no existe un botiquín con el mismo nombre en la misma planta
        $existingBotiquin = $this->botiquinRepository->findByNombre($botiquin->getNombre(), $botiquin->getIdPlanta());
        if ($existingBotiquin !== null) {
            throw new \InvalidArgumentException('Ya existe un botiquín con ese nombre en esta planta');
        }
        
        return $this->botiquinRepository->save($botiquin);
    }

    public function updateBotiquin(Botiquin $botiquin): bool {
        // Validaciones
        $this->validateBotiquin($botiquin);
        
        if ($botiquin->getIdBotiquin() === null) {
            throw new \InvalidArgumentException('No se puede actualizar un botiquín sin ID');
        }
        
        // Verificar que el botiquín existe
        $existingBotiquin = $this->botiquinRepository->findById($botiquin->getIdBotiquin());
        if ($existingBotiquin === null) {
            throw new \InvalidArgumentException('El botiquín no existe');
        }
        
        // Si se cambió el nombre o la planta, verificar que no haya otro con el mismo nombre en la misma planta
        if ($botiquin->getNombre() !== $existingBotiquin->getNombre() || 
            $botiquin->getIdPlanta() !== $existingBotiquin->getIdPlanta()) {
            
            $otherBotiquin = $this->botiquinRepository->findByNombre($botiquin->getNombre(), $botiquin->getIdPlanta());
            if ($otherBotiquin !== null && $otherBotiquin->getIdBotiquin() !== $botiquin->getIdBotiquin()) {
                throw new \InvalidArgumentException('Ya existe un botiquín con ese nombre en esta planta');
            }
        }
        
        return $this->botiquinRepository->update($botiquin);
    }

    public function deleteBotiquin(int $id): bool {
        // Verificar que el botiquín existe
        $existingBotiquin = $this->botiquinRepository->findById($id);
        if ($existingBotiquin === null) {
            throw new \InvalidArgumentException('El botiquín no existe');
        }
        
        // Aquí podrían añadirse más validaciones, como verificar que no hay stock en el botiquín
        
        return $this->botiquinRepository->delete($id);
    }

    public function activateBotiquin(int $id): bool {
        // Verificar que el botiquín existe
        $existingBotiquin = $this->botiquinRepository->findById($id);
        if ($existingBotiquin === null) {
            throw new \InvalidArgumentException('El botiquín no existe');
        }
        
        return $this->botiquinRepository->activate($id);
    }

    public function deactivateBotiquin(int $id): bool {
        // Verificar que el botiquín existe
        $existingBotiquin = $this->botiquinRepository->findById($id);
        if ($existingBotiquin === null) {
            throw new \InvalidArgumentException('El botiquín no existe');
        }
        
        return $this->botiquinRepository->deactivate($id);
    }
    
    private function validateBotiquin(Botiquin $botiquin): void {
        if (empty($botiquin->getNombre())) {
            throw new \InvalidArgumentException('El nombre del botiquín no puede estar vacío');
        }
        
        if ($botiquin->getIdPlanta() === null) {
            throw new \InvalidArgumentException('El botiquín debe tener una planta asociada');
        }
        
        // Verificar que la planta existe
        $planta = $this->plantaRepository->findById($botiquin->getIdPlanta());
        if ($planta === null) {
            throw new \InvalidArgumentException('La planta asociada no existe');
        }
    }
}
