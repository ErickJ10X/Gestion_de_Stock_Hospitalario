<?php

namespace services\interfaces;

use Models\Botiquin;

interface BotiquinServiceInterface {
    /**
     * Obtiene todos los botiquines
     * @return Botiquin[] Lista de botiquines
     */
    public function getAllBotiquines(): array;
    
    /**
     * Obtiene un botiquín por su ID
     * @param int $id ID del botiquín
     * @return Botiquin|null Botiquín encontrado o null si no existe
     */
    public function getBotiquinById(int $id): ?Botiquin;
    
    /**
     * Obtiene todos los botiquines de una planta
     * @param int $idPlanta ID de la planta
     * @return Botiquin[] Lista de botiquines de la planta
     */
    public function getBotiquinesByPlanta(int $idPlanta): array;
    
    /**
     * Busca un botiquín por nombre en una planta específica
     * @param string $nombre Nombre del botiquín
     * @param int $idPlanta ID de la planta
     * @return Botiquin|null Botiquín encontrado o null si no existe
     */
    public function getBotiquinByNombre(string $nombre, int $idPlanta): ?Botiquin;
    
    /**
     * Obtiene todos los botiquines activos
     * @return Botiquin[] Lista de botiquines activos
     */
    public function getActiveBotiquines(): array;
    
    /**
     * Obtiene todos los botiquines activos de una planta
     * @param int $idPlanta ID de la planta
     * @return Botiquin[] Lista de botiquines activos de la planta
     */
    public function getActiveBotiquinesByPlanta(int $idPlanta): array;
    
    /**
     * Crea un nuevo botiquín
     * @param Botiquin $botiquin Botiquín a crear
     * @return Botiquin Botiquín creado
     */
    public function createBotiquin(Botiquin $botiquin): Botiquin;
    
    /**
     * Actualiza un botiquín existente
     * @param Botiquin $botiquin Botiquín con los datos actualizados
     * @return bool True si se actualizó correctamente
     */
    public function updateBotiquin(Botiquin $botiquin): bool;
    
    /**
     * Elimina un botiquín por su ID
     * @param int $id ID del botiquín a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function deleteBotiquin(int $id): bool;
    
    /**
     * Activa un botiquín
     * @param int $id ID del botiquín
     * @return bool True si se activó correctamente
     */
    public function activateBotiquin(int $id): bool;
    
    /**
     * Desactiva un botiquín
     * @param int $id ID del botiquín
     * @return bool True si se desactivó correctamente
     */
    public function deactivateBotiquin(int $id): bool;
}
