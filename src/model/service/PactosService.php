<?php

namespace model\service;

use Exception;
use model\entity\Pactos;
use model\repository\PactosRepository;
use PDOException;

require_once(__DIR__ . '/../repository/PactosRepository.php');
require_once(__DIR__ . '/../entity/Pactos.php');

class PactosService
{
    private PactosRepository $pactosRepository;

    public function __construct()
    {
        $this->pactosRepository = new PactosRepository();
    }

    public function getAllPactos(): array
    {
        try {
            return $this->pactosRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los pactos: " . $e->getMessage());
        }
    }

    public function getPactoById(int $id): ?Pactos
    {
        try {
            return $this->pactosRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el pacto con ID {$id}: " . $e->getMessage());
        }
    }

    public function getPactosByProducto(int $idProducto): array
    {
        try {
            return $this->pactosRepository->findByIdProducto($idProducto);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener pactos del producto {$idProducto}: " . $e->getMessage());
        }
    }

    public function getPactosByTipoUbicacion(string $tipoUbicacion): array
    {
        try {
            return $this->pactosRepository->findByTipoUbicacion($tipoUbicacion);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener pactos del tipo de ubicación {$tipoUbicacion}: " . $e->getMessage());
        }
    }

    public function getPactosByDestino(int $idDestino): array
    {
        try {
            return $this->pactosRepository->findByIdDestino($idDestino);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener pactos del destino {$idDestino}: " . $e->getMessage());
        }
    }

    public function getPactosByCantidadPactada(int $cantidadPactada): array
    {
        try {
            return $this->pactosRepository->findByCantidadPactada($cantidadPactada);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener pactos con cantidad pactada {$cantidadPactada}: " . $e->getMessage());
        }
    }

    public function createPacto(
        int $idProducto,
        string $tipoUbicacion,
        int $idDestino,
        int $cantidadPactada
    ): bool {
        try {
            $pacto = new Pactos(0, $idProducto, $tipoUbicacion, $idDestino, $cantidadPactada);
            return $this->pactosRepository->save($pacto);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el pacto: " . $e->getMessage());
        }
    }

    public function updatePacto(
        int $idPacto,
        int $idProducto,
        string $tipoUbicacion,
        int $idDestino,
        int $cantidadPactada
    ): bool {
        try {
            $pactoExistente = $this->pactosRepository->findById($idPacto);
            if (!$pactoExistente) {
                throw new Exception("No se encontró el pacto con ID: " . $idPacto);
            }
            
            $pacto = new Pactos($idPacto, $idProducto, $tipoUbicacion, $idDestino, $cantidadPactada);
            return $this->pactosRepository->update($pacto);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el pacto: " . $e->getMessage());
        }
    }

    public function deletePacto(int $idPacto): bool
    {
        try {
            $pacto = $this->pactosRepository->findById($idPacto);
            if (!$pacto) {
                throw new Exception("No se encontró el pacto con ID: " . $idPacto);
            }
            
            return $this->pactosRepository->delete($idPacto);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el pacto: " . $e->getMessage());
        }
    }
}
