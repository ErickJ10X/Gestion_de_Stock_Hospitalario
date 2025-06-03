<?php

namespace repositories\interfaces;

use models\LecturaStock;

interface LecturaStockRepositoryInterface
{
    public function findById(int $id): ?LecturaStock;
    
    public function findByBotiquin(int $idBotiquin): array;
    
    public function findByProducto(int $idProducto): array;
    
    public function findLatestByProductoAndBotiquin(int $idProducto, int $idBotiquin): ?LecturaStock;
    
    public function save(LecturaStock $lecturaStock): LecturaStock;
}
