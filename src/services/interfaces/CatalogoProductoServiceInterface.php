<?php

namespace services\interfaces;

use Models\CatalogoProducto;

interface CatalogoProductoServiceInterface {
    /**
     * Obtiene todos los catálogos de productos
     * @return CatalogoProducto[] Lista de catálogos
     */
    public function getAllCatalogoProductos(): array;
    
    /**
     * Obtiene un catálogo por su ID
     * @param int $id ID del catálogo
     * @return CatalogoProducto|null Catálogo encontrado o null si no existe
     */
    public function getCatalogoProductoById(int $id): ?CatalogoProducto;
    
    /**
     * Obtiene todos los catálogos de una planta
     * @param int $idPlanta ID de la planta
     * @return CatalogoProducto[] Lista de catálogos de la planta
     */
    public function getCatalogoProductosByPlanta(int $idPlanta): array;
    
    /**
     * Obtiene todos los catálogos de un producto
     * @param int $idProducto ID del producto
     * @return CatalogoProducto[] Lista de catálogos del producto
     */
    public function getCatalogoProductosByProducto(int $idProducto): array;
    
    /**
     * Obtiene un catálogo por planta y producto
     * @param int $idPlanta ID de la planta
     * @param int $idProducto ID del producto
     * @return CatalogoProducto|null Catálogo encontrado o null si no existe
     */
    public function getCatalogoProductoByPlantaAndProducto(int $idPlanta, int $idProducto): ?CatalogoProducto;
    
    /**
     * Obtiene todos los catálogos activos
     * @return CatalogoProducto[] Lista de catálogos activos
     */
    public function getActiveCatalogoProductos(): array;
    
    /**
     * Obtiene todos los catálogos activos de una planta
     * @param int $idPlanta ID de la planta
     * @return CatalogoProducto[] Lista de catálogos activos de la planta
     */
    public function getActiveCatalogoProductosByPlanta(int $idPlanta): array;
    
    /**
     * Crea un nuevo catálogo
     * @param CatalogoProducto $catalogoProducto Catálogo a crear
     * @return CatalogoProducto Catálogo creado
     */
    public function createCatalogoProducto(CatalogoProducto $catalogoProducto): CatalogoProducto;
    
    /**
     * Actualiza un catálogo existente
     * @param CatalogoProducto $catalogoProducto Catálogo con los datos actualizados
     * @return bool True si se actualizó correctamente
     */
    public function updateCatalogoProducto(CatalogoProducto $catalogoProducto): bool;
    
    /**
     * Elimina un catálogo por su ID
     * @param int $id ID del catálogo a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function deleteCatalogoProducto(int $id): bool;
    
    /**
     * Activa un catálogo
     * @param int $id ID del catálogo
     * @return bool True si se activó correctamente
     */
    public function activateCatalogoProducto(int $id): bool;
    
    /**
     * Desactiva un catálogo
     * @param int $id ID del catálogo
     * @return bool True si se desactivó correctamente
     */
    public function deactivateCatalogoProducto(int $id): bool;
}
