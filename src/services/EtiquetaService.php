<?php

namespace services;

use repositories\interfaces\EtiquetaRepositoryInterface;
use repositories\interfaces\ReposicionRepositoryInterface;
use services\interfaces\EtiquetaServiceInterface;
use models\Etiqueta;

class EtiquetaService implements EtiquetaServiceInterface {
    private EtiquetaRepositoryInterface $etiquetaRepository;
    private ReposicionRepositoryInterface $reposicionRepository;

    public function __construct(
        EtiquetaRepositoryInterface $etiquetaRepository,
        ReposicionRepositoryInterface $reposicionRepository
    ) {
        $this->etiquetaRepository = $etiquetaRepository;
        $this->reposicionRepository = $reposicionRepository;
    }

    public function getLabelById(int $id): ?Etiqueta {
        return $this->etiquetaRepository->findById($id);
    }

    public function getAllLabels(): array {
        return $this->etiquetaRepository->findAll();
    }

    public function getLabelsByReplenishment(int $idReposicion): array {
        return $this->etiquetaRepository->findByReposicion($idReposicion);
    }

    public function getLabelsByProduct(int $idProducto): array {
        return $this->etiquetaRepository->findByProducto($idProducto);
    }

    public function getLabelsByType(string $tipo): array {
        if (!in_array($tipo, ['Informativa', 'RFID'])) {
            throw new \InvalidArgumentException("Tipo de etiqueta no válido");
        }
        return $this->etiquetaRepository->findByTipo($tipo);
    }

    public function getLabelsByPriority(string $prioridad): array {
        if (!in_array($prioridad, ['Normal', 'Urgente'])) {
            throw new \InvalidArgumentException("Prioridad no válida");
        }
        return $this->etiquetaRepository->findByPrioridad($prioridad);
    }

    public function getUnprintedLabels(): array {
        return $this->etiquetaRepository->findNoImpresas();
    }

    public function createLabel(Etiqueta $etiqueta): int {
        return $this->etiquetaRepository->save($etiqueta);
    }

    public function updateLabel(Etiqueta $etiqueta): bool {
        if ($etiqueta->getId() === null) {
            throw new \InvalidArgumentException("La etiqueta no tiene un ID válido");
        }
        
        return $this->etiquetaRepository->update($etiqueta);
    }

    public function markLabelAsPrinted(int $id): bool {
        $etiqueta = $this->etiquetaRepository->findById($id);
        if (!$etiqueta) {
            return false;
        }
        
        $etiqueta->setImpresa(true);
        return $this->etiquetaRepository->update($etiqueta);
    }

    public function deleteLabel(int $id): bool {
        return $this->etiquetaRepository->delete($id);
    }

    public function generateLabelsForReplenishment(int $idReposicion): array {
        $reposicion = $this->reposicionRepository->findById($idReposicion);
        if (!$reposicion) {
            throw new \InvalidArgumentException("Reposición no encontrada");
        }
        
        $etiquetas = [];
        $prioridad = $reposicion->isUrgente() ? 'Urgente' : 'Normal';
        
        // Crear etiqueta informativa
        $etiquetaInformativa = new Etiqueta(
            $reposicion->getIdProducto(), 
            $idReposicion, 
            'Informativa', 
            $prioridad
        );
        $idEtiquetaInformativa = $this->etiquetaRepository->save($etiquetaInformativa);
        $etiquetaInformativa->setId($idEtiquetaInformativa);
        $etiquetas[] = $etiquetaInformativa;
        
        // Crear etiqueta RFID
        $etiquetaRFID = new Etiqueta(
            $reposicion->getIdProducto(), 
            $idReposicion, 
            'RFID', 
            $prioridad
        );
        $idEtiquetaRFID = $this->etiquetaRepository->save($etiquetaRFID);
        $etiquetaRFID->setId($idEtiquetaRFID);
        $etiquetas[] = $etiquetaRFID;
        
        return $etiquetas;
    }
}
