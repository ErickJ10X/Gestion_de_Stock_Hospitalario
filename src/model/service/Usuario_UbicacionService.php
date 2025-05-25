<?php

namespace model\service;

use Exception;
use model\entity\Usuario_Ubicacion;
use model\repository\Usuario_ubicacionRepository;
use PDOException;

require_once(__DIR__ . '/../repository/Usuario_ubicacionRepository.php');
require_once(__DIR__ . '/../entity/Usuario_Ubicacion.php');

class Usuario_UbicacionService
{
    private Usuario_ubicacionRepository $usuario_ubicacionRepository;

    public function __construct()
    {
        $this->usuario_ubicacionRepository = new Usuario_ubicacionRepository();
    }

    public function getAllUsuarioUbicaciones(): array
    {
        try {
            return $this->usuario_ubicacionRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las ubicaciones de usuarios: " . $e->getMessage());
        }
    }

    public function getUbicacionesByUsuario($idUsuario): array
    {
        try {
            return $this->usuario_ubicacionRepository->findByUsuario($idUsuario);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las ubicaciones del usuario: " . $e->getMessage());
        }
    }

    public function createUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion): bool
    {
        try {
            $usuarioUbicacion = new Usuario_Ubicacion($idUsuario, $tipoUbicacion, $idUbicacion);
            return $this->usuario_ubicacionRepository->save($usuarioUbicacion);
        } catch (PDOException $e) {
            throw new Exception("Error al crear la asociación usuario-ubicación: " . $e->getMessage());
        }
    }

    public function updateUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion): bool
    {
        try {
            $usuarioUbicacion = new Usuario_Ubicacion($idUsuario, $tipoUbicacion, $idUbicacion);
            return $this->usuario_ubicacionRepository->update($usuarioUbicacion);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la asociación usuario-ubicación: " . $e->getMessage());
        }
    }

    public function deleteUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion): bool
    {
        try {
            return $this->usuario_ubicacionRepository->delete($idUsuario, $tipoUbicacion, $idUbicacion);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la asociación usuario-ubicación: " . $e->getMessage());
        }
    }

    public function deleteUbicacionesByUsuario($idUsuario): bool
    {
        try {
            return $this->usuario_ubicacionRepository->deleteByUsuario($idUsuario);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar las ubicaciones del usuario: " . $e->getMessage());
        }
    }

    public function deleteUsuariosByUbicacion($idUbicacion): bool
    {
        try {
            return $this->usuario_ubicacionRepository->deleteByUbicacion($idUbicacion);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar los usuarios de la ubicación: " . $e->getMessage());
        }
    }
}
