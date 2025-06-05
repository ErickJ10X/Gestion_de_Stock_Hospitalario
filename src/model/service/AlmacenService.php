<?php

namespace model\service;

use model\entity\Almacen;
use model\repository\AlmacenRepository;
use InvalidArgumentException;

class AlmacenService {
    private AlmacenRepository $almacenRepository;

    public function __construct(AlmacenRepository $almacenRepository = null) {
        $this->almacenRepository = $almacenRepository ?? new AlmacenRepository();
    }

    public function getAlmacenById(int $id): ?Almacen {
        return $this->almacenRepository->findById($id);
    }

    public function getAllAlmacenes(): array {
        return $this->almacenRepository->findAll();
    }

    public function getAlmacenesByHospital(int $idHospital): array {
        return $this->almacenRepository->findByHospital($idHospital);
    }

    public function createAlmacen(array $data): Almacen {
        $this->validateAlmacenData($data);
        
        $almacen = new Almacen(
            null,
            $data['tipo'] ?? 'General',
            $data['id_planta'] ?? null,
            $data['id_hospital'],
            $data['activo'] ?? true
        );
        
        // Si es almacén de tipo Planta, debe tener id_planta
        if ($almacen->getTipo() === 'Planta' && $almacen->getIdPlanta() === null) {
            throw new InvalidArgumentException('Un almacén de tipo Planta debe tener una planta asignada.');
        }
        
        return $this->almacenRepository->save($almacen);
    }

    public function updateAlmacen(int $id, array $data): Almacen {
        $almacen = $this->almacenRepository->findById($id);
        if (!$almacen) {
            throw new InvalidArgumentException('Almacén no encontrado');
        }
        
        if (isset($data['tipo'])) {
            $almacen->setTipo($data['tipo']);
        }
        
        if (isset($data['id_planta'])) {
            $almacen->setIdPlanta($data['id_planta']);
        }
        
        if (isset($data['id_hospital'])) {
            $almacen->setIdHospital($data['id_hospital']);
        }
        
        if (isset($data['activo'])) {
            $almacen->setActivo($data['activo']);
        }
        
        // Si es almacén de tipo Planta, debe tener id_planta
        if ($almacen->getTipo() === 'Planta' && $almacen->getIdPlanta() === null) {
            throw new InvalidArgumentException('Un almacén de tipo Planta debe tener una planta asignada.');
        }
        
        return $this->almacenRepository->save($almacen);
    }

    public function deleteAlmacen(int $id): bool {
        return $this->almacenRepository->delete($id);
    }

    public function desactivarAlmacen(int $id): bool {
        return $this->almacenRepository->softDelete($id);
    }

    private function validateAlmacenData(array $data): void {
        if (!isset($data['id_hospital'])) {
            throw new InvalidArgumentException('El hospital es obligatorio');
        }
    }
}
