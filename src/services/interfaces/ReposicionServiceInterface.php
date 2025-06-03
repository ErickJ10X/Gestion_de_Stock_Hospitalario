<?php

namespace services\interfaces;

use models\Reposicion;

interface ReposicionServiceInterface {
    public function getReplenishmentById(int $id): ?Reposicion;
    public function getAllReplenishments(): array;
    public function getReplenishmentsByKit(int $idBotiquin): array;
    public function getReplenishmentsByWarehouse(int $idAlmacen): array;
    public function getReplenishmentsByProduct(int $idProducto): array;
    public function getUrgentReplenishments(): array;
    public function createReplenishment(Reposicion $reposicion): int;
    public function updateReplenishment(Reposicion $reposicion): bool;
    public function deleteReplenishment(int $id): bool;
}
