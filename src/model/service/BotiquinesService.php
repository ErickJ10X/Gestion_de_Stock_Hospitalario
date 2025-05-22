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
    private $botiquinesRepository;

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
            $botiquin = new Botiquines($id, $nombre, $planta_id);
            return $this->botiquinesRepository->update($botiquin);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el botiquín: " . $e->getMessage());
        }
    }

    public function createBotiquin($nombre, $planta_id): bool
    {
        try {
            $botiquin = new Botiquines(null, $nombre, $planta_id);
            return $this->botiquinesRepository->save($botiquin);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el botiquín: " . $e->getMessage());
        }
    }
}
