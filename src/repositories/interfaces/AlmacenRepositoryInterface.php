<?php

namespace repositories\interfaces;

use Models\Almacen;

interface AlmacenRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?Almacen;
    public function findByHospital(int $idHospital): array;
    public function findByPlanta(int $idPlanta): array;
    public function findByTipo(string $tipo): array;
    public function findActive(): array;
    public function findActiveByHospital(int $idHospital): array;
    public function findGeneralByHospital(int $idHospital): ?Almacen;
    public function save(Almacen $almacen): Almacen;
    public function update(Almacen $almacen): bool;
    public function delete(int $id): bool;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
}
