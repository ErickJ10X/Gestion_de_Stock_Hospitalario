<?php

namespace model\service;

use model\entity\Botiquin;
use model\repository\BotiquinRepository;
use InvalidArgumentException;

class BotiquinService {
    private BotiquinRepository $botiquinRepository;

    public function __construct(BotiquinRepository $botiquinRepository = null) {
        $this->botiquinRepository = $botiquinRepository ?? new BotiquinRepository();
    }

    public function getBotiquinById(int $id): ?Botiquin {
        return $this->botiquinRepository->findById($id);
    }

    public function getAllBotiquines(): array {
        return $this->botiquinRepository->findAll();
    }

    public function getBotiquinesByPlanta(int $idPlanta): array {
        return $this->botiquinRepository->findByPlanta($idPlanta);
    }

    public function getBotiquinesByHospital(int $idHospital): array {
        return $this->botiquinRepository->findByHospital($idHospital);
    }

    public function createBotiquin(array $data): Botiquin {
        $this->validateBotiquinData($data);
        
        $botiquin = new Botiquin(
            null,
            $data['id_planta'],
            $data['nombre'],
            $data['activo'] ?? true
        );
        
        return $this->botiquinRepository->save($botiquin);
    }

    public function updateBotiquin(int $id, array $data): Botiquin {
        $botiquin = $this->botiquinRepository->findById($id);
        if (!$botiquin) {
            throw new InvalidArgumentException('Botiquín no encontrado');
        }
        
        if (isset($data['id_planta'])) {
            $botiquin->setIdPlanta($data['id_planta']);
        }
        
        if (isset($data['nombre'])) {
            $botiquin->setNombre($data['nombre']);
        }
        
        if (isset($data['activo'])) {
            $botiquin->setActivo($data['activo']);
        }
        
        return $this->botiquinRepository->save($botiquin);
    }

    public function deleteBotiquin(int $id): bool {
        return $this->botiquinRepository->delete($id);
    }

    public function desactivarBotiquin(int $id): bool {
        return $this->botiquinRepository->softDelete($id);
    }

    private function validateBotiquinData(array $data): void {
        if (!isset($data['id_planta'])) {
            throw new InvalidArgumentException('La planta es obligatoria');
        }
        
        if (!isset($data['nombre']) || empty($data['nombre'])) {
            throw new InvalidArgumentException('El nombre es obligatorio');
        }
    }
}
