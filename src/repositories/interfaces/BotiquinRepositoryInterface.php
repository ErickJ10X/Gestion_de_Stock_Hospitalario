<?php

namespace repositories\interfaces;

use Models\Botiquin;

interface BotiquinRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?Botiquin;
    public function findByPlanta(int $idPlanta): array;
    public function findByNombre(string $nombre, int $idPlanta): ?Botiquin;
    public function findActive(): array;
    public function findActiveByPlanta(int $idPlanta): array;
    public function save(Botiquin $botiquin): Botiquin;
    public function update(Botiquin $botiquin): bool;
    public function delete(int $id): bool;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
}
