<?php

namespace model\service;
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../repository/HospitalesRepository.php');
require_once(__DIR__ . '/../entity/Hospital.php');

use model\entity\Hospital;
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

    public function getHospitalById($id): ?Hospital
    {
        try {
            return $this->hospitalRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el hospital: " . $e->getMessage());
        }
    }

    public function createHospital($nombre): bool
    {
        try {
            if (empty($nombre)) {
                throw new Exception("El nombre del hospital es obligatorio");
            }
            
            $hospital = new Hospital(0, $nombre);
            return $this->hospitalRepository->save($hospital);
        } catch (PDOException $e) {
            throw new Exception("Error al crear el hospital: " . $e->getMessage());
        }
    }

    public function updateHospital($id, $nombre): bool
    {
        try {
            if (empty($id) || empty($nombre)) {
                throw new Exception("El ID y el nombre del hospital son obligatorios");
            }
            
            $hospital = new Hospital($id, $nombre);
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
