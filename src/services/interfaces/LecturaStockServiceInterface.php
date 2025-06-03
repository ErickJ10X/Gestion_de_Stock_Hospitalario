<?php

namespace services\interfaces;

use models\LecturaStock;

interface LecturaStockServiceInterface
{
    public function registerReading(int $idProducto, int $idBotiquin, int $cantidadDisponible, int $registradoPor): LecturaStock;
    
    public function getLastReadingByProductAndKit(int $idProducto, int $idBotiquin): ?LecturaStock;
    
    public function getReadingHistoryByKit(int $idBotiquin): array;
    
    public function getReadingHistoryByProduct(int $idProducto): array;
    
    public function getCurrentStockByKit(int $idBotiquin): array;
}
