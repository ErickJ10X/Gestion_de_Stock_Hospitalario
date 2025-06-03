<?php

namespace model\service;

use model\entity\Etiqueta;
use model\repository\EtiquetaRepository;
use model\repository\ReposicionRepository;
use InvalidArgumentException;

class EtiquetaService {
    private EtiquetaRepository $etiquetaRepository;
    
    public function __construct(EtiquetaRepository $etiquetaRepository = null) {
        $this->etiquetaRepository = $etiquetaRepository ?? new EtiquetaRepository();
    }
    
    public function getEtiquetaById(int $id): ?Etiqueta {
        return $this->etiquetaRepository->findById($id);
    }
    
    public function getAllEtiquetas(): array {
        return $this->etiquetaRepository->findAll();
    }
    
    public function getEtiquetasByReposicion(int $idReposicion): array {
        return $this->etiquetaRepository->findByReposicion($idReposicion);
    }
    
    public function getEtiquetasNoImpresas(): array {
        return $this->etiquetaRepository->findNoImpresas();
    }
    
    public function createEtiqueta(array $data): Etiqueta {
        $this->validateEtiquetaData($data);
        
        $etiqueta = new Etiqueta(
            $data['id_producto'],
            $data['id_reposicion'],
            $data['tipo'],
            $data['prioridad'],
            $data['impresa'] ?? false
        );
        
        return $this->etiquetaRepository->save($etiqueta);
    }
    
    public function updateEtiqueta(int $id, array $data): Etiqueta {
        $etiqueta = $this->etiquetaRepository->findById($id);
        if (!$etiqueta) {
            throw new InvalidArgumentException('Etiqueta no encontrada');
        }
        
        if (isset($data['id_producto'])) {
            $etiqueta->setIdProducto($data['id_producto']);
        }
        
        if (isset($data['id_reposicion'])) {
            $etiqueta->setIdReposicion($data['id_reposicion']);
        }
        
        if (isset($data['tipo'])) {
            $etiqueta->setTipo($data['tipo']);
        }
        
        if (isset($data['prioridad'])) {
            $etiqueta->setPrioridad($data['prioridad']);
        }
        
        if (isset($data['impresa'])) {
            $etiqueta->setImpresa($data['impresa']);
        }
        
        return $this->etiquetaRepository->save($etiqueta);
    }
    
    public function marcarComoImpresa(int $id): bool {
        return $this->etiquetaRepository->marcarComoImpresa($id);
    }
    
    public function deleteEtiqueta(int $id): bool {
        return $this->etiquetaRepository->delete($id);
    }
    
    public function generarEtiquetasDesdeReposicion(int $idReposicion, string $tipo = 'Informativa'): array {
        // Nota: Este método requeriría una implementación más específica según las reglas de negocio
        // Por ejemplo, podría consultar una reposición y generar las etiquetas según la cantidad
        $etiquetas = [];
        
        // Aquí se implementaría la lógica para generar etiquetas a partir de una reposición
        // Por ahora, solo devolveremos un array vacío como ejemplo
        
        return $etiquetas;
    }
    
    private function validateEtiquetaData(array $data): void {
        if (!isset($data['id_producto'])) {
            throw new InvalidArgumentException('El producto es obligatorio');
        }
        
        if (!isset($data['id_reposicion'])) {
            throw new InvalidArgumentException('La reposición es obligatoria');
        }
        
        if (!isset($data['tipo']) || !in_array($data['tipo'], ['Informativa', 'RFID'])) {
            throw new InvalidArgumentException('El tipo de etiqueta es obligatorio y debe ser Informativa o RFID');
        }
        
        if (!isset($data['prioridad']) || !in_array($data['prioridad'], ['Normal', 'Urgente'])) {
            throw new InvalidArgumentException('La prioridad es obligatoria y debe ser Normal o Urgente');
        }
    }
}
