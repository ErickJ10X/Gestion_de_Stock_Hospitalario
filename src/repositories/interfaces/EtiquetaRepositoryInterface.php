<?php

namespace repositories\interfaces;

use models\Etiqueta;

interface EtiquetaRepositoryInterface {
    public function findById(int $id): ?Etiqueta;
    public function findAll(): array;
    public function findByReposicion(int $idReposicion): array;
    public function findByProducto(int $idProducto): array;
    public function findByTipo(string $tipo): array;
    public function findByPrioridad(string $prioridad): array;
    public function findNoImpresas(): array;
    public function save(Etiqueta $etiqueta): int;
    public function update(Etiqueta $etiqueta): bool;
    public function delete(int $id): bool;
}
