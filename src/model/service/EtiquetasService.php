<?php

namespace model\service;

use Exception;
use model\entity\Etiquetas;
use model\repository\EtiquetasRepository;
use PDOException;

require_once(__DIR__ . '/../repository/EtiquetasRepository.php');
require_once(__DIR__ . '/../entity/Etiquetas.php');

class EtiquetasService
{
    private EtiquetasRepository $etiquetasRepository;

    public function __construct()
    {
        $this->etiquetasRepository = new EtiquetasRepository();
    }

    public function getAllEtiquetas(): array
    {
        try {
            return $this->etiquetasRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las etiquetas: " . $e->getMessage());
        }
    }

    public function getEtiquetaById(int $id): ?Etiquetas
    {
        try {
            return $this->etiquetasRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la etiqueta con ID {$id}: " . $e->getMessage());
        }
    }

    public function getEtiquetasByProducto(int $idProducto): array
    {
        try {
            return $this->etiquetasRepository->findByProducto($idProducto);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener etiquetas del producto {$idProducto}: " . $e->getMessage());
        }
    }

    public function getEtiquetasByReposicion(int $idReposicion): array
    {
        try {
            return $this->etiquetasRepository->findByReposicion($idReposicion);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener etiquetas de la reposición {$idReposicion}: " . $e->getMessage());
        }
    }

    public function getEtiquetasByTipo(string $tipo): array
    {
        try {
            return $this->etiquetasRepository->findByTipo($tipo);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener etiquetas del tipo {$tipo}: " . $e->getMessage());
        }
    }

    public function getEtiquetasByPrioridad(string $prioridad): array
    {
        try {
            return $this->etiquetasRepository->findByPrioridad($prioridad);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener etiquetas con prioridad {$prioridad}: " . $e->getMessage());
        }
    }

    public function getEtiquetasByImpresa(bool $impresa): array
    {
        try {
            return $this->etiquetasRepository->findByImpresa($impresa);
        } catch (PDOException $e) {
            $estado = $impresa ? "impresas" : "no impresas";
            throw new Exception("Error al obtener etiquetas {$estado}: " . $e->getMessage());
        }
    }

    public function createEtiqueta(
        int $idProducto,
        int $idReposicion,
        string $tipo,
        string $prioridad,
        bool $impresa
    ): bool {
        try {
            $etiqueta = new Etiquetas(
                0,
                $idProducto,
                $idReposicion,
                $tipo,
                $prioridad,
                $impresa
            );
            return $this->etiquetasRepository->save($etiqueta);
        } catch (PDOException $e) {
            throw new Exception("Error al crear la etiqueta: " . $e->getMessage());
        }
    }

    public function updateEtiqueta(
        int $idEtiqueta,
        int $idProducto,
        int $idReposicion,
        string $tipo,
        string $prioridad,
        bool $impresa
    ): bool {
        try {
            $etiquetaExistente = $this->etiquetasRepository->findById($idEtiqueta);
            if (!$etiquetaExistente) {
                throw new Exception("No se encontró la etiqueta con ID: " . $idEtiqueta);
            }
            
            $etiqueta = new Etiquetas(
                $idEtiqueta,
                $idProducto,
                $idReposicion,
                $tipo,
                $prioridad,
                $impresa
            );
            return $this->etiquetasRepository->update($etiqueta);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la etiqueta: " . $e->getMessage());
        }
    }

    public function marcarEtiquetaImpresa(int $idEtiqueta, bool $impresa): bool
    {
        try {
            $etiqueta = $this->etiquetasRepository->findById($idEtiqueta);
            if (!$etiqueta) {
                throw new Exception("No se encontró la etiqueta con ID: " . $idEtiqueta);
            }
            
            $etiqueta->setImpresa($impresa);
            return $this->etiquetasRepository->update($etiqueta);
        } catch (PDOException $e) {
            throw new Exception("Error al marcar la etiqueta como impresa: " . $e->getMessage());
        }
    }

    public function deleteEtiqueta(int $idEtiqueta): bool
    {
        try {
            $etiqueta = $this->etiquetasRepository->findById($idEtiqueta);
            if (!$etiqueta) {
                throw new Exception("No se encontró la etiqueta con ID: " . $idEtiqueta);
            }
            
            return $this->etiquetasRepository->delete($idEtiqueta);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la etiqueta: " . $e->getMessage());
        }
    }
}
