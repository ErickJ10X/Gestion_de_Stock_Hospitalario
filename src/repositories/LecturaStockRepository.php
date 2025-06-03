<?php

namespace repositories;

use models\Producto;
use models\Botiquin;
use models\LecturaStock;
use models\Usuario;
use repositories\interfaces\LecturaStockRepositoryInterface;

class LecturaStockRepository implements LecturaStockRepositoryInterface
{
    private \PDO $db;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $this->db = getConnection();
    }

    public function findById(int $id): ?LecturaStock
    {
        $stmt = $this->db->prepare('
            SELECT l.*, p.nombre as nombre_producto, b.nombre as nombre_botiquin, u.nombre as nombre_usuario
            FROM lecturas_stock l
            LEFT JOIN productos p ON l.id_producto = p.id_producto
            LEFT JOIN botiquines b ON l.id_botiquin = b.id_botiquin
            LEFT JOIN usuarios u ON l.registrado_por = u.id_usuario
            WHERE l.id_lectura = :id
        ');
        $stmt->execute(['id' => $id]);
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return $this->createLecturaStockFromData($data);
    }
    
    public function findByBotiquin(int $idBotiquin): array
    {
        $stmt = $this->db->prepare('
            SELECT l.*, p.nombre as nombre_producto, b.nombre as nombre_botiquin, u.nombre as nombre_usuario
            FROM lecturas_stock l
            LEFT JOIN productos p ON l.id_producto = p.id_producto
            LEFT JOIN botiquines b ON l.id_botiquin = b.id_botiquin
            LEFT JOIN usuarios u ON l.registrado_por = u.id_usuario
            WHERE l.id_botiquin = :id_botiquin
            ORDER BY l.fecha_lectura DESC
        ');
        $stmt->execute(['id_botiquin' => $idBotiquin]);
        
        $lecturas = [];
        while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $lecturas[] = $this->createLecturaStockFromData($data);
        }
        
        return $lecturas;
    }
    
    public function findByProducto(int $idProducto): array
    {
        $stmt = $this->db->prepare('
            SELECT l.*, p.nombre as nombre_producto, b.nombre as nombre_botiquin, u.nombre as nombre_usuario
            FROM lecturas_stock l
            LEFT JOIN productos p ON l.id_producto = p.id_producto
            LEFT JOIN botiquines b ON l.id_botiquin = b.id_botiquin
            LEFT JOIN usuarios u ON l.registrado_por = u.id_usuario
            WHERE l.id_producto = :id_producto
            ORDER BY l.fecha_lectura DESC
        ');
        $stmt->execute(['id_producto' => $idProducto]);
        
        $lecturas = [];
        while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $lecturas[] = $this->createLecturaStockFromData($data);
        }
        
        return $lecturas;
    }
    
    public function findLatestByProductoAndBotiquin(int $idProducto, int $idBotiquin): ?LecturaStock
    {
        $stmt = $this->db->prepare('
            SELECT l.*, p.nombre as nombre_producto, b.nombre as nombre_botiquin, u.nombre as nombre_usuario
            FROM lecturas_stock l
            LEFT JOIN productos p ON l.id_producto = p.id_producto
            LEFT JOIN botiquines b ON l.id_botiquin = b.id_botiquin
            LEFT JOIN usuarios u ON l.registrado_por = u.id_usuario
            WHERE l.id_producto = :id_producto AND l.id_botiquin = :id_botiquin
            ORDER BY l.fecha_lectura DESC
            LIMIT 1
        ');
        $stmt->execute([
            'id_producto' => $idProducto,
            'id_botiquin' => $idBotiquin
        ]);
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return $this->createLecturaStockFromData($data);
    }

    public function save(LecturaStock $lecturaStock): LecturaStock
    {
        if ($lecturaStock->getIdLectura() !== null) {
            // Update
            $stmt = $this->db->prepare('
                UPDATE lecturas_stock 
                SET id_producto = :id_producto,
                    id_botiquin = :id_botiquin,
                    cantidad_disponible = :cantidad_disponible,
                    fecha_lectura = :fecha_lectura,
                    registrado_por = :registrado_por
                WHERE id_lectura = :id_lectura
            ');
            
            $stmt->execute([
                'id_producto' => $lecturaStock->getIdProducto(),
                'id_botiquin' => $lecturaStock->getIdBotiquin(),
                'cantidad_disponible' => $lecturaStock->getCantidadDisponible(),
                'fecha_lectura' => $lecturaStock->getFechaLectura()->format('Y-m-d H:i:s'),
                'registrado_por' => $lecturaStock->getRegistradoPor(),
                'id_lectura' => $lecturaStock->getIdLectura()
            ]);
            
            return $lecturaStock;
        } else {
            // Insert
            $stmt = $this->db->prepare('
                INSERT INTO lecturas_stock (id_producto, id_botiquin, cantidad_disponible, fecha_lectura, registrado_por)
                VALUES (:id_producto, :id_botiquin, :cantidad_disponible, :fecha_lectura, :registrado_por)
            ');
            
            $stmt->execute([
                'id_producto' => $lecturaStock->getIdProducto(),
                'id_botiquin' => $lecturaStock->getIdBotiquin(),
                'cantidad_disponible' => $lecturaStock->getCantidadDisponible(),
                'fecha_lectura' => $lecturaStock->getFechaLectura()->format('Y-m-d H:i:s'),
                'registrado_por' => $lecturaStock->getRegistradoPor()
            ]);
            
            $lecturaStock->setIdLectura($this->db->lastInsertId());
            return $lecturaStock;
        }
    }
    
    private function createLecturaStockFromData(array $data): LecturaStock
    {
        $lecturaStock = new LecturaStock();
        $lecturaStock->setIdLectura($data['id_lectura']);
        $lecturaStock->setIdProducto($data['id_producto']);
        $lecturaStock->setIdBotiquin($data['id_botiquin']);
        $lecturaStock->setCantidadDisponible($data['cantidad_disponible']);
        $lecturaStock->setFechaLectura(new \DateTime($data['fecha_lectura']));
        $lecturaStock->setRegistradoPor($data['registrado_por']);
        
        // Si hay datos relacionados, crear objetos
        if (isset($data['nombre_producto'])) {
            $producto = new Producto();
            $producto->setIdProducto($data['id_producto']);
            $producto->setNombre($data['nombre_producto']);
            $lecturaStock->setProducto($producto);
        }
        
        if (isset($data['nombre_botiquin'])) {
            $botiquin = new Botiquin();
            $botiquin->setIdBotiquin($data['id_botiquin']);
            $botiquin->setNombre($data['nombre_botiquin']);
            $lecturaStock->setBotiquin($botiquin);
        }
        
        if (isset($data['nombre_usuario'])) {
            $usuario = new Usuario();
            $usuario->setIdUsuario($data['registrado_por']);
            $usuario->setNombre($data['nombre_usuario']);
            $lecturaStock->setUsuario($usuario);
        }
        
        return $lecturaStock;
    }
}
