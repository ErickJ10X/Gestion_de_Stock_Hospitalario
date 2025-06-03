<?php

namespace repositories\interfaces;

use Models\CatalogoProducto;

interface CatalogoProductoRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?CatalogoProducto;
    public function findByPlanta(int $idPlanta): array;
    public function findByProducto(int $idProducto): array;
    public function findByPlantaAndProducto(int $idPlanta, int $idProducto): ?CatalogoProducto;
    public function findActive(): array;
    public function findActiveByPlanta(int $idPlanta): array;
    public function save(CatalogoProducto $catalogoProducto): CatalogoProducto;
    public function update(CatalogoProducto $catalogoProducto): bool;
    public function delete(int $id): bool;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
}
