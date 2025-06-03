<?php

namespace services\interfaces;

use Models\Hospital;

interface HospitalServiceInterface {
    /**
     * Obtiene todos los hospitales
     * @return Hospital[] Lista de hospitales
     */
    public function getAllHospitales(): array;
    
    /**
     * Obtiene un hospital por su ID
     * @param int $id ID del hospital
     * @return Hospital|null Hospital encontrado o null si no existe
     */
    public function getHospitalById(int $id): ?Hospital;
    
    /**
     * Obtiene todos los hospitales activos
     * @return Hospital[] Lista de hospitales activos
     */
    public function getActiveHospitales(): array;
    
    /**
     * Crea un nuevo hospital
     * @param Hospital $hospital Hospital a crear
     * @return Hospital Hospital creado
     */
    public function createHospital(Hospital $hospital): Hospital;
    
    /**
     * Actualiza un hospital existente
     * @param Hospital $hospital Hospital con los datos actualizados
     * @return bool True si se actualizó correctamente
     */
    public function updateHospital(Hospital $hospital): bool;
    
    /**
     * Elimina un hospital por su ID
     * @param int $id ID del hospital a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function deleteHospital(int $id): bool;
    
    /**
     * Activa un hospital
     * @param int $id ID del hospital
     * @return bool True si se activó correctamente
     */
    public function activateHospital(int $id): bool;
    
    /**
     * Desactiva un hospital
     * @param int $id ID del hospital
     * @return bool True si se desactivó correctamente
     */
    public function deactivateHospital(int $id): bool;
}
