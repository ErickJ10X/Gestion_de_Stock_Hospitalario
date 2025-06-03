<?php

namespace model\service;

use model\entity\Reposicion;
use model\repository\ReposicionRepository;
use model\repository\ProductoRepository;
use model\repository\AlmacenRepository;
use model\repository\BotiquinRepository;
use model\repository\EtiquetaRepository;
use DateTime;
use InvalidArgumentException;

class ReposicionService {
    private ReposicionRepository $reposicionRepository;
    private ?ProductoRepository $productoRepository;
    private ?AlmacenRepository $almacenRepository;
    private ?BotiquinRepository $botiquinRepository;
    private ?EtiquetaRepository $etiquetaRepository;

    public function __construct(
        ReposicionRepository $reposicionRepository = null,
        ProductoRepository $productoRepository = null,
        AlmacenRepository $almacenRepository = null,
        BotiquinRepository $botiquinRepository = null,
        EtiquetaRepository $etiquetaRepository = null
    ) {
        $this->reposicionRepository = $reposicionRepository ?? new ReposicionRepository();
        $this->productoRepository = $productoRepository ?? new ProductoRepository();
        $this->almacenRepository = $almacenRepository ?? new AlmacenRepository();
        $this->botiquinRepository = $botiquinRepository ?? new BotiquinRepository();
        $this->etiquetaRepository = $etiquetaRepository ?? new EtiquetaRepository();
    }

    public function getReposicionById(int $id): ?Reposicion {
        return $this->reposicionRepository->findById($id);
    }

    public function getAllReposiciones(): array {
        return $this->reposicionRepository->findAll();
    }

    public function getReposicionesByProducto(int $idProducto): array {
        return $this->reposicionRepository->findByProducto($idProducto);
    }

    public function getReposicionesByBotiquin(int $idBotiquin): array {
        return $this->reposicionRepository->findByBotiquin($idBotiquin);
    }

    public function getReposicionesByAlmacen(int $idAlmacen): array {
        return $this->reposicionRepository->findByAlmacen($idAlmacen);
    }

    public function getReposicionesUrgentes(): array {
        return $this->reposicionRepository->findUrgentes();
    }

    public function crearReposicion(array $data): Reposicion {
        $this->validateReposicionData($data);
        
        $fecha = isset($data['fecha']) 
            ? (is_string($data['fecha']) ? new DateTime($data['fecha']) : $data['fecha']) 
            : new DateTime();

        $reposicion = new Reposicion(
            $data['id_producto'],
            $data['desde_almacen'],
            $data['hacia_botiquin'],
            $data['cantidad_repuesta'],
            $fecha,
            $data['urgente'] ?? false
        );
        
        $reposicion = $this->reposicionRepository->save($reposicion);
        
        // Si hay un servicio de etiquetas disponible, se podrían generar las etiquetas automáticamente
        if ($this->etiquetaRepository && isset($data['generar_etiquetas']) && $data['generar_etiquetas']) {
            // Implementación para generar etiquetas
        }
        
        return $reposicion;
    }

    public function actualizarReposicion(int $id, array $data): Reposicion {
        $reposicion = $this->reposicionRepository->findById($id);
        if (!$reposicion) {
            throw new InvalidArgumentException('Reposición no encontrada');
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
            $fecha = is_string($data['fecha']) ? new DateTime($data['fecha']) : $data['fecha'];
            $reposicion->setFecha($fecha);
        }
        
        if (isset($data['urgente'])) {
            $reposicion->setUrgente($data['urgente']);
        }
        
        return $this->reposicionRepository->save($reposicion);
    }

    public function eliminarReposicion(int $id): bool {
        // Se podrían agregar validaciones adicionales antes de eliminar
        return $this->reposicionRepository->delete($id);
    }

    private function validateReposicionData(array $data): void {
        if (!isset($data['id_producto'])) {
            throw new InvalidArgumentException('El producto es obligatorio');
        }
        
        if (!isset($data['desde_almacen'])) {
            throw new InvalidArgumentException('El almacén origen es obligatorio');
        }
        
        if (!isset($data['hacia_botiquin'])) {
            throw new InvalidArgumentException('El botiquín destino es obligatorio');
        }
        
        if (!isset($data['cantidad_repuesta']) || $data['cantidad_repuesta'] <= 0) {
            throw new InvalidArgumentException('La cantidad repuesta debe ser mayor que cero');
        }
        
        // Validaciones adicionales si hay repositorios disponibles
        if ($this->productoRepository && !$this->productoRepository->findById($data['id_producto'])) {
            throw new InvalidArgumentException('El producto especificado no existe');
        }
        
        if ($this->almacenRepository && !$this->almacenRepository->findById($data['desde_almacen'])) {
            throw new InvalidArgumentException('El almacén especificado no existe');
        }
        
        if ($this->botiquinRepository && !$this->botiquinRepository->findById($data['hacia_botiquin'])) {
            throw new InvalidArgumentException('El botiquín especificado no existe');
        }
    }
}
