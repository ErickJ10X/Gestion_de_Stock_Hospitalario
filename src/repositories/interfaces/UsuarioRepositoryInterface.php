<?php

namespace repositories\interfaces;

use Models\Usuario;

interface UsuarioRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?Usuario;
    public function findByEmail(string $email): ?Usuario;
    public function findByRol(string $rol): array;
    public function findActive(): array;
    public function findByUbicacion(string $tipoUbicacion, int $idUbicacion): array;
    public function save(Usuario $usuario): Usuario;
    public function update(Usuario $usuario): bool;
    public function delete(int $id): bool;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
    public function loadUbicaciones(Usuario $usuario): Usuario;
}
