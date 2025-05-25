<?php

namespace model\repository;

require_once(__DIR__ . '/../../../config/database.php');
require_once(__DIR__ . '/../entity/Pactos.php');

use model\entity\Pactos;
use PDO;

class PactosRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function mapToPactos($row): Pactos
    {
        return new Pactos(
            $row['id_pacto'],
            $row['id_producto'],
            $row['tipo_ubicacion'],
            $row['id_destino'],
            $row['cantidad_pactada']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pactos");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToPactos'], $rows);
    }

    public function findById($id): ?Pactos
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pactos WHERE id_pacto = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToPactos($row) : null;
    }

    public function findByIdProducto($id_producto): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pactos WHERE id_producto = :id_producto");
        $stmt->bindParam(':id_producto', $id_producto);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToPactos'], $rows);
    }

    public function findByTipoUbicacion($tipo_ubicacion): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pactos WHERE tipo_ubicacion = :tipo_ubicacion");
        $stmt->bindParam(':tipo_ubicacion', $tipo_ubicacion);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToPactos'], $rows);
    }

    public function findByIdDestino($id_destino): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pactos WHERE id_destino = :id_destino");
        $stmt->bindParam(':id_destino', $id_destino);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToPactos'], $rows);
    }

    public function findByCantidadPactada($cantidad_pactada): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pactos WHERE cantidad_pactada = :cantidad_pactada");
        $stmt->bindParam(':cantidad_pactada', $cantidad_pactada);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToPactos'], $rows);
    }

    public function save(Pactos $pacto): bool
    {
        $sql = "INSERT INTO pactos (id_producto, tipo_ubicacion, id_destino, cantidad_pactada) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $pacto->getIdProducto(),
            $pacto->getTipoUbicacion(),
            $pacto->getIdDestino(),
            $pacto->getCantidadPactada()
        ]);
    }

    public function update(Pactos $pacto): bool
    {
        $sql = "UPDATE pactos SET id_producto = ?, tipo_ubicacion = ?, id_destino = ?, cantidad_pactada = ? WHERE id_pacto = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $pacto->getIdProducto(),
            $pacto->getTipoUbicacion(),
            $pacto->getIdDestino(),
            $pacto->getCantidadPactada(),
            $pacto->getIdPacto()
        ]);
    }

    public function delete($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM pactos WHERE id_pacto = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
