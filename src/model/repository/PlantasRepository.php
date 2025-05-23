<?php

namespace model\repository;

require_once __DIR__ . '/../entity/Plantas.php';

use PDO;
use Exception;
use model\entity\Plantas;

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
                $planta = new Plantas();
                $planta->setIdPlanta($row['id']);
                $planta->setNombre($row['nombre']);
                $planta->setIdHospital($row['hospital_id']);
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
                $planta = new Plantas();
                $planta->setIdPlanta($row['id']);
                $planta->setNombre($row['nombre']);
                $planta->setIdHospital($row['hospital_id']);
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
                $planta = new Plantas();
                $planta->setIdPlanta($row['id']);
                $planta->setNombre($row['nombre']);
                $planta->setIdHospital($row['hospital_id']);
                $plantas[] = $planta;
            }

            return $plantas;
        } catch (Exception $e) {
            throw new Exception("Error al consultar plantas por hospital_id: " . $e->getMessage());
        }
    }

    public function save(Plantas $planta)
    {
        try {
            $query = "INSERT INTO plantas (nombre, hospital_id) VALUES (:nombre, :hospital_id)";
            $statement = $this->db->prepare($query);
            $nombre = $planta->getNombre();
            $hospitalId = $planta->getIdHospital();
            
            $statement->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $statement->bindParam(':hospital_id', $hospitalId, PDO::PARAM_INT);
            
            return $statement->execute();
        } catch (Exception $e) {
            throw new Exception("Error al guardar planta: " . $e->getMessage());
        }
    }

    public function update(Plantas $planta)
    {
        try {
            $query = "UPDATE plantas SET nombre = :nombre, hospital_id = :hospital_id WHERE id = :id";
            $statement = $this->db->prepare($query);
            
            $id = $planta->getIdPlanta();
            $nombre = $planta->getNombre();
            $hospitalId = $planta->getIdHospital();
            
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
            
            $query = "DELETE FROM plantas WHERE id = :id";
            $statement = $this->db->prepare($query);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $statement->execute();
        } catch (Exception $e) {
            throw new Exception("Error al eliminar planta: " . $e->getMessage());
        }
    }
}
