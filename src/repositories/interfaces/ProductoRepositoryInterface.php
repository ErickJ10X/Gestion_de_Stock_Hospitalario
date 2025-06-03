<?php

namespace repositories\interfaces;

use Models\Producto;

interface ProductoRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?Producto;
    public function findByCodigo(string $codigo): ?Producto;
    public function findActive(): array;
    public function save(Producto $producto): Producto;
    public function update(Producto $producto): bool;
    public function delete(int $id): bool;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
}
