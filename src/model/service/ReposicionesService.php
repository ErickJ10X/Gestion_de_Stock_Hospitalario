<?php

namespace model\service;

use Exception;
use model\entity\Reposiciones;
use model\repository\ReposicionesRepository;
use PDOException;

require_once(__DIR__ . '/../repository/ReposicionesRepository.php');
require_once(__DIR__ . '/../entity/Reposiciones.php');

class ReposicionesService
{
    private ReposicionesRepository $reposicionesRepository;

    public function __construct()
    {
        $this->reposicionesRepository = new ReposicionesRepository();
    }

    public function getAllReposiciones(): array
    {
        try {
            return $this->reposicionesRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las reposiciones: " . $e->getMessage());
        }
    }

    public function getReposicionById(int $id): ?Reposiciones
    {
        try {
            return $this->reposicionesRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la reposición con ID {$id}: " . $e->getMessage());
        }
    }

    public function getReposicionesByProducto(int $idProducto): array
    {
        try {
            return $this->reposicionesRepository->findByIdProducto($idProducto);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reposiciones del producto {$idProducto}: " . $e->getMessage());
        }
    }

    public function getReposicionesByBotiquin(int $idBotiquin): array
    {
        try {
            return $this->reposicionesRepository->findByIdBotiquin($idBotiquin);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reposiciones del botiquín {$idBotiquin}: " . $e->getMessage());
        }
    }

    public function getReposicionesByAlmacen(int $idAlmacen): array
    {
        try {
            return $this->reposicionesRepository->findByIdAlmacen($idAlmacen);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reposiciones del almacén {$idAlmacen}: " . $e->getMessage());
        }
    }

    public function getReposicionesByUrgente(bool $urgente): array
    {
        try {
            return $this->reposicionesRepository->findByUrgente($urgente);
        } catch (PDOException $e) {
            $estado = $urgente ? "urgentes" : "no urgentes";
            throw new Exception("Error al obtener reposiciones {$estado}: " . $e->getMessage());
        }
    }

    public function createReposicion(
        int $idProducto,
        int $desdeAlmacen,
        int $hastaBotiquin,
        int $cantidadRepuesta,
        string $fecha,
        bool $urgente
    ): bool {
        try {
            // El ID se generará automáticamente (autoincremental)
            $reposicion = new Reposiciones(
                0, 
                $idProducto, 
                $desdeAlmacen, 
                $hastaBotiquin, 
                $cantidadRepuesta, 
                $fecha, 
                $urgente
            );
            return $this->reposicionesRepository->save($reposicion);
        } catch (PDOException $e) {
            throw new Exception("Error al crear la reposición: " . $e->getMessage());
        }
    }

    public function updateReposicion(
        int $idReposicion,
        int $idProducto,
        int $desdeAlmacen,
        int $hastaBotiquin,
        int $cantidadRepuesta,
        string $fecha,
        bool $urgente
    ): bool {
        try {
            // Verificamos que exista la reposición
            $reposicionExistente = $this->reposicionesRepository->findById($idReposicion);
            if (!$reposicionExistente) {
                throw new Exception("No se encontró la reposición con ID: " . $idReposicion);
            }
            
            $reposicion = new Reposiciones(
                $idReposicion, 
                $idProducto, 
                $desdeAlmacen, 
                $hastaBotiquin, 
                $cantidadRepuesta, 
                $fecha, 
                $urgente
            );
            return $this->reposicionesRepository->update($reposicion);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la reposición: " . $e->getMessage());
        }
    }

    public function deleteReposicion(int $idReposicion): bool
    {
        try {
            // Verificamos que exista la reposición
            $reposicion = $this->reposicionesRepository->findById($idReposicion);
            if (!$reposicion) {
                throw new Exception("No se encontró la reposición con ID: " . $idReposicion);
            }
            
            return $this->reposicionesRepository->delete($idReposicion);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la reposición: " . $e->getMessage());
        }
    }
}
