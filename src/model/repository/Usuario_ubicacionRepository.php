<?php

namespace model\repository;

use model\entity\Usuario_Ubicacion;
use PDO;

class Usuario_ubicacionRepository
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function mapToUsUb($row): array
    {
        return [
            'id_usuario' => $row['id_usuario'],
            'tipo_ubicacion' => $row['tipo_ubicacion'],
            'id_ubicacion' => $row['id_ubicacion']
        ];
    }

    public function mapToUsUbArray($row): array
    {
        $usbus = [];
        foreach ($row as $r) {
            $usbus[] = $this->mapToUsUb($r);
        }
        return $usbus;
    }

    public function findByUsuario($id_usuario): array
    {
        $sql = "SELECT * FROM usuario_ubicacion WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $this->mapToUsUbArray($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function save(Usuario_Ubicacion $usub): bool
    {
        $sql = "INSERT INTO usuario_ubicacion (id_usuario, tipo_ubicacion, id_ubicacion) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $usub->getIdUsuario(),
            $usub->getTipoUbicacion(),
            $usub->getIdUbicacion()
        ]);
    }

    public function delete($id_usuario, $tipo_ubicacion, $id_ubicacion): bool
    {
        $sql = "DELETE FROM usuario_ubicacion WHERE id_usuario = ? AND tipo_ubicacion = ? AND id_ubicacion = ?";
        return $this->pdo->prepare($sql)->execute([
            $id_usuario,
            $tipo_ubicacion,
            $id_ubicacion
        ]);
    }

    public function deleteByUsuario($id_usuario): bool
    {
        $sql = "DELETE FROM usuario_ubicacion WHERE id_usuario = ?";
        return $this->pdo->prepare($sql)->execute([$id_usuario]);
    }
    public function deleteByUbicacion($id_ubicacion): bool
    {
        $sql = "DELETE FROM usuario_ubicacion WHERE id_ubicacion = ?";
        return $this->pdo->prepare($sql)->execute([$id_ubicacion]);
    }
    public function update(Usuario_Ubicacion $usub): bool
    {
        $sql = "UPDATE usuario_ubicacion SET tipo_ubicacion = ?, id_ubicacion = ? WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $usub->getTipoUbicacion(),
            $usub->getIdUbicacion(),
            $usub->getIdUsuario()
        ]);
    }

}