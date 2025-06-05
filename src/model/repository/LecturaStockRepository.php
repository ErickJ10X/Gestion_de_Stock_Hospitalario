<?php

namespace model\repository;

require_once __DIR__ . '/../../../config/database.php';

use model\entity\LecturaStock;
use model\entity\Producto;
use model\entity\Botiquin;
use model\entity\Usuario;
use DateTime;
use PDO;

class LecturaStockRepository {
    private PDO $conexion;

    public function __construct(PDO $conexion = null) {
        if ($conexion === null) {
            $this->conexion = getConnection();
        } else {
            $this->conexion = $conexion;
        }
    }

    public function findById(int $id): ?LecturaStock {
        $stmt = $this->conexion->prepare("
            SELECT ls.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo, p.descripcion as producto_descripcion, p.unidad_medida,
                   b.nombre as botiquin_nombre, b.id_planta as botiquin_planta,
                   u.nombre as usuario_nombre, u.email as usuario_email
            FROM lecturas_stock ls
            LEFT JOIN productos p ON ls.id_producto = p.id_producto
            LEFT JOIN botiquines b ON ls.id_botiquin = b.id_botiquin
            LEFT JOIN usuarios u ON ls.registrado_por = u.id_usuario
            WHERE ls.id_lectura = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        return $this->createLecturaStockFromData($data);
    }

    public function findAll(): array {
        $stmt = $this->conexion->query("
            SELECT ls.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo, p.descripcion as producto_descripcion, p.unidad_medida,
                   b.nombre as botiquin_nombre, b.id_planta as botiquin_planta,
                   u.nombre as usuario_nombre, u.email as usuario_email
            FROM lecturas_stock ls
            LEFT JOIN productos p ON ls.id_producto = p.id_producto
            LEFT JOIN botiquines b ON ls.id_botiquin = b.id_botiquin
            LEFT JOIN usuarios u ON ls.registrado_por = u.id_usuario
        ");
        
        $lecturas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lecturas[] = $this->createLecturaStockFromData($data);
        }
        
        return $lecturas;
    }

    public function findByBotiquin(int $idBotiquin): array {
        $stmt = $this->conexion->prepare("
            SELECT ls.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo, p.descripcion as producto_descripcion, p.unidad_medida,
                   b.nombre as botiquin_nombre, b.id_planta as botiquin_planta,
                   u.nombre as usuario_nombre, u.email as usuario_email
            FROM lecturas_stock ls
            LEFT JOIN productos p ON ls.id_producto = p.id_producto
            LEFT JOIN botiquines b ON ls.id_botiquin = b.id_botiquin
            LEFT JOIN usuarios u ON ls.registrado_por = u.id_usuario
            WHERE ls.id_botiquin = :id_botiquin
        ");
        $stmt->bindParam(':id_botiquin', $idBotiquin);
        $stmt->execute();
        
        $lecturas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lecturas[] = $this->createLecturaStockFromData($data);
        }
        
        return $lecturas;
    }

    public function findByProducto(int $idProducto): array {
        $stmt = $this->conexion->prepare("
            SELECT ls.*, 
                   p.nombre as producto_nombre, p.codigo as producto_codigo, p.descripcion as producto_descripcion, p.unidad_medida,
                   b.nombre as botiquin_nombre, b.id_planta as botiquin_planta,
                   u.nombre as usuario_nombre, u.email as usuario_email
            FROM lecturas_stock ls
            LEFT JOIN productos p ON ls.id_producto = p.id_producto
            LEFT JOIN botiquines b ON ls.id_botiquin = b.id_botiquin
            LEFT JOIN usuarios u ON ls.registrado_por = u.id_usuario
            WHERE ls.id_producto = :id_producto
        ");
        $stmt->bindParam(':id_producto', $idProducto);
        $stmt->execute();
        
        $lecturas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lecturas[] = $this->createLecturaStockFromData($data);
        }
        
        return $lecturas;
    }

    public function findLatestForBotiquin(int $idBotiquin): array {
        $stmt = $this->conexion->prepare("
            SELECT ls1.*, 
                p.nombre as producto_nombre, p.codigo as producto_codigo, p.descripcion as producto_descripcion, p.unidad_medida,
                b.nombre as botiquin_nombre, b.id_planta as botiquin_planta,
                u.nombre as usuario_nombre, u.email as usuario_email
            FROM lecturas_stock ls1
            INNER JOIN (
                SELECT id_producto, MAX(fecha_lectura) as ultima_fecha
                FROM lecturas_stock
                WHERE id_botiquin = :id_botiquin
                GROUP BY id_producto
            ) ls2 ON ls1.id_producto = ls2.id_producto AND ls1.fecha_lectura = ls2.ultima_fecha
            LEFT JOIN productos p ON ls1.id_producto = p.id_producto
            LEFT JOIN botiquines b ON ls1.id_botiquin = b.id_botiquin
            LEFT JOIN usuarios u ON ls1.registrado_por = u.id_usuario
            WHERE ls1.id_botiquin = :id_botiquin
        ");
        $stmt->bindParam(':id_botiquin', $idBotiquin);
        $stmt->execute();
        
        $lecturas = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lecturas[] = $this->createLecturaStockFromData($data);
        }
        
        return $lecturas;
    }

    public function save(LecturaStock $lectura): LecturaStock {
        $stmt = $this->conexion->prepare("
            INSERT INTO lecturas_stock (id_producto, id_botiquin, cantidad_disponible, fecha_lectura, registrado_por)
            VALUES (:id_producto, :id_botiquin, :cantidad_disponible, :fecha_lectura, :registrado_por)
        ");
        
        $fechaFormato = $lectura->getFechaLectura()->format('Y-m-d H:i:s');
        
        $stmt->bindValue(':id_producto', $lectura->getIdProducto());
        $stmt->bindValue(':id_botiquin', $lectura->getIdBotiquin());
        $stmt->bindValue(':cantidad_disponible', $lectura->getCantidadDisponible());
        $stmt->bindValue(':fecha_lectura', $fechaFormato);
        $stmt->bindValue(':registrado_por', $lectura->getRegistradoPor());
        
        $stmt->execute();
        $lectura->setIdLectura($this->conexion->lastInsertId());
        
        return $lectura;
    }

    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM lecturas_stock WHERE id_lectura = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    private function createLecturaStockFromData(array $data): LecturaStock {
        $lectura = new LecturaStock();
        
        $lectura->setIdLectura($data['id_lectura']);
        $lectura->setIdProducto($data['id_producto']);
        $lectura->setIdBotiquin($data['id_botiquin']);
        $lectura->setCantidadDisponible($data['cantidad_disponible']);
        $lectura->setFechaLectura(new DateTime($data['fecha_lectura']));
        $lectura->setRegistradoPor($data['registrado_por']);
        
        // Establecer relaciones si hay datos disponibles
        if (isset($data['producto_nombre'])) {
            $producto = new Producto();
            $producto->setIdProducto($data['id_producto']);
            $producto->setNombre($data['producto_nombre']);
            $producto->setCodigo($data['producto_codigo'] ?? '');
            $producto->setDescripcion($data['producto_descripcion'] ?? '');
            $producto->setUnidadMedida($data['unidad_medida'] ?? '');
            
            $lectura->setProducto($producto);
        }
        
        if (isset($data['botiquin_nombre'])) {
            $botiquin = new Botiquin();
            $botiquin->setIdBotiquin($data['id_botiquin']);
            $botiquin->setNombre($data['botiquin_nombre']);
            $botiquin->setIdPlanta($data['botiquin_planta'] ?? null);
            
            $lectura->setBotiquin($botiquin);
        }
        
        if (isset($data['usuario_nombre'])) {
            $usuario = new Usuario();
            $usuario->setIdUsuario($data['registrado_por']);
            $usuario->setNombre($data['usuario_nombre']);
            $usuario->setEmail($data['usuario_email'] ?? '');
            
            $lectura->setUsuario($usuario);
        }
        
        return $lectura;
    }
}
