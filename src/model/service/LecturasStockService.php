<?php

namespace model\service;

use Exception;
use model\entity\Lecturas_stock;
use model\repository\LecturaRepository;
use PDOException;

require_once(__DIR__ . '/../repository/LecturaRepository.php');
require_once(__DIR__ . '/../entity/Lecturas_stock.php');

class LecturasStockService
{
    private LecturaRepository $lecturaRepository;

    public function __construct()
    {
        $this->lecturaRepository = new LecturaRepository();
    }

    public function getAllLecturas(): array
    {
        try {
            return $this->lecturaRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las lecturas de stock: " . $e->getMessage());
        }
    }

    public function getLecturaById(int $id): ?Lecturas_stock
    {
        try {
            return $this->lecturaRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la lectura con ID {$id}: " . $e->getMessage());
        }
    }

    public function getLecturasByProducto(int $idProducto): array
    {
        try {
            return $this->lecturaRepository->findByProducto($idProducto);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener lecturas del producto {$idProducto}: " . $e->getMessage());
        }
    }

    public function getLecturasByBotiquin(int $idBotiquin): array
    {
        try {
            return $this->lecturaRepository->findByBotiquin($idBotiquin);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener lecturas del botiquín {$idBotiquin}: " . $e->getMessage());
        }
    }

    public function getLecturasByRegistrador(int $idUsuario): array
    {
        try {
            return $this->lecturaRepository->findByRegistrador($idUsuario);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener lecturas registradas por el usuario {$idUsuario}: " . $e->getMessage());
        }
    }

    public function createLectura(
        int    $idProducto,
        int    $idBotiquin,
        int    $cantidadDisponible,
        string $fechaLectura,
        int    $registradoPor
    ): bool
    {
        try {
            $lectura = new Lecturas_stock(
                0,
                $idProducto,
                $idBotiquin,
                $cantidadDisponible,
                $fechaLectura,
                $registradoPor
            );
            return $this->lecturaRepository->save($lectura);
        } catch (PDOException $e) {
            throw new Exception("Error al crear la lectura de stock: " . $e->getMessage());
        }
    }

    public function updateLectura(
        int    $idLectura,
        int    $idProducto,
        int    $idBotiquin,
        int    $cantidadDisponible,
        string $fechaLectura,
        int    $registradoPor
    ): bool
    {
        try {
            $lecturaExistente = $this->lecturaRepository->findById($idLectura);
            if (!$lecturaExistente) {
                throw new Exception("No se encontró la lectura con ID: " . $idLectura);
            }

            $lectura = new Lecturas_stock(
                $idLectura,
                $idProducto,
                $idBotiquin,
                $cantidadDisponible,
                $fechaLectura,
                $registradoPor
            );
            return $this->lecturaRepository->update($lectura);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la lectura de stock: " . $e->getMessage());
        }
    }

    public function deleteLectura(int $idLectura): bool
    {
        try {
            $lectura = $this->lecturaRepository->findById($idLectura);
            if (!$lectura) {
                throw new Exception("No se encontró la lectura con ID: " . $idLectura);
            }

            return $this->lecturaRepository->delete($idLectura);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la lectura de stock: " . $e->getMessage());
        }
    }
}
