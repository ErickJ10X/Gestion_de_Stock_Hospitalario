<?php

namespace services;

use repositories\interfaces\ReposicionRepositoryInterface;
use services\interfaces\ReposicionServiceInterface;
use models\Reposicion;

class ReposicionService implements ReposicionServiceInterface {
    private ReposicionRepositoryInterface $reposicionRepository;

    public function __construct(ReposicionRepositoryInterface $reposicionRepository) {
        $this->reposicionRepository = $reposicionRepository;
    }

    public function getReplenishmentById(int $id): ?Reposicion {
        return $this->reposicionRepository->findById($id);
    }

    public function getAllReplenishments(): array {
        return $this->reposicionRepository->findAll();
    }

    public function getReplenishmentsByKit(int $idBotiquin): array {
        return $this->reposicionRepository->findByBotiquin($idBotiquin);
    }

    public function getReplenishmentsByWarehouse(int $idAlmacen): array {
        return $this->reposicionRepository->findByAlmacen($idAlmacen);
    }

    public function getReplenishmentsByProduct(int $idProducto): array {
        return $this->reposicionRepository->findByProducto($idProducto);
    }

    public function getUrgentReplenishments(): array {
        return $this->reposicionRepository->findUrgentes();
    }

    public function createReplenishment(Reposicion $reposicion): int {
        // Aquí se pueden añadir validaciones adicionales antes de guardar
        if ($reposicion->getCantidadRepuesta() <= 0) {
            throw new \InvalidArgumentException("La cantidad repuesta debe ser mayor que cero");
        }
        
        return $this->reposicionRepository->save($reposicion);
    }

    public function updateReplenishment(Reposicion $reposicion): bool {
        if ($reposicion->getId() === null) {
            throw new \InvalidArgumentException("La reposición no tiene un ID válido");
        }
        
        if ($reposicion->getCantidadRepuesta() <= 0) {
            throw new \InvalidArgumentException("La cantidad repuesta debe ser mayor que cero");
        }
        
        return $this->reposicionRepository->update($reposicion);
    }

    public function deleteReplenishment(int $id): bool {
        return $this->reposicionRepository->delete($id);
    }
}
