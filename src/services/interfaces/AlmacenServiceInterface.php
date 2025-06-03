<?php

namespace services\interfaces;

use Models\Almacen;

interface AlmacenServiceInterface {
    /**
     * Obtiene todos los almacenes
     * @return Almacen[] Lista de almacenes
     */
    public function getAllAlmacenes(): array;
    
    /**
     * Obtiene un almacén por su ID
     * @param int $id ID del almacén
     * @return Almacen|null Almacén encontrado o null si no existe
     */
    public function getAlmacenById(int $id): ?Almacen;
    
    /**
     * Obtiene todos los almacenes de un hospital
     * @param int $idHospital ID del hospital
     * @return Almacen[] Lista de almacenes del hospital
     */
    public function getAlmacenesByHospital(int $idHospital): array;
    
    /**
     * Obtiene todos los almacenes de una planta
     * @param int $idPlanta ID de la planta
     * @return Almacen[] Lista de almacenes de la planta
     */
    public function getAlmacenesByPlanta(int $idPlanta): array;
    
    /**
     * Obtiene todos los almacenes de un tipo específico
     * @param string $tipo Tipo de almacén (General o Planta)
     * @return Almacen[] Lista de almacenes del tipo especificado
     */
    public function getAlmacenesByTipo(string $tipo): array;
    
    /**
     * Obtiene todos los almacenes activos
     * @return Almacen[] Lista de almacenes activos
     */
    public function getActiveAlmacenes(): array;
    
    /**
     * Obtiene todos los almacenes activos de un hospital
     * @param int $idHospital ID del hospital
     * @return Almacen[] Lista de almacenes activos del hospital
     */
    public function getActiveAlmacenesByHospital(int $idHospital): array;
    
    /**
     * Obtiene el almacén general de un hospital
     * @param int $idHospital ID del hospital
     * @return Almacen|null Almacén general del hospital o null si no existe
     */
    public function getGeneralAlmacenByHospital(int $idHospital): ?Almacen;
    
    /**
     * Crea un nuevo almacén
     * @param Almacen $almacen Almacén a crear
     * @return Almacen Almacén creado
     */
    public function createAlmacen(Almacen $almacen): Almacen;
    
    /**
     * Actualiza un almacén existente
     * @param Almacen $almacen Almacén con los datos actualizados
     * @return bool True si se actualizó correctamente
     */
    public function updateAlmacen(Almacen $almacen): bool;
    
    /**
     * Elimina un almacén por su ID
     * @param int $id ID del almacén a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function deleteAlmacen(int $id): bool;
    
    /**
     * Activa un almacén
     * @param int $id ID del almacén
     * @return bool True si se activó correctamente
     */
    public function activateAlmacen(int $id): bool;
    
    /**
     * Desactiva un almacén
     * @param int $id ID del almacén
     * @return bool True si se desactivó correctamente
     */
    public function deactivateAlmacen(int $id): bool;
}
