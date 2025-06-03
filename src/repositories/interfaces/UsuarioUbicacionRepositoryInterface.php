<?php

namespace repositories\interfaces;

use Models\UsuarioUbicacion;

interface UsuarioUbicacionRepositoryInterface {
    public function findByUsuario(int $idUsuario): array;
    public function findByUbicacion(string $tipoUbicacion, int $idUbicacion): array;
    public function find(int $idUsuario, string $tipoUbicacion, int $idUbicacion): ?UsuarioUbicacion;
    public function save(UsuarioUbicacion $ubicacion): bool;
    public function delete(int $idUsuario, string $tipoUbicacion, int $idUbicacion): bool;
    public function deleteAllByUsuario(int $idUsuario): bool;
}
