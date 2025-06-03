<?php

namespace services\interfaces;

use models\Pacto;

interface PactoServiceInterface
{
    public function createAgreement(int $idProducto, string $tipoUbicacion, int $idDestino, int $cantidadPactada): Pacto;
    
    public function updateAgreement(int $idPacto, int $cantidadPactada, bool $activo = true): ?Pacto;
    
    public function deactivateAgreement(int $idPacto): bool;
    
    public function getAgreementsByProduct(int $idProducto, bool $soloActivos = true): array;
    
    public function getAgreementsByKit(int $idBotiquin, bool $soloActivos = true): array;
    
    public function getAgreementsByPlant(int $idPlanta, bool $soloActivos = true): array;
    
    public function getAllAgreements(bool $soloActivos = true): array;
    
    public function verifyAgreementCompliance(int $idProducto, int $idBotiquin, int $cantidadActual): array;
}
