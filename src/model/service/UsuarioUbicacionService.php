<?php

namespace model\service;

use model\entity\UsuarioUbicacion;
use model\repository\UsuarioUbicacionRepository;
use model\repository\UsuarioRepository;
use InvalidArgumentException;

class UsuarioUbicacionService {
    private UsuarioUbicacionRepository $ubicacionRepository;
    private ?UsuarioRepository $usuarioRepository;

    public function __construct(
        UsuarioUbicacionRepository $ubicacionRepository = null,
        UsuarioRepository $usuarioRepository = null
    ) {
        $this->ubicacionRepository = $ubicacionRepository ?? new UsuarioUbicacionRepository();
        $this->usuarioRepository = $usuarioRepository ?? new UsuarioRepository();
    }

    public function getUbicacionesByUsuario(int $idUsuario): array {
        return $this->ubicacionRepository->findByUsuario($idUsuario);
    }

    public function getUsuariosByUbicacion(string $tipoUbicacion, int $idUbicacion): array {
        if (!in_array($tipoUbicacion, UsuarioUbicacion::TIPOS_VALIDOS)) {
            throw new InvalidArgumentException('Tipo de ubicación no válido');
        }
        
        return $this->ubicacionRepository->findByUbicacion($tipoUbicacion, $idUbicacion);
    }

    public function asignarUbicacion(int $idUsuario, string $tipoUbicacion, int $idUbicacion): bool {
        // Validar que el usuario exista
        if ($this->usuarioRepository && !$this->usuarioRepository->findById($idUsuario)) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }
        
        // Validar el tipo de ubicación
        if (!in_array($tipoUbicacion, UsuarioUbicacion::TIPOS_VALIDOS)) {
            throw new InvalidArgumentException('Tipo de ubicación no válido');
        }
        
        $ubicacion = new UsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
        $this->ubicacionRepository->save($ubicacion);
        
        return true;
    }

    public function eliminarUbicacion(int $idUsuario, string $tipoUbicacion, int $idUbicacion): bool {
        return $this->ubicacionRepository->delete($idUsuario, $tipoUbicacion, $idUbicacion);
    }
    
    public function eliminarUbicacionesPorUsuario(int $idUsuario): bool {
        return $this->ubicacionRepository->deleteByUsuario($idUsuario);
    }
    
    public function eliminarUbicacionesPorUbicacion(string $tipoUbicacion, int $idUbicacion): bool {
        if (!in_array($tipoUbicacion, UsuarioUbicacion::TIPOS_VALIDOS)) {
            throw new InvalidArgumentException('Tipo de ubicación no válido');
        }
        
        return $this->ubicacionRepository->deleteByUbicacion($tipoUbicacion, $idUbicacion);
    }
    
    public function verificarAcceso(int $idUsuario, string $tipoUbicacion, int $idUbicacion): bool {
        $ubicaciones = $this->ubicacionRepository->findByUsuario($idUsuario);
        
        foreach ($ubicaciones as $ubicacion) {
            if ($ubicacion->getTipoUbicacion() === $tipoUbicacion && 
                $ubicacion->getIdUbicacion() === $idUbicacion) {
                return true;
            }
        }
        
        return false;
    }
}
