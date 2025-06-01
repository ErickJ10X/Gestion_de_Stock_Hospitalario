<?php

namespace controller;

require_once(__DIR__ . '/../model/service/Usuario_UbicacionService.php');
require_once(__DIR__ . '/../model/entity/Usuario_Ubicacion.php');

use Exception;
use model\entity\Usuario_Ubicacion;
use model\service\Usuario_UbicacionService;

class Usuario_UbicacionesController
{
    private Usuario_UbicacionService $usuario_ubicacionService;

    public function __construct()
    {
        $this->usuario_ubicacionService = new Usuario_UbicacionService();
    }

    /**
     * Obtiene todas las asociaciones usuario-ubicación
     * @return array Resultado con datos o mensaje de error
     */
    public function getAllUsuarioUbicaciones(): array
    {
        try {
            $ubicaciones = $this->usuario_ubicacionService->getAllUsuarioUbicaciones();
            return [
                'success' => true,
                'data' => $ubicaciones
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las ubicaciones de un usuario específico
     * @param int $idUsuario ID del usuario
     * @return array Resultado con datos o mensaje de error
     */
    public function getUbicacionesByUsuario(int $idUsuario): array
    {
        try {
            $ubicaciones = $this->usuario_ubicacionService->getUbicacionesByUsuario($idUsuario);
            return [
                'success' => true,
                'data' => $ubicaciones
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Crea una nueva asociación usuario-ubicación
     * @param int $idUsuario ID del usuario
     * @param string $tipoUbicacion Tipo de ubicación
     * @param int $idUbicacion ID de la ubicación
     * @return array Resultado con éxito o mensaje de error
     */
    public function createUsuarioUbicacion(int $idUsuario, string $tipoUbicacion, int $idUbicacion): array
    {
        try {
            $result = $this->usuario_ubicacionService->createUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
            return [
                'success' => $result,
                'message' => $result ? 'Asociación usuario-ubicación creada con éxito' : 'No se pudo crear la asociación'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualiza una asociación usuario-ubicación existente
     * @param int $idUsuario ID del usuario
     * @param string $tipoUbicacion Tipo de ubicación
     * @param int $idUbicacion ID de la ubicación
     * @return array Resultado con éxito o mensaje de error
     */
    public function updateUsuarioUbicacion(int $idUsuario, string $tipoUbicacion, int $idUbicacion): array
    {
        try {
            $result = $this->usuario_ubicacionService->updateUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
            return [
                'success' => $result,
                'message' => $result ? 'Asociación usuario-ubicación actualizada con éxito' : 'No se pudo actualizar la asociación'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Elimina una asociación usuario-ubicación específica
     * @param int $idUsuario ID del usuario
     * @param string $tipoUbicacion Tipo de ubicación
     * @param int $idUbicacion ID de la ubicación
     * @return array Resultado con éxito o mensaje de error
     */
    public function deleteUsuarioUbicacion(int $idUsuario, string $tipoUbicacion, int $idUbicacion): array
    {
        try {
            $result = $this->usuario_ubicacionService->deleteUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
            return [
                'success' => $result,
                'message' => $result ? 'Asociación usuario-ubicación eliminada con éxito' : 'No se pudo eliminar la asociación'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Elimina todas las ubicaciones asociadas a un usuario
     * @param int $idUsuario ID del usuario
     * @return array Resultado con éxito o mensaje de error
     */
    public function deleteUbicacionesByUsuario(int $idUsuario): array
    {
        try {
            $result = $this->usuario_ubicacionService->deleteUbicacionesByUsuario($idUsuario);
            return [
                'success' => $result,
                'message' => $result ? 'Ubicaciones del usuario eliminadas con éxito' : 'No se pudieron eliminar las ubicaciones del usuario'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Elimina todas las asociaciones de usuarios a una ubicación específica
     * @param int $idUbicacion ID de la ubicación
     * @return array Resultado con éxito o mensaje de error
     */
    public function deleteUsuariosByUbicacion(int $idUbicacion): array
    {
        try {
            $result = $this->usuario_ubicacionService->deleteUsuariosByUbicacion($idUbicacion);
            return [
                'success' => $result,
                'message' => $result ? 'Asociaciones de usuarios a la ubicación eliminadas con éxito' : 'No se pudieron eliminar las asociaciones'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
