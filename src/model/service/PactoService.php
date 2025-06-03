<?php

namespace model\service;

use model\entity\Pacto;
use model\repository\PactoRepository;
use InvalidArgumentException;

class PactoService {
    private PactoRepository $pactoRepository;

    public function __construct(PactoRepository $pactoRepository = null) {
        $this->pactoRepository = $pactoRepository ?? new PactoRepository();
    }

    public function getPactoById(int $id): ?Pacto {
        return $this->pactoRepository->findById($id);
    }

    public function getAllPactos(): array {
        return $this->pactoRepository->findAll();
    }

    public function getPactosByProducto(int $idProducto): array {
        return $this->pactoRepository->findByProducto($idProducto);
    }

    public function getPactosByDestino(string $tipoUbicacion, int $idDestino): array {
        if (!in_array($tipoUbicacion, ['Planta', 'Botiquin'])) {
            throw new InvalidArgumentException('El tipo de ubicación debe ser Planta o Botiquin');
        }
        return $this->pactoRepository->findByDestino($tipoUbicacion, $idDestino);
    }

    public function createPacto(array $data): Pacto {
        $this->validatePactoData($data);
        
        $pacto = new Pacto();
        $pacto->setIdProducto($data['id_producto'])
              ->setTipoUbicacion($data['tipo_ubicacion'])
              ->setIdDestino($data['id_destino'])
              ->setCantidadPactada($data['cantidad_pactada'])
              ->setActivo($data['activo'] ?? true);
        
        return $this->pactoRepository->save($pacto);
    }

    public function updatePacto(int $id, array $data): Pacto {
        $pacto = $this->pactoRepository->findById($id);
        if (!$pacto) {
            throw new InvalidArgumentException('Pacto no encontrado');
        }
        
        if (isset($data['id_producto'])) {
            $pacto->setIdProducto($data['id_producto']);
        }
        
        if (isset($data['tipo_ubicacion'])) {
            $pacto->setTipoUbicacion($data['tipo_ubicacion']);
        }
        
        if (isset($data['id_destino'])) {
            $pacto->setIdDestino($data['id_destino']);
        }
        
        if (isset($data['cantidad_pactada'])) {
            $pacto->setCantidadPactada($data['cantidad_pactada']);
        }
        
        if (isset($data['activo'])) {
            $pacto->setActivo($data['activo']);
        }
        
        return $this->pactoRepository->save($pacto);
    }

    public function deletePacto(int $id): bool {
        return $this->pactoRepository->delete($id);
    }

    public function desactivarPacto(int $id): bool {
        return $this->pactoRepository->softDelete($id);
    }

    public function verificarNecesidadReposicion(int $idProducto, string $tipoUbicacion, int $idDestino, int $cantidadDisponible): bool {
        $pactos = $this->pactoRepository->findByDestino($tipoUbicacion, $idDestino);
        
        foreach ($pactos as $pacto) {
            if ($pacto->getIdProducto() == $idProducto && $pacto->isActivo()) {
                // Si la cantidad disponible es menor que la cantidad pactada, se necesita reposición
                if ($cantidadDisponible < $pacto->getCantidadPactada()) {
                    return true;
                }
            }
        }
        
        return false;
    }

    public function getCantidadPactada(int $idProducto, string $tipoUbicacion, int $idDestino): int {
        $pactos = $this->pactoRepository->findByDestino($tipoUbicacion, $idDestino);
        
        foreach ($pactos as $pacto) {
            if ($pacto->getIdProducto() == $idProducto && $pacto->isActivo()) {
                return $pacto->getCantidadPactada();
            }
        }
        
        return 0; // No hay pacto para este producto en este destino
    }

    private function validatePactoData(array $data): void {
        if (!isset($data['id_producto'])) {
            throw new InvalidArgumentException('El producto es obligatorio');
        }
        
        if (!isset($data['tipo_ubicacion']) || !in_array($data['tipo_ubicacion'], ['Planta', 'Botiquin'])) {
            throw new InvalidArgumentException('El tipo de ubicación es obligatorio y debe ser Planta o Botiquin');
        }
        
        if (!isset($data['id_destino'])) {
            throw new InvalidArgumentException('El destino es obligatorio');
        }
        
        if (!isset($data['cantidad_pactada'])) {
            throw new InvalidArgumentException('La cantidad pactada es obligatoria');
        }
        
        if (isset($data['cantidad_pactada']) && $data['cantidad_pactada'] <= 0) {
            throw new InvalidArgumentException('La cantidad pactada debe ser mayor que cero');
        }
    }
}
