<?php

namespace model\service;

use Exception;
use model\entity\Almacenes;
use model\repository\AlmacenesRepository;
use PDOException;

require_once(__DIR__ . '/../repository/AlmacenesRepository.php');
require_once(__DIR__ . '/../entity/Almacenes.php');

class AlmacenesService
{
    private AlmacenesRepository $almacenesRepository;

    public function __construct()
    {
        $this->almacenesRepository = new AlmacenesRepository();
    }

    public function getAllAlmacenes(): array
    {
        try {
            return $this->almacenesRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al cargar los almacenes: " . $e->getMessage());
        }
    }

    public function getAlmacenById($id): ?Almacenes
    {
        try {
            return $this->almacenesRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al cargar el almacén: " . $e->getMessage());
        }
    }

    public function deleteAlmacen($id): bool
    {
        try {
            return $this->almacenesRepository->delete($id);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el almacén: " . $e->getMessage());
        }
    }

    public function updateAlmacen($id, $planta_id, $tipo, $id_hospital): bool
    {
        try {
            $almacenExistente = $this->almacenesRepository->findById($id);

            if (!$almacenExistente) {
                throw new Exception("No se encontró el almacén con ID: " . $id);
            }

            $almacen = new Almacenes($id, $planta_id, $tipo, $id_hospital);
            return $this->almacenesRepository->update($almacen);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el almacén: " . $e->getMessage());
        }
    }

    public function createAlmacen($planta_id, $tipo, $id_hospital): bool
    {
        try {
            $almacen = new Almacenes(0, $planta_id, $tipo, $id_hospital);
            return $this->almacenesRepository->save($almacen);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el almacén: " . $e->getMessage());
        }
    }
}
