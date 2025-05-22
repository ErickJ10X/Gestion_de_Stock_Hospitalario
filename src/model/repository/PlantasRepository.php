<?php

namespace model\repository;

require_once __DIR__ . '/../entity/Planta.php';

use PDO;
use Exception;
use model\entity\Planta;

class PlantasRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        try {
            $query = "SELECT * FROM plantas ORDER BY id ASC";
            $statement = $this->db->prepare($query);
            $statement->execute();

            $plantas = [];
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $planta = new Planta();
                $planta->setId($row['id']);
                $planta->setNombre($row['nombre']);
                $planta->setHospitalId($row['hospital_id']);
                $plantas[] = $planta;
            }

            return $plantas;
        } catch (Exception $e) {
            throw new Exception("Error al consultar todas las plantas: " . $e->getMessage());
        }
    }

    public function findById($id)
    {
        try {
            $query = "SELECT * FROM plantas WHERE id = :id";
            $statement = $this->db->prepare($query);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $planta = new Planta();
                $planta->setId($row['id']);
                $planta->setNombre($row['nombre']);
                $planta->setHospitalId($row['hospital_id']);
                return $planta;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception("Error al consultar planta por ID: " . $e->getMessage());
        }
    }

    public function findByHospitalId($hospitalId)
    {
        try {
            $query = "SELECT * FROM plantas WHERE hospital_id = :hospital_id ORDER BY nombre ASC";
            $statement = $this->db->prepare($query);
            $statement->bindParam(':hospital_id', $hospitalId, PDO::PARAM_INT);
            $statement->execute();

            $plantas = [];
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $planta = new Planta();
                $planta->setId($row['id']);
                $planta->setNombre($row['nombre']);
                $planta->setHospitalId($row['hospital_id']);
                $plantas[] = $planta;
            }

            return $plantas;
        } catch (Exception $e) {
            throw new Exception("Error al consultar plantas por hospital_id: " . $e->getMessage());
        }
    }

    public function save(Planta $planta)
    {
        try {
            $query = "INSERT INTO plantas (nombre, hospital_id) VALUES (:nombre, :hospital_id)";
            $statement = $this->db->prepare($query);
            $nombre = $planta->getNombre();
            $hospitalId = $planta->getHospitalId();
            
            $statement->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $statement->bindParam(':hospital_id', $hospitalId, PDO::PARAM_INT);
            
            return $statement->execute();
        } catch (Exception $e) {
            throw new Exception("Error al guardar planta: " . $e->getMessage());
        }
    }

    public function update(Planta $planta)
    {
        try {
            $query = "UPDATE plantas SET nombre = :nombre, hospital_id = :hospital_id WHERE id = :id";
            $statement = $this->db->prepare($query);
            
            $id = $planta->getId();
            $nombre = $planta->getNombre();
            $hospitalId = $planta->getHospitalId();
            
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $statement->bindParam(':hospital_id', $hospitalId, PDO::PARAM_INT);
            
            return $statement->execute();
        } catch (Exception $e) {
            throw new Exception("Error al actualizar planta: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            // Comprobar primero si la planta tiene almacenes o botiquines asociados
            $checkBotiquines = "SELECT COUNT(*) FROM botiquines WHERE planta_id = :id";
            $checkAlmacenes = "SELECT COUNT(*) FROM almacenes WHERE planta_id = :id";
            
            $stmtBotiquines = $this->db->prepare($checkBotiquines);
            $stmtBotiquines->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtBotiquines->execute();
            $botiquinesCount = $stmtBotiquines->fetchColumn();
            
            if ($botiquinesCount > 0) {
                throw new Exception("No se puede eliminar la planta porque tiene botiquines asociados.");
            }
            
            $stmtAlmacenes = $this->db->prepare($checkAlmacenes);
            $stmtAlmacenes->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtAlmacenes->execute();
            $almacenesCount = $stmtAlmacenes->fetchColumn();
            
            if ($almacenesCount > 0) {
                throw new Exception("No se puede eliminar la planta porque tiene almacenes asociados.");
            }
            
            // Si no hay dependencias, eliminar la planta
            $query = "DELETE FROM plantas WHERE id = :id";
            $statement = $this->db->prepare($query);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $statement->execute();
        } catch (Exception $e) {
            throw new Exception("Error al eliminar planta: " . $e->getMessage());
        }
    }
}
