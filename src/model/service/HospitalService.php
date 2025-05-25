<?php

namespace model\service;
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../repository/HospitalesRepository.php');
require_once(__DIR__ . '/../entity/Hospitales.php');

use model\entity\Hospitales;
use model\repository\HospitalesRepository;
use PDOException;
use Exception;

class HospitalService
{
    private HospitalesRepository $hospitalRepository;

    public function __construct()
    {
        $this->hospitalRepository = new HospitalesRepository();
    }

    public function getAllHospitales(): array
    {
        try {
            return $this->hospitalRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al cargar los hospitales: " . $e->getMessage());
        }
    }

    public function getHospitalById($id): ?Hospitales
    {
        try {
            return $this->hospitalRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el hospital: " . $e->getMessage());
        }
    }

    public function createHospital($nombre, $ubicacion = ""): bool
    {
        try {
            if (empty($nombre)) {
                throw new Exception("El nombre del hospital es obligatorio");
            }

            $hospital = new Hospitales(0, $nombre, $ubicacion);
            return $this->hospitalRepository->save($hospital);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el hospital: " . $e->getMessage());
        }
    }

    public function updateHospital($id, $nombre, $ubicacion = null): bool
    {
        try {
            if (empty($id) || empty($nombre)) {
                throw new Exception("El ID y el nombre del hospital son obligatorios");
            }

            $hospital = $this->hospitalRepository->findById($id);
            if (!$hospital) {
                throw new Exception("Hospital no encontrado");
            }

            $hospital->setNombre($nombre);
            if ($ubicacion !== null) {
                $hospital->setUbicacion($ubicacion);
            }

            return $this->hospitalRepository->update($hospital);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el hospital: " . $e->getMessage());
        }
    }

    public function deleteHospital($id): bool
    {
        try {
            if (empty($id)) {
                throw new Exception("El ID del hospital es obligatorio");
            }

            return $this->hospitalRepository->deleteById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el hospital: " . $e->getMessage());
        }
    }
}
