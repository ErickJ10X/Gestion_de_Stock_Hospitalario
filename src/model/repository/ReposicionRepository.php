<?php

namespace model\repository;

require_once __DIR__ . '/../entity/Reposicion.php';
require_once __DIR__ . '/../../../config/Database.php';

use model\entity\Reposicion;
use DateTime;
use PDO;
use PDOException;

class ReposicionRepository {
    private PDO $conn;

    public function __construct() {
        $this->conn = getConnection();
        $this->ensureNotasColumnExists();
        $this->ensureEstadoColumnExists();
    }

    /**
     * Verifica si la columna 'notas' existe y la crea si es necesario
     */
    private function ensureNotasColumnExists(): void {
        try {
            // Verificar si la columna existe
            $sql = "SHOW COLUMNS FROM reposiciones LIKE 'notas'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                // La columna no existe, crear la columna
                $alterSql = "ALTER TABLE reposiciones ADD COLUMN notas TEXT NULL AFTER urgente";
                $this->conn->exec($alterSql);
            }
        } catch (PDOException $e) {
            // Si hay un error, lo registramos pero no interrumpimos la ejecución
            error_log("Error verificando/creando columna 'notas': " . $e->getMessage());
        }
    }

    /**
     * Verifica si la columna 'estado' existe y la crea si es necesario
     */
    private function ensureEstadoColumnExists(): void {
        try {
            // Verificar si la columna existe
            $sql = "SHOW COLUMNS FROM reposiciones LIKE 'estado'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                // La columna no existe, crear la columna
                $alterSql = "ALTER TABLE reposiciones ADD COLUMN estado TINYINT(1) NOT NULL AFTER fecha";
                $this->conn->exec($alterSql);
            }
        } catch (PDOException $e) {
            // Si hay un error, lo registramos pero no interrumpimos la ejecución
            error_log("Error verificando/creando columna 'estado': " . $e->getMessage());
        }
    }

    public function findById(int $id): ?Reposicion {
        $sql = "SELECT * FROM reposiciones WHERE id_reposicion = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->createReposicionFromRow($row);
    }

    public function findAll(): array {
        $sql = "SELECT * FROM reposiciones ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }

        return $reposiciones;
    }

    /**
     * Busca reposiciones dentro de un rango de fechas
     *
     * @param DateTime $fechaDesde Fecha inicial
     * @param DateTime $fechaHasta Fecha final
     * @return array Lista de reposiciones
     */
    public function findByFechas(DateTime $fechaDesde, DateTime $fechaHasta): array {
        $sql = "SELECT * FROM reposiciones WHERE fecha >= :fecha_desde AND fecha <= :fecha_hasta ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);

        $fechaDesdeStr = $fechaDesde->format('Y-m-d 00:00:00');
        $fechaHastaStr = $fechaHasta->format('Y-m-d 23:59:59');

        $stmt->bindParam(':fecha_desde', $fechaDesdeStr);
        $stmt->bindParam(':fecha_hasta', $fechaHastaStr);
        $stmt->execute();

        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }

        return $reposiciones;
    }

    public function findByAlmacen(int $idAlmacen): array {
        $sql = "SELECT * FROM reposiciones WHERE desde_almacen = :id_almacen ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_almacen', $idAlmacen);
        $stmt->execute();

        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }

        return $reposiciones;
    }

    public function findByBotiquin(int $idBotiquin): array {
        $sql = "SELECT * FROM reposiciones WHERE hacia_botiquin = :id_botiquin ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_botiquin', $idBotiquin);
        $stmt->execute();

        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }

        return $reposiciones;
    }

    public function findUrgentes(): array {
        $sql = "SELECT * FROM reposiciones WHERE urgente = TRUE ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }

        return $reposiciones;
    }

    /**
     * Busca reposiciones por su estado
     *
     * @param bool $estado Estado de reposición (true = completada, false = pendiente)
     * @return array Lista de reposiciones
     */
    public function findByEstado(bool $estado): array {
        $sql = "SELECT * FROM reposiciones WHERE estado = :estado ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $estadoInt = $estado ? 1 : 0;
        $stmt->bindParam(':estado', $estadoInt, PDO::PARAM_INT);
        $stmt->execute();

        $reposiciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reposiciones[] = $this->createReposicionFromRow($row);
        }

        return $reposiciones;
    }

    public function save(Reposicion $reposicion): Reposicion {
        if ($reposicion->getId() === null) {
            return $this->insert($reposicion);
        } else {
            return $this->update($reposicion);
        }
    }

    private function insert(Reposicion $reposicion): Reposicion {
        try {
            $sql = "INSERT INTO reposiciones (id_producto, desde_almacen, hacia_botiquin, cantidad_repuesta, fecha, estado, urgente, notas)
                    VALUES (:id_producto, :desde_almacen, :hacia_botiquin, :cantidad_repuesta, :fecha, :estado, :urgente, :notas)";

            $stmt = $this->conn->prepare($sql);

            $idProducto = $reposicion->getIdProducto();
            $desdeAlmacen = $reposicion->getDesdeAlmacen();
            $haciaBotiquin = $reposicion->getHaciaBotiquin();
            $cantidadRepuesta = $reposicion->getCantidadRepuesta();
            $fecha = $reposicion->getFecha()->format('Y-m-d H:i:s');
            $estado = $reposicion->getEstado() ? 1 : 0;
            $urgente = $reposicion->isUrgente() ? 1 : 0;
            $notas = $reposicion->getNotas();

            $stmt->bindParam(':id_producto', $idProducto);
            $stmt->bindParam(':desde_almacen', $desdeAlmacen);
            $stmt->bindParam(':hacia_botiquin', $haciaBotiquin);
            $stmt->bindParam(':cantidad_repuesta', $cantidadRepuesta);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':urgente', $urgente);
            $stmt->bindParam(':notas', $notas);

            $stmt->execute();

            $id = $this->conn->lastInsertId();
            $reposicion->setId((int)$id);

            return $reposicion;
        } catch (PDOException $e) {
            // Si el error es por la columna 'notas' o 'estado' no existente, intentamos crear las columnas y reintentar
            if (stripos($e->getMessage(), "Column not found: 1054 Unknown column 'notas'") !== false) {
                $this->ensureNotasColumnExists();
                return $this->insert($reposicion);
            } else if (stripos($e->getMessage(), "Column not found: 1054 Unknown column 'estado'") !== false) {
                $this->ensureEstadoColumnExists();
                return $this->insert($reposicion);
            }
            // Si es otro error, lo propagamos
            throw $e;
        }
    }

    private function update(Reposicion $reposicion): Reposicion {
        try {
            // Para depuración - ver el estado antes de actualizar
            $estado = $reposicion->getEstado() ? 1 : 0;
            error_log("Repositorio - Actualizando reposición ID " . $reposicion->getId() . " con estado = $estado");

            $sql = "UPDATE reposiciones 
                    SET id_producto = :id_producto, 
                        desde_almacen = :desde_almacen,
                        hacia_botiquin = :hacia_botiquin,
                        cantidad_repuesta = :cantidad_repuesta,
                        fecha = :fecha,
                        estado = :estado,
                        urgente = :urgente,
                        notas = :notas
                    WHERE id_reposicion = :id";

            $stmt = $this->conn->prepare($sql);

            $id = $reposicion->getId();
            $idProducto = $reposicion->getIdProducto();
            $desdeAlmacen = $reposicion->getDesdeAlmacen();
            $haciaBotiquin = $reposicion->getHaciaBotiquin();
            $cantidadRepuesta = $reposicion->getCantidadRepuesta();
            $fecha = $reposicion->getFecha()->format('Y-m-d H:i:s');
            $estado = $reposicion->getEstado() ? 1 : 0;
            $urgente = $reposicion->isUrgente() ? 1 : 0;
            $notas = $reposicion->getNotas();

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':id_producto', $idProducto);
            $stmt->bindParam(':desde_almacen', $desdeAlmacen);
            $stmt->bindParam(':hacia_botiquin', $haciaBotiquin);
            $stmt->bindParam(':cantidad_repuesta', $cantidadRepuesta);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':urgente', $urgente);
            $stmt->bindParam(':notas', $notas);

            $resultado = $stmt->execute();

            // Verificar si la operación fue exitosa
            if ($resultado) {
                error_log("Actualización exitosa de reposición ID $id con estado = $estado");

                // Verificar cuántas filas se actualizaron
                $filasActualizadas = $stmt->rowCount();
                error_log("Filas actualizadas: $filasActualizadas");

                // Verificar que el estado se actualizó correctamente en la base de datos
                $sqlVerificacion = "SELECT estado FROM reposiciones WHERE id_reposicion = :id";
                $stmtVerificacion = $this->conn->prepare($sqlVerificacion);
                $stmtVerificacion->bindParam(':id', $id);
                $stmtVerificacion->execute();
                $estadoDb = $stmtVerificacion->fetchColumn();

                error_log("Estado en la base de datos después de actualizar: $estadoDb");

                if ((bool)$estadoDb !== $reposicion->getEstado()) {
                    error_log("¡ADVERTENCIA! El estado en la base de datos no coincide con el estado del objeto.");
                }
            } else {
                error_log("Error al actualizar reposición ID $id: " . print_r($stmt->errorInfo(), true));
            }

            return $reposicion;
        } catch (PDOException $e) {
            // Si el error es por columnas no existentes, intentamos crearlas y reintentar
            if (stripos($e->getMessage(), "Column not found: 1054 Unknown column 'notas'") !== false) {
                $this->ensureNotasColumnExists();
                return $this->update($reposicion);
            } else if (stripos($e->getMessage(), "Column not found: 1054 Unknown column 'estado'") !== false) {
                $this->ensureEstadoColumnExists();
                return $this->update($reposicion);
            }
            // Si es otro error, lo registramos y lo propagamos
            error_log("Error PDO al actualizar reposición: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM reposiciones WHERE id_reposicion = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    private function createReposicionFromRow(array $row): Reposicion {
        // Verificar cuál es el nombre de la columna ID
        $idColumnName = isset($row['id_reposicion']) ? 'id_reposicion' : 'id';

        // Crear la reposición usando el método withId
        return Reposicion::withId(
            (int)$row[$idColumnName],
            (int)$row['id_producto'],
            (int)$row['desde_almacen'],
            (int)$row['hacia_botiquin'],
            (int)$row['cantidad_repuesta'],
            new DateTime($row['fecha']),
            (bool)$row['estado'],
            (bool)$row['urgente'],
            $row['notas'] ?? ''
        );
    }
}