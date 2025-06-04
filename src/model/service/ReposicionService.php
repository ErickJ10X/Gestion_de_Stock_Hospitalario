<?php

namespace model\service;

require_once(__DIR__ . '/../../model/entity/Reposicion.php');
require_once(__DIR__ . '/../../model/repository/ReposicionRepository.php');

use model\entity\Reposicion;
use model\repository\ReposicionRepository;
use DateTime;
use InvalidArgumentException;

class ReposicionService {
    private ReposicionRepository $reposicionRepository;

    public function __construct(ReposicionRepository $reposicionRepository = null) {
        $this->reposicionRepository = $reposicionRepository ?? new ReposicionRepository();
    }

    public function getReposicionById(int $id): ?Reposicion {
        return $this->reposicionRepository->findById($id);
    }

    public function getAllReposiciones(): array {
        return $this->reposicionRepository->findAll();
    }

    /**
     * Obtiene las reposiciones dentro de un rango de fechas
     * 
     * @param DateTime $fechaDesde Fecha inicial
     * @param DateTime $fechaHasta Fecha final
     * @return array Lista de reposiciones
     */
    public function getReposicionesByFechas(DateTime $fechaDesde, DateTime $fechaHasta): array {
        return $this->reposicionRepository->findByFechas($fechaDesde, $fechaHasta);
    }

    public function getReposicionesByAlmacen(int $idAlmacen): array {
        return $this->reposicionRepository->findByAlmacen($idAlmacen);
    }

    public function getReposicionesByBotiquin(int $idBotiquin): array {
        return $this->reposicionRepository->findByBotiquin($idBotiquin);
    }

    public function getReposicionesUrgentes(): array {
        return $this->reposicionRepository->findUrgentes();
    }

    public function crearReposicion(array $data): Reposicion {
        $this->validarReposicionData($data);
        
        $reposicion = new Reposicion(
            null,
            $data['id_producto'],
            $data['desde_almacen'],
            $data['hacia_botiquin'],
            $data['cantidad_repuesta'],
            $data['fecha'] ?? new DateTime(),
            $data['urgente'] ?? false,
            $data['notas'] ?? ''
        );
        
        return $this->reposicionRepository->save($reposicion);
    }

    public function actualizarReposicion(int $id, array $data): Reposicion {
        $reposicion = $this->reposicionRepository->findById($id);
        
        if (!$reposicion) {
            throw new InvalidArgumentException("Reposición no encontrada");
        }
        
        if (isset($data['id_producto'])) {
            $reposicion->setIdProducto($data['id_producto']);
        }
        
        if (isset($data['desde_almacen'])) {
            $reposicion->setDesdeAlmacen($data['desde_almacen']);
        }
        
        if (isset($data['hacia_botiquin'])) {
            $reposicion->setHaciaBotiquin($data['hacia_botiquin']);
        }
        
        if (isset($data['cantidad_repuesta'])) {
            $reposicion->setCantidadRepuesta($data['cantidad_repuesta']);
        }
        
        if (isset($data['fecha'])) {
            $reposicion->setFecha($data['fecha']);
        }
        
        if (isset($data['urgente'])) {
            $reposicion->setUrgente($data['urgente']);
        }
        
        if (isset($data['notas'])) {
            $reposicion->setNotas($data['notas']);
        }
        
        return $this->reposicionRepository->save($reposicion);
    }

    public function eliminarReposicion(int $id): bool {
        return $this->reposicionRepository->delete($id);
    }

    private function validarReposicionData(array $data): void {
        if (!isset($data['id_producto']) || empty($data['id_producto'])) {
            throw new InvalidArgumentException("El producto es obligatorio");
        }
        
        if (!isset($data['desde_almacen']) || empty($data['desde_almacen'])) {
            throw new InvalidArgumentException("El almacén de origen es obligatorio");
        }
        
        if (!isset($data['hacia_botiquin']) || empty($data['hacia_botiquin'])) {
            throw new InvalidArgumentException("El botiquín de destino es obligatorio");
        }
        
        if (!isset($data['cantidad_repuesta']) || $data['cantidad_repuesta'] <= 0) {
            throw new InvalidArgumentException("La cantidad debe ser mayor que cero");
        }
    }
}
