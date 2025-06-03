<?php

namespace services;

use models\Pacto;
use repositories\BotiquinRepository;
use repositories\interfaces\PactoRepositoryInterface;
use repositories\ProductoRepository;
use services\interfaces\PactoServiceInterface;

class PactoService implements PactoServiceInterface
{
    private PactoRepositoryInterface $pactoRepository;
    private ProductoRepository $productoRepository;
    private BotiquinRepository $botiquinRepository;
    
    public function __construct(
        PactoRepositoryInterface $pactoRepository,
        ProductoRepository $productoRepository,
        BotiquinRepository $botiquinRepository
    ) {
        $this->pactoRepository = $pactoRepository;
        $this->productoRepository = $productoRepository;
        $this->botiquinRepository = $botiquinRepository;
    }
    
    public function createAgreement(int $idProducto, string $tipoUbicacion, int $idDestino, int $cantidadPactada): Pacto
    {
        // Validar que el producto exista
        $producto = $this->productoRepository->findById($idProducto);
        if (!$producto) {
            throw new \Exception("El producto no existe");
        }
        
        // Validar que el destino exista según el tipo
        if ($tipoUbicacion === 'Botiquin') {
            $botiquin = $this->botiquinRepository->findById($idDestino);
            if (!$botiquin) {
                throw new \Exception("El botiquín no existe");
            }
        }
        // Para el tipo 'Planta' se podría agregar otra validación
        
        $pacto = new Pacto();
        $pacto->setIdProducto($idProducto);
        $pacto->setTipoUbicacion($tipoUbicacion);
        $pacto->setIdDestino($idDestino);
        $pacto->setCantidadPactada($cantidadPactada);
        $pacto->setActivo(true);
        
        return $this->pactoRepository->save($pacto);
    }
    
    public function updateAgreement(int $idPacto, int $cantidadPactada, bool $activo = true): ?Pacto
    {
        $pacto = $this->pactoRepository->findById($idPacto);
        if (!$pacto) {
            return null;
        }
        
        $pacto->setCantidadPactada($cantidadPactada);
        $pacto->setActivo($activo);
        
        return $this->pactoRepository->save($pacto);
    }
    
    public function deactivateAgreement(int $idPacto): bool
    {
        return $this->pactoRepository->desactivar($idPacto);
    }
    
    public function getAgreementsByProduct(int $idProducto, bool $soloActivos = true): array
    {
        return $this->pactoRepository->findByProducto($idProducto, $soloActivos);
    }
    
    public function getAgreementsByKit(int $idBotiquin, bool $soloActivos = true): array
    {
        return $this->pactoRepository->findByUbicacion('Botiquin', $idBotiquin, $soloActivos);
    }
    
    public function getAgreementsByPlant(int $idPlanta, bool $soloActivos = true): array
    {
        return $this->pactoRepository->findByUbicacion('Planta', $idPlanta, $soloActivos);
    }
    
    public function getAllAgreements(bool $soloActivos = true): array
    {
        return $this->pactoRepository->findAll($soloActivos);
    }
    
    public function verifyAgreementCompliance(int $idProducto, int $idBotiquin, int $cantidadActual): array
    {
        $pactos = $this->pactoRepository->findByUbicacion('Botiquin', $idBotiquin, true);
        
        $resultado = [];
        foreach ($pactos as $pacto) {
            if ($pacto->getIdProducto() === $idProducto) {
                $cumple = $cantidadActual >= $pacto->getCantidadPactada();
                $resultado[] = [
                    'pacto' => $pacto,
                    'cumple' => $cumple,
                    'diferencia' => $cantidadActual - $pacto->getCantidadPactada()
                ];
            }
        }
        
        return $resultado;
    }
}
