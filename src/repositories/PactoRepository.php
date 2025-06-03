<?php

namespace repositories;

use models\Producto;
use models\Pacto;
use repositories\interfaces\PactoRepositoryInterface;

class PactoRepository implements PactoRepositoryInterface
{
    private \PDO $db;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $this->db = getConnection();
    }

    public function findById(int $id): ?Pacto
    {
        $stmt = $this->db->prepare('
            SELECT p.*, pr.nombre as nombre_producto
            FROM pactos p
            LEFT JOIN productos pr ON p.id_producto = pr.id_producto
            WHERE p.id_pacto = :id
        ');
        $stmt->execute(['id' => $id]);
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return $this->createPactoFromData($data);
    }

    public function findAll(bool $soloActivos = true): array
    {
        $sql = '
            SELECT p.*, pr.nombre as nombre_producto
            FROM pactos p
            LEFT JOIN productos pr ON p.id_producto = pr.id_producto
        ';
        
        if ($soloActivos) {
            $sql .= ' WHERE p.activo = 1';
        }
        
        $stmt = $this->db->query($sql);
        
        $pactos = [];
        while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $pactos[] = $this->createPactoFromData($data);
        }
        
        return $pactos;
    }
    
    public function findByProducto(int $idProducto, bool $soloActivos = true): array
    {
        $sql = '
            SELECT p.*, pr.nombre as nombre_producto
            FROM pactos p
            LEFT JOIN productos pr ON p.id_producto = pr.id_producto
            WHERE p.id_producto = :id_producto
        ';
        
        if ($soloActivos) {
            $sql .= ' AND p.activo = 1';
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_producto' => $idProducto]);
        
        $pactos = [];
        while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $pactos[] = $this->createPactoFromData($data);
        }
        
        return $pactos;
    }
    
    public function findByUbicacion(string $tipoUbicacion, int $idDestino, bool $soloActivos = true): array
    {
        $sql = '
            SELECT p.*, pr.nombre as nombre_producto
            FROM pactos p
            LEFT JOIN productos pr ON p.id_producto = pr.id_producto
            WHERE p.tipo_ubicacion = :tipo_ubicacion
            AND p.id_destino = :id_destino
        ';
        
        if ($soloActivos) {
            $sql .= ' AND p.activo = 1';
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'tipo_ubicacion' => $tipoUbicacion,
            'id_destino' => $idDestino
        ]);
        
        $pactos = [];
        while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $pactos[] = $this->createPactoFromData($data);
        }
        
        return $pactos;
    }

    public function save(Pacto $pacto): Pacto
    {
        if ($pacto->getIdPacto()) {
            // Update
            $stmt = $this->db->prepare('
                UPDATE pactos 
                SET id_producto = :id_producto,
                    tipo_ubicacion = :tipo_ubicacion,
                    id_destino = :id_destino,
                    cantidad_pactada = :cantidad_pactada,
                    activo = :activo
                WHERE id_pacto = :id_pacto
            ');
            
            $stmt->execute([
                'id_producto' => $pacto->getIdProducto(),
                'tipo_ubicacion' => $pacto->getTipoUbicacion(),
                'id_destino' => $pacto->getIdDestino(),
                'cantidad_pactada' => $pacto->getCantidadPactada(),
                'activo' => $pacto->isActivo() ? 1 : 0,
                'id_pacto' => $pacto->getIdPacto()
            ]);
            
            return $pacto;
        } else {
            // Insert
            $stmt = $this->db->prepare('
                INSERT INTO pactos (id_producto, tipo_ubicacion, id_destino, cantidad_pactada, activo)
                VALUES (:id_producto, :tipo_ubicacion, :id_destino, :cantidad_pactada, :activo)
            ');
            
            $stmt->execute([
                'id_producto' => $pacto->getIdProducto(),
                'tipo_ubicacion' => $pacto->getTipoUbicacion(),
                'id_destino' => $pacto->getIdDestino(),
                'cantidad_pactada' => $pacto->getCantidadPactada(),
                'activo' => $pacto->isActivo() ? 1 : 0
            ]);
            
            $pacto->setIdPacto($this->db->lastInsertId());
            return $pacto;
        }
    }
    
    public function desactivar(int $idPacto): bool
    {
        $stmt = $this->db->prepare('
            UPDATE pactos
            SET activo = 0
            WHERE id_pacto = :id_pacto
        ');
        
        return $stmt->execute(['id_pacto' => $idPacto]);
    }
    
    private function createPactoFromData(array $data): Pacto
    {
        $pacto = new Pacto();
        $pacto->setIdPacto($data['id_pacto']);
        $pacto->setIdProducto($data['id_producto']);
        $pacto->setTipoUbicacion($data['tipo_ubicacion']);
        $pacto->setIdDestino($data['id_destino']);
        $pacto->setCantidadPactada($data['cantidad_pactada']);
        $pacto->setActivo($data['activo'] == 1);
        
        // Si hay datos del producto, crear objeto
        if (isset($data['nombre_producto'])) {
            $producto = new Producto();
            $producto->setIdProducto($data['id_producto']);
            $producto->setNombre($data['nombre_producto']);
            $pacto->setProducto($producto);
        }
        
        return $pacto;
    }
}
