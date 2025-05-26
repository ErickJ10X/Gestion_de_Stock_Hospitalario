<?php

namespace controller;

use Exception;
use model\service\BotiquinesService;

require_once(__DIR__ . '/../model/service/BotiquinesService.php');

class BotiquinesController
{
    private BotiquinesService $botiquinesService;

    public function __construct()
    {
        $this->botiquinesService = new BotiquinesService();
    }

    public function index(): array
    {
        try {
            return ['error' => false, 'botiquines' => $this->botiquinesService->getAllBotiquines()];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            $botiquin = $this->botiquinesService->getBotiquinById($id);
            if ($botiquin) {
                return ['error' => false, 'botiquin' => $botiquin];
            } else {
                return ['error' => true, 'mensaje' => 'Botiquín no encontrado'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($nombre, $planta_id): array
    {
        try {
            if (empty(trim($nombre))) {
                return ['error' => true, 'mensaje' => 'El nombre del botiquín es obligatorio'];
            }

            if (!is_numeric($planta_id) || $planta_id <= 0) {
                return ['error' => true, 'mensaje' => 'El ID de la planta debe ser un número positivo'];
            }

            $resultado = $this->botiquinesService->createBotiquin($nombre, $planta_id);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Botiquín creado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear el botiquín'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($id, $nombre, $planta_id): array
    {
        try {
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de botiquín inválido'];
            }

            if (empty(trim($nombre))) {
                return ['error' => true, 'mensaje' => 'El nombre del botiquín es obligatorio'];
            }

            if (!is_numeric($planta_id) || $planta_id <= 0) {
                return ['error' => true, 'mensaje' => 'El ID de la planta debe ser un número positivo'];
            }

            $resultado = $this->botiquinesService->updateBotiquin($id, $nombre, $planta_id);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Botiquín actualizado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar el botiquín'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($id): array
    {
        try {
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de botiquín inválido'];
            }

            $resultado = $this->botiquinesService->deleteBotiquin($id);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Botiquín eliminado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar el botiquín'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
