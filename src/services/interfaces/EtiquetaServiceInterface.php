<?php

namespace services\interfaces;

use models\Etiqueta;

interface EtiquetaServiceInterface {
    public function getLabelById(int $id): ?Etiqueta;
    public function getAllLabels(): array;
    public function getLabelsByReplenishment(int $idReposicion): array;
    public function getLabelsByProduct(int $idProducto): array;
    public function getLabelsByType(string $tipo): array;
    public function getLabelsByPriority(string $prioridad): array;
    public function getUnprintedLabels(): array;
    public function createLabel(Etiqueta $etiqueta): int;
    public function updateLabel(Etiqueta $etiqueta): bool;
    public function markLabelAsPrinted(int $id): bool;
    public function deleteLabel(int $id): bool;
    public function generateLabelsForReplenishment(int $idReposicion): array;
}
