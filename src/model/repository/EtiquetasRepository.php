<?php

namespace model\repository;

use model\entity\Etiquetas;
use PDO;

class EtiquetasRepository
{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = getConnection();
    }

    public function mapToEtiquetas($row): Etiquetas
    {
        return new Etiquetas(
            $row['id_etiqueta'],
            $row['id_producto'],
            $row['id_reposicion'],
            $row['tipo'],
            $row['prioridad'],
            $row['Impresa']
        );
    }
    public function mapToEtiquetasArray($rows): array
    {
        $etiquetas = [];
        foreach ($rows as $row) {
            $etiquetas[] = $this->mapToEtiquetas($row);
        }
        return $etiquetas;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM etiquetas");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapToEtiquetasArray($rows);
    }

    public function findById($id): ?Etiquetas
    {
        $stmt = $this->pdo->prepare("SELECT * FROM etiquetas WHERE id_etiqueta = ?");
        return $this->mapToEtiquetas($stmt->fetch(PDO::FETCH_ASSOC));
    }
    public function save(Etiquetas $etiqueta): bool
    {
        $sql = "INSERT INTO etiquetas (id_producto, id_reposicion, tipo, prioridad, Impresa) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $etiqueta->getIdProducto(),
            $etiqueta->getIdReposicion(),
            $etiqueta->getTipo(),
            $etiqueta->getPrioridad(),
            $etiqueta->getImpresa()
        ]);
    }

    public function update(Etiquetas $etiqueta): bool
    {
        $sql = "UPDATE etiquetas SET id_producto = ?, id_reposicion = ?, tipo = ?, prioridad = ?, Impresa = ? WHERE id_etiqueta = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $etiqueta->getIdProducto(),
            $etiqueta->getIdReposicion(),
            $etiqueta->getTipo(),
            $etiqueta->getPrioridad(),
            $etiqueta->getImpresa(),
            $etiqueta->getIdEtiqueta()
        ]);
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM etiquetas WHERE id_etiqueta = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}