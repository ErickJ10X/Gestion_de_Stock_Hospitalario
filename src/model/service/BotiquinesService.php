<?php

namespace model\service;

use model\entity\Botiquines;
use model\repository\BotiquinesRepository;
use PDOException;
use Exception;

require_once(__DIR__ . '/../repository/BotiquinesRepository.php');
require_once(__DIR__ . '/../entity/Botiquines.php');
require_once(__DIR__ . '/../../../config/database.php');

class BotiquinesService
{
    private BotiquinesRepository $botiquinesRepository;

    public function __construct()
    {
        $this->botiquinesRepository = new BotiquinesRepository();
    }

    public function getAllBotiquines(): array
    {
        try {
            return $this->botiquinesRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al cargar los botiquines: " . $e->getMessage());
        }
    }

    public function getBotiquinById($id): ?Botiquines
    {
        try {
            return $this->botiquinesRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al cargar el botiquín: " . $e->getMessage());
        }
    }

    public function deleteBotiquin($id): bool
    {
        try {
            return $this->botiquinesRepository->delete($id);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el botiquín: " . $e->getMessage());
        }
    }

    public function updateBotiquin($id, $nombre, $planta_id): bool
    {
        try {
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                throw new Exception("ID de botiquín inválido");
            }

            if (empty(trim($nombre))) {
                throw new Exception("El nombre del botiquín es obligatorio");
            }

            if (!is_numeric($planta_id) || $planta_id <= 0) {
                throw new Exception("El ID de la planta debe ser un número positivo");
            }

            $botiquin = $this->getBotiquinById($id);
            if (!$botiquin) {
                throw new Exception("El botiquín no existe");
            }

            $botiquin = new Botiquines($id, $nombre, $planta_id);
            return $this->botiquinesRepository->update($botiquin);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el botiquín: " . $e->getMessage());
        }
    }

    public function createBotiquin($nombre, $planta_id): bool
    {
        try {
            if (empty(trim($nombre))) {
                throw new Exception("El nombre del botiquín es obligatorio");
            }

            if (!is_numeric($planta_id) || $planta_id <= 0) {
                throw new Exception("El ID de la planta debe ser un número positivo");
            }

            $botiquin = new Botiquines(null, $nombre, $planta_id);
            return $this->botiquinesRepository->save($botiquin);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el botiquín: " . $e->getMessage());
        }
    }
}
