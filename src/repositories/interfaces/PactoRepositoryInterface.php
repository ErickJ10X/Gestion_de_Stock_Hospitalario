<?php

namespace repositories\interfaces;

use models\Pacto;

interface PactoRepositoryInterface
{
    public function findById(int $id): ?Pacto;
    
    public function findAll(bool $soloActivos = true): array;
    
    public function findByProducto(int $idProducto, bool $soloActivos = true): array;
    
    public function findByUbicacion(string $tipoUbicacion, int $idDestino, bool $soloActivos = true): array;
    
    public function save(Pacto $pacto): Pacto;
    
    public function desactivar(int $idPacto): bool;
}
