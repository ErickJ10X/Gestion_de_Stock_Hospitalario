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
        try {
            return $this->reposicionRepository->findAll();
        } catch (\Exception $e) {
            error_log("Error al obtener todas las reposiciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las reposiciones dentro de un rango de fechas
     *
     * @param DateTime $fechaDesde Fecha inicial
     * @param DateTime $fechaHasta Fecha final
     * @return array Lista de reposiciones
     */
    public function getReposicionesByFechas(DateTime $fechaDesde, DateTime $fechaHasta): array {
        try {
            return $this->reposicionRepository->findByFechas($fechaDesde, $fechaHasta);
        } catch (\Exception $e) {
            error_log("Error al obtener reposiciones por fechas: " . $e->getMessage());
            return [];
        }
    }

    public function getReposicionesByAlmacen(int $idAlmacen): array {
        try {
            return $this->reposicionRepository->findByAlmacen($idAlmacen);
        } catch (\Exception $e) {
            error_log("Error al obtener reposiciones por almacén: " . $e->getMessage());
            return [];
        }
    }

    public function getReposicionesByBotiquin(int $idBotiquin): array {
        try {
            return $this->reposicionRepository->findByBotiquin($idBotiquin);
        } catch (\Exception $e) {
            error_log("Error al obtener reposiciones por botiquín: " . $e->getMessage());
            return [];
        }
    }

    public function getReposicionesUrgentes(): array {
        try {
            return $this->reposicionRepository->findUrgentes();
        } catch (\Exception $e) {
            error_log("Error al obtener reposiciones urgentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las reposiciones por estado
     *
     * @param bool $estado Estado de las reposiciones
     * @return array Lista de reposiciones
     */
    public function getReposicionesByEstado(bool $estado): array {
        try {
            return $this->reposicionRepository->findByEstado($estado);
        } catch (\Exception $e) {
            error_log("Error al obtener reposiciones por estado: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las reposiciones pendientes
     *
     * @return array Lista de reposiciones pendientes
     */
    public function getReposicionesPendientes(): array {
        return $this->getReposicionesByEstado(false);
    }

    /**
     * Obtiene las reposiciones completadas
     *
     * @return array Lista de reposiciones completadas
     */
    public function getReposicionesCompletadas(): array {
        return $this->getReposicionesByEstado(true);
    }

    public function crearReposicion(array $data): Reposicion {
        $this->validarReposicionData($data);

        // Convertir los datos a los tipos correctos
        $idProducto = (int)$data['id_producto'];
        $desdeAlmacen = (int)$data['desde_almacen'];
        $haciaBotiquin = (int)$data['hacia_botiquin'];
        $cantidadRepuesta = (int)$data['cantidad_repuesta'];
        $fecha = $data['fecha'] ?? new DateTime();
        $estado = $data['estado'] ?? false;
        $urgente = $data['urgente'] ?? false;
        $notas = $data['notas'] ?? '';

        // Crear la reposición
        $reposicion = new Reposicion(
            $idProducto,
            $desdeAlmacen,
            $haciaBotiquin,
            $cantidadRepuesta,
            $fecha,
            $estado,
            $urgente,
            $notas
        );

        return $this->reposicionRepository->save($reposicion);
    }

    public function actualizarReposicion(int $id, array $data): Reposicion {
        $reposicion = $this->reposicionRepository->findById($id);

        if (!$reposicion) {
            throw new InvalidArgumentException("Reposición no encontrada");
        }

        if (isset($data['id_producto'])) {
            $reposicion->setIdProducto((int)$data['id_producto']);
        }

        if (isset($data['desde_almacen'])) {
            $reposicion->setDesdeAlmacen((int)$data['desde_almacen']);
        }

        if (isset($data['hacia_botiquin'])) {
            $reposicion->setHaciaBotiquin((int)$data['hacia_botiquin']);
        }

        if (isset($data['cantidad_repuesta'])) {
            $reposicion->setCantidadRepuesta((int)$data['cantidad_repuesta']);
        }

        if (isset($data['fecha'])) {
            $reposicion->setFecha($data['fecha']);
        }

        if (isset($data['urgente'])) {
            $reposicion->setUrgente((bool)$data['urgente']);
        }

        if (isset($data['notas'])) {
            $reposicion->setNotas((string)$data['notas']);
        }

        // Asegurarnos de que el estado se actualiza correctamente
        if (isset($data['estado'])) {
            $nuevoEstado = (bool)$data['estado'];
            $reposicion->setEstado($nuevoEstado);
            // Para depuración
            error_log("Actualizando estado de reposición ID $id a " . ($nuevoEstado ? "ENTREGADO(true)" : "PENDIENTE(false)"));
        }

        $reposicionActualizada = $this->reposicionRepository->save($reposicion);

        // Para depuración - verificar el estado después de guardar
        error_log("Estado después de actualización: " . ($reposicionActualizada->getEstado() ? "ENTREGADO(true)" : "PENDIENTE(false)"));

        return $reposicionActualizada;
    }

    /**
     * Cambia el estado de una reposición
     *
     * @param int $id ID de la reposición
     * @param bool $estado Nuevo estado
     * @return Reposicion Reposición actualizada
     */
    public function cambiarEstadoReposicion(int $id, bool $estado): Reposicion {
        $reposicion = $this->reposicionRepository->findById($id);

        if (!$reposicion) {
            throw new InvalidArgumentException("Reposición no encontrada");
        }

        // Registrar el estado actual antes del cambio para depuración
        $estadoAnterior = $reposicion->getEstado();
        error_log("Cambiando estado de reposición ID $id de " .
            ($estadoAnterior ? "ENTREGADO(true)" : "PENDIENTE(false)") .
            " a " .
            ($estado ? "ENTREGADO(true)" : "PENDIENTE(false)"));

        $reposicion->setEstado($estado);
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