<?php

namespace model\service;

use model\entity\LecturaStock;
use model\repository\LecturaStockRepository;
use model\repository\BotiquinRepository;
use model\repository\ProductoRepository;
use InvalidArgumentException;
use DateTime;

class LecturaStockService {
    private LecturaStockRepository $lecturaRepository;
    private ?BotiquinRepository $botiquinRepository;
    private ?ProductoRepository $productoRepository;

    public function __construct(
        LecturaStockRepository $lecturaRepository = null,
        BotiquinRepository $botiquinRepository = null,
        ProductoRepository $productoRepository = null
    ) {
        $this->lecturaRepository = $lecturaRepository ?? new LecturaStockRepository();
        $this->botiquinRepository = $botiquinRepository;
        $this->productoRepository = $productoRepository;
    }

    public function getLecturaById(int $id): ?LecturaStock {
        return $this->lecturaRepository->findById($id);
    }

    public function getAllLecturas(): array {
        return $this->lecturaRepository->findAll();
    }

    public function getLecturasByBotiquin(int $idBotiquin): array {
        return $this->lecturaRepository->findByBotiquin($idBotiquin);
    }

    public function getLecturasByProducto(int $idProducto): array {
        return $this->lecturaRepository->findByProducto($idProducto);
    }

    public function getUltimasLecturasPorBotiquin(int $idBotiquin): array {
        return $this->lecturaRepository->findLatestForBotiquin($idBotiquin);
    }

    public function registrarLectura(array $data): LecturaStock {
        $this->validateLecturaData($data);
        
        $lectura = new LecturaStock();
        $lectura->setIdProducto($data['id_producto']);
        $lectura->setIdBotiquin($data['id_botiquin']);
        $lectura->setCantidadDisponible($data['cantidad_disponible']);
        
        if (isset($data['fecha_lectura'])) {
            if ($data['fecha_lectura'] instanceof DateTime) {
                $lectura->setFechaLectura($data['fecha_lectura']);
            } else {
                $lectura->setFechaLectura(new DateTime($data['fecha_lectura']));
            }
        }
        
        $lectura->setRegistradoPor($data['registrado_por']);
        
        return $this->lecturaRepository->save($lectura);
    }

    public function eliminarLectura(int $id): bool {
        return $this->lecturaRepository->delete($id);
    }

    public function analizarInventario(int $idBotiquin): array {
        $ultimasLecturas = $this->lecturaRepository->findLatestForBotiquin($idBotiquin);
        $resultados = [
            'productos_bajos' => [],
            'productos_agotados' => [],
            'productos_disponibles' => []
        ];
        
        // Aquí implementarías la lógica para analizar las lecturas
        // Este es un ejemplo simplificado
        foreach ($ultimasLecturas as $lectura) {
            // Si hay una función para obtener el nivel pactado, se usaría aquí para comparar
            if ($lectura->getCantidadDisponible() <= 0) {
                $resultados['productos_agotados'][] = $lectura;
            } else if ($lectura->getCantidadDisponible() < 3) { // Valor de ejemplo
                $resultados['productos_bajos'][] = $lectura;
            } else {
                $resultados['productos_disponibles'][] = $lectura;
            }
        }
        
        return $resultados;
    }

    private function validateLecturaData(array $data): void {
        if (!isset($data['id_producto'])) {
            throw new InvalidArgumentException('El producto es obligatorio');
        }
        
        if (!isset($data['id_botiquin'])) {
            throw new InvalidArgumentException('El botiquín es obligatorio');
        }
        
        if (!isset($data['cantidad_disponible'])) {
            throw new InvalidArgumentException('La cantidad disponible es obligatoria');
        }
        
        if (!isset($data['registrado_por'])) {
            throw new InvalidArgumentException('El usuario que registra es obligatorio');
        }
        
        // Validaciones adicionales podrían incluir verificación de existencia real de producto y botiquín
        if ($this->productoRepository && !$this->productoRepository->findById($data['id_producto'])) {
            throw new InvalidArgumentException('El producto especificado no existe');
        }
        
        if ($this->botiquinRepository && !$this->botiquinRepository->findById($data['id_botiquin'])) {
            throw new InvalidArgumentException('El botiquín especificado no existe');
        }
    }
}
