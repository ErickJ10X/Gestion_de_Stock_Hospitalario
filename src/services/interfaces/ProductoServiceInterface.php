<?php

namespace services\interfaces;

use Models\Producto;

interface ProductoServiceInterface {
    /**
     * Obtiene todos los productos
     * @return Producto[] Lista de productos
     */
    public function getAllProductos(): array;
    
    /**
     * Obtiene un producto por su ID
     * @param int $id ID del producto
     * @return Producto|null Producto encontrado o null si no existe
     */
    public function getProductoById(int $id): ?Producto;
    
    /**
     * Obtiene un producto por su código
     * @param string $codigo Código del producto
     * @return Producto|null Producto encontrado o null si no existe
     */
    public function getProductoByCodigo(string $codigo): ?Producto;
    
    /**
     * Obtiene todos los productos activos
     * @return Producto[] Lista de productos activos
     */
    public function getActiveProductos(): array;
    
    /**
     * Crea un nuevo producto
     * @param Producto $producto Producto a crear
     * @return Producto Producto creado
     */
    public function createProducto(Producto $producto): Producto;
    
    /**
     * Actualiza un producto existente
     * @param Producto $producto Producto con los datos actualizados
     * @return bool True si se actualizó correctamente
     */
    public function updateProducto(Producto $producto): bool;
    
    /**
     * Elimina un producto por su ID
     * @param int $id ID del producto a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function deleteProducto(int $id): bool;
    
    /**
     * Activa un producto
     * @param int $id ID del producto
     * @return bool True si se activó correctamente
     */
    public function activateProducto(int $id): bool;
    
    /**
     * Desactiva un producto
     * @param int $id ID del producto
     * @return bool True si se desactivó correctamente
     */
    public function deactivateProducto(int $id): bool;
}
