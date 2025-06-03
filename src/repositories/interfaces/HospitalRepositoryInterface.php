<?php

namespace repositories\interfaces;

use Models\Hospital;

interface HospitalRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?Hospital;
    public function findActive(): array;
    public function save(Hospital $hospital): Hospital;
    public function update(Hospital $hospital): bool;
    public function delete(int $id): bool;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
}
