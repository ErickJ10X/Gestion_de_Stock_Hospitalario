<?php

namespace model\service;

use Exception;
use PDO;
use PDOException;
use model\entity\Plantas;
require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../entity/Plantas.php');

class PlantaService
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = getConnection();
    }

    public function getAllPlantas(): array
    {
        try {
            $stmt = $this->conexion->query("SELECT * FROM plantas ORDER BY nombre");
            $plantas = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $plantas[] = new Plantas(
                    $row['id_planta'],
                    $row['nombre'],
                    $row['id_hospital']
                );
            }
            
            return $plantas;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todas las plantas: " . $e->getMessage());
        }
    }

    public function getPlantasByHospital($hospitalId): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM plantas WHERE id_hospital = :hospital_id ORDER BY nombre");
            $stmt->bindParam(':hospital_id', $hospitalId, PDO::PARAM_INT);
            $stmt->execute();
            
            $plantas = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $plantas[] = new Plantas(
                    $row['id_planta'],
                    $row['nombre'],
                    $row['id_hospital']
                );
            }
            
            return $plantas;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener plantas por hospital: " . $e->getMessage());
        }
    }

    public function getPlantaById($id): ?Plantas
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM plantas WHERE id_planta = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Plantas(
                    $row['id_planta'],
                    $row['nombre'],
                    $row['id_hospital']
                );
            }
            
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener planta por ID: " . $e->getMessage());
        }
    }
}
