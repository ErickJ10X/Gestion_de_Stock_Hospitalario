<?php

namespace model\service;

use Exception;
use model\entity\Roles;
use model\repository\RolesRepository;
use PDOException;

require_once(__DIR__ . '/../repository/RolesRepository.php');
require_once(__DIR__ . '/../entity/Roles.php');

class RolesService
{
    private RolesRepository $rolesRepository;

    public function __construct()
    {
        $this->rolesRepository = new RolesRepository();
    }

    public function getAllRoles(): array
    {
        try {
            return $this->rolesRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los roles: " . $e->getMessage());
        }
    }

    public function getRolById(int $id): ?Roles
    {
        try {
            return $this->rolesRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el rol con ID {$id}: " . $e->getMessage());
        }
    }

    public function createRol(string $nombre): bool
    {
        try {
            $rol = new Roles(null, $nombre);
            $this->rolesRepository->save($rol);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el rol: " . $e->getMessage());
        }
    }

    public function updateRol(int $id, string $nombre): bool
    {
        try {
            $rol = $this->rolesRepository->findById($id);
            if (!$rol) {
                throw new Exception("No se encontró el rol con ID: " . $id);
            }

            $rol->setNombre($nombre);
            $this->rolesRepository->update($rol);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el rol: " . $e->getMessage());
        }
    }

    public function deleteRol(int $id): bool
    {
        try {
            $rol = $this->rolesRepository->findById($id);
            if (!$rol) {
                throw new Exception("No se encontró el rol con ID: " . $id);
            }

            $this->rolesRepository->delete($id);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el rol: " . $e->getMessage());
        }
    }
}
