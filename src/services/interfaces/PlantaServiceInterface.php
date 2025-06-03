<?php

namespace services\interfaces;

use Models\Planta;

interface PlantaServiceInterface {
    /**
     * Obtiene todas las plantas
     * @return Planta[] Lista de plantas
     */
    public function getAllPlantas(): array;
    
    /**
     * Obtiene una planta por su ID
     * @param int $id ID de la planta
     * @return Planta|null Planta encontrada o null si no existe
     */
    public function getPlantaById(int $id): ?Planta;
    
    /**
     * Obtiene todas las plantas de un hospital
     * @param int $idHospital ID del hospital
     * @return Planta[] Lista de plantas del hospital
     */
    public function getPlantasByHospital(int $idHospital): array;
    
    /**
     * Obtiene todas las plantas activas
     * @return Planta[] Lista de plantas activas
     */
    public function getActivePlantas(): array;
    
    /**
     * Obtiene todas las plantas activas de un hospital
     * @param int $idHospital ID del hospital
     * @return Planta[] Lista de plantas activas del hospital
     */
    public function getActivePlantasByHospital(int $idHospital): array;
    
    /**
     * Crea una nueva planta
     * @param Planta $planta Planta a crear
     * @return Planta Planta creada
     */
    public function createPlanta(Planta $planta): Planta;
    
    /**
     * Actualiza una planta existente
     * @param Planta $planta Planta con los datos actualizados
     * @return bool True si se actualizó correctamente
     */
    public function updatePlanta(Planta $planta): bool;
    
    /**
     * Elimina una planta por su ID
     * @param int $id ID de la planta a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function deletePlanta(int $id): bool;
    
    /**
     * Activa una planta
     * @param int $id ID de la planta
     * @return bool True si se activó correctamente
     */
    public function activatePlanta(int $id): bool;
    
    /**
     * Desactiva una planta
     * @param int $id ID de la planta
     * @return bool True si se desactivó correctamente
     */
    public function deactivatePlanta(int $id): bool;
}
