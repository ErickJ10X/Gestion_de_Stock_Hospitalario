<?php

namespace model\repository;

require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../entity/Lecturas_stock.php');

use model\entity\Lecturas_stock;
use PDO;

class LecturaRepository
{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = getConnection();
    }

    public function mapToLectura($row): Lecturas_stock
    {
        return new Lecturas_stock(
            $row['id_lectura'],
            $row['id_producto'],
            $row['id_botiquin'],
            $row['cantidad_disponible'],
            $row['fecha_lectura'],
            $row['registrado_por']
        );
    }

    public function mapToLecturaArray(array $rows): array
    {
        $lecturas = [];
        foreach ($rows as $row) {
            $lecturas[] = $this->mapToLectura($row);
        }
        return $lecturas;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lecturas_stock");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapToLecturaArray($rows);
    }

    public function findById($id): ?Lecturas_stock
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lecturas_stock WHERE id_lectura = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }
        
        return $this->mapToLectura($result);
    }
    
    public function findByProducto($idProducto): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lecturas_stock WHERE id_producto = ?");
        $stmt->execute([$idProducto]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapToLecturaArray($rows);
    }
    
    public function findByBotiquin($idBotiquin): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lecturas_stock WHERE id_botiquin = ?");
        $stmt->execute([$idBotiquin]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapToLecturaArray($rows);
    }
    
    public function findByRegistrador($registradoPor): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lecturas_stock WHERE registrado_por = ?");
        $stmt->execute([$registradoPor]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapToLecturaArray($rows);
    }

    public function save(Lecturas_stock $lectura): bool
    {
        $sql = "INSERT INTO lecturas_stock (id_producto, id_botiquin, cantidad_disponible, fecha_lectura, registrado_por) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $lectura->getIdProducto(),
            $lectura->getIdBotiquin(),
            $lectura->getCantidadDisponible(),
            $lectura->getFechaLectura(),
            $lectura->getRegistradoPor()
        ]);
    }
    
    public function update(Lecturas_stock $lectura): bool
    {
        $stmt = $this->pdo->prepare("UPDATE lecturas_stock SET id_producto = ?, id_botiquin = ?, cantidad_disponible = ?, fecha_lectura = ?, registrado_por = ? WHERE id_lectura = ?");
        return $stmt->execute([
            $lectura->getIdProducto(),
            $lectura->getIdBotiquin(),
            $lectura->getCantidadDisponible(),
            $lectura->getFechaLectura(),
            $lectura->getRegistradoPor(),
            $lectura->getIdLectura()
        ]);
    }
    
    public function delete($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM lecturas_stock WHERE id_lectura = ?");
        return $stmt->execute([$id]);
    }
}
