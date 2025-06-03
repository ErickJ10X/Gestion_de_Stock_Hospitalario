<?php

namespace repositories\interfaces;

use models\Reposicion;

interface ReposicionRepositoryInterface {
    public function findById(int $id): ?Reposicion;
    public function findAll(): array;
    public function findByBotiquin(int $idBotiquin): array;
    public function findByAlmacen(int $idAlmacen): array;
    public function findByProducto(int $idProducto): array;
    public function findUrgentes(): array;
    public function save(Reposicion $reposicion): int;
    public function update(Reposicion $reposicion): bool;
    public function delete(int $id): bool;
}
