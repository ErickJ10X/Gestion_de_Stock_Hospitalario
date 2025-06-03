<?php

namespace services;

use models\LecturaStock;
use repositories\BotiquinRepository;
use repositories\interfaces\LecturaStockRepositoryInterface;
use repositories\ProductoRepository;
use services\interfaces\LecturaStockServiceInterface;

class LecturaStockService implements LecturaStockServiceInterface
{
    private LecturaStockRepositoryInterface $lecturaStockRepository;
    private ProductoRepository $productoRepository;
    private BotiquinRepository $botiquinRepository;
    
    public function __construct(
        LecturaStockRepositoryInterface $lecturaStockRepository,
        ProductoRepository $productoRepository,
        BotiquinRepository $botiquinRepository
    ) {
        $this->lecturaStockRepository = $lecturaStockRepository;
        $this->productoRepository = $productoRepository;
        $this->botiquinRepository = $botiquinRepository;
    }
    
    public function registerReading(int $idProducto, int $idBotiquin, int $cantidadDisponible, int $registradoPor): LecturaStock
    {
        $lectura = new LecturaStock();
        $lectura->setIdProducto($idProducto);
        $lectura->setIdBotiquin($idBotiquin);
        $lectura->setCantidadDisponible($cantidadDisponible);
        $lectura->setFechaLectura(new \DateTime());
        $lectura->setRegistradoPor($registradoPor);
        
        return $this->lecturaStockRepository->save($lectura);
    }
    
    public function getLastReadingByProductAndKit(int $idProducto, int $idBotiquin): ?LecturaStock
    {
        return $this->lecturaStockRepository->findLatestByProductoAndBotiquin($idProducto, $idBotiquin);
    }
    
    public function getReadingHistoryByKit(int $idBotiquin): array
    {
        return $this->lecturaStockRepository->findByBotiquin($idBotiquin);
    }
    
    public function getReadingHistoryByProduct(int $idProducto): array
    {
        return $this->lecturaStockRepository->findByProducto($idProducto);
    }
    
    public function getCurrentStockByKit(int $idBotiquin): array
    {
        $botiquin = $this->botiquinRepository->findById($idBotiquin);
        if (!$botiquin) {
            throw new \Exception("Botiquín no encontrado");
        }
        
        // Obtenemos los productos asociados al botiquín
        $productos = $this->productoRepository->findAll();
        
        $stockActual = [];
        foreach ($productos as $producto) {
            $ultimaLectura = $this->getLastReadingByProductAndKit(
                $producto->getIdProducto(),
                $idBotiquin
            );
            
            $stockActual[] = [
                'producto' => $producto,
                'ultima_lectura' => $ultimaLectura
            ];
        }
        
        return $stockActual;
    }
}
