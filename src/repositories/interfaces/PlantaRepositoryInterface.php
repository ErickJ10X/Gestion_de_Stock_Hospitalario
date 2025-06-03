<?php

namespace repositories\interfaces;

use Models\Planta;

interface PlantaRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?Planta;
    public function findByHospital(int $idHospital): array;
    public function findActive(): array;
    public function findActiveByHospital(int $idHospital): array;
    public function save(Planta $planta): Planta;
    public function update(Planta $planta): bool;
    public function delete(int $id): bool;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
}
