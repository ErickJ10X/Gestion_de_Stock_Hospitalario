<?php

namespace controller;

require_once __DIR__ . '/../model/service/AlmacenService.php';
require_once __DIR__ . '/../model/service/PlantaService.php';
require_once __DIR__ . '/../model/service/ReposicionService.php';
require_once __DIR__ . '/../model/service/ProductoService.php';
require_once __DIR__ . '/../util/Session.php';
require_once __DIR__ . '/../util/AuthGuard.php';

use model\service\AlmacenService;
use model\service\PlantaService;
use model\service\ReposicionService;
use model\service\ProductoService;
use util\Session;
use util\AuthGuard;
use DateTime;
use Exception;

class InformesController
{
    private AlmacenService $almacenService;
    private PlantaService $plantaService;
    private ReposicionService $reposicionService;
    private ProductoService $productoService;
    private Session $session;
    private AuthGuard $authGuard;
    private string $downloadsDir;

    public function __construct()
    {
        $this->almacenService = new AlmacenService();
        $this->plantaService = new PlantaService();
        $this->reposicionService = new ReposicionService();
        $this->productoService = new ProductoService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
        $this->downloadsDir = __DIR__ . '/../../public/downloads/';
    }

    /**
     * Método principal para cargar la vista de informes
     * @return array Datos para la vista
     */
    public function index(): array
    {
        try {
            $this->authGuard->requireGestorHospital();

            return [
                'success' => true
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al cargar la página de informes: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene los datos de actividad de almacenes y plantas basados en reposiciones
     *
     * @param string|null $fechaDesde Fecha inicial para el filtro (formato Y-m-d)
     * @param string|null $fechaHasta Fecha final para el filtro (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     * @return array Datos de actividad
     */
    public function getActividadAlmacenesYPlantas(?string $fechaDesde = null, ?string $fechaHasta = null,
                                                  ?int    $idAlmacen = null, ?int $idPlanta = null): array
    {
        try {
            $fechaDesdeObj = $fechaDesde ? new DateTime($fechaDesde) : new DateTime('first day of this month');
            $fechaHastaObj = $fechaHasta ? new DateTime($fechaHasta) : new DateTime();

            $reposiciones = $this->reposicionService->getReposicionesByFechas($fechaDesdeObj, $fechaHastaObj);

            if ($idAlmacen !== null) {
                $reposiciones = array_filter($reposiciones, function ($repo) use ($idAlmacen) {
                    return $repo->getDesdeAlmacen() == $idAlmacen;
                });
            }

            if ($idPlanta !== null) {
                $reposiciones = array_filter($reposiciones, function ($repo) use ($idPlanta) {
                    $botiquin = $repo->getHaciaBotiquin();
                    $almacen = $this->almacenService->getAlmacenById($botiquin);
                    return $almacen && $almacen->getIdPlanta() == $idPlanta;
                });
            }

            $movimientos = $this->convertirReposicionesAMovimientos($reposiciones);
            $datosActividad = $this->procesarMovimientosParaVista($movimientos);

            return [
                'success' => true,
                'movimientos' => $datosActividad,
                'resumen_almacenes' => $this->calcularResumenPorAlmacen($movimientos),
                'resumen_plantas' => $this->calcularResumenPorPlanta($movimientos),
                'total_entradas' => array_sum(array_column($datosActividad, 'cantidad_entrada')),
                'total_salidas' => array_sum(array_column($datosActividad, 'cantidad_salida'))
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'movimientos' => [],
                'resumen_almacenes' => [],
                'resumen_plantas' => []
            ];
        }
    }

    /**
     * Convierte reposiciones a formato de movimientos para procesamiento
     *
     * @param array $reposiciones Lista de objetos Reposicion
     * @return array Movimientos generados a partir de las reposiciones
     */
    private function convertirReposicionesAMovimientos(array $reposiciones): array
    {
        $movimientos = [];

        foreach ($reposiciones as $reposicion) {
            $idAlmacenOrigen = $reposicion->getDesdeAlmacen();
            $idBotiquin = $reposicion->getHaciaBotiquin();
            $almacenOrigen = $this->almacenService->getAlmacenById($idAlmacenOrigen);
            $botiquin = $this->almacenService->getAlmacenById($idBotiquin);
            $producto = $this->productoService->getProductoById($reposicion->getIdProducto());
            $cantidad = $reposicion->getCantidad();
            $fecha = $reposicion->getFecha();
            $tipoMovimiento = $reposicion->getTipoMovimiento();
            $tipoUbicacion = $reposicion->getTipoUbicacion();
            $idPlanta = $almacenOrigen ? $almacenOrigen->getIdPlanta() : null;
            $nombrePlanta = $idPlanta ? $this->plantaService->getPlantaById($idPlanta)->getNombre() : 'Desconocida';
            $nombreAlmacen = $almacenOrigen ? $almacenOrigen->getNombre() : 'Desconocido';
            $nombreBotiquin = $botiquin ? $botiquin->getNombre() : 'Desconocido';
            $nombreProducto = $producto ? $producto->getNombre() : 'Desconocido';
            $codigoProducto = $producto ? $producto->getCodigo() : 'N/A';
            $movimientos[] = [
                'id_almacen_origen' => $idAlmacenOrigen,
                'nombre_almacen_origen' => $nombreAlmacen,
                'id_botiquin' => $idBotiquin,
                'nombre_botiquin' => $nombreBotiquin,
                'id_planta' => $idPlanta,
                'nombre_planta' => $nombrePlanta,
                'id_producto' => $reposicion->getIdProducto(),
                'codigo_producto' => $codigoProducto,
                'nombre_producto' => $nombreProducto,
                'cantidad' => $cantidad,
                'fecha' => $fecha->format('Y-m-d H:i:s'),
                'tipo_movimiento' => $tipoMovimiento,
                'tipo_ubicacion' => $tipoUbicacion
            ];
        }
        return $movimientos;
    }

    /**
     * Procesa los movimientos para generar datos adecuados para la vista
     *
     * @param array $movimientos Lista de movimientos
     * @return array Datos procesados para la vista
     */
    private function procesarMovimientosParaVista(array $movimientos): array
    {
        $datosActividad = [];

        foreach ($movimientos as $movimiento) {
            $key = "{$movimiento['id_almacen_origen']}_{$movimiento['id_botiquin']}_{$movimiento['id_producto']}";

            if (!isset($datosActividad[$key])) {
                $datosActividad[$key] = [
                    'id_almacen_origen' => $movimiento['id_almacen_origen'],
                    'nombre_almacen_origen' => $movimiento['nombre_almacen_origen'],
                    'id_botiquin' => $movimiento['id_botiquin'],
                    'nombre_botiquin' => $movimiento['nombre_botiquin'],
                    'id_planta' => $movimiento['id_planta'],
                    'nombre_planta' => $movimiento['nombre_planta'],
                    'id_producto' => $movimiento['id_producto'],
                    'codigo_producto' => $movimiento['codigo_producto'],
                    'nombre_producto' => $movimiento['nombre_producto'],
                    'cantidad_entrada' => 0,
                    'cantidad_salida' => 0,
                    'fecha' => $movimiento['fecha']
                ];
            }

            if ($movimiento['tipo_movimiento'] === 'entrada') {
                $datosActividad[$key]['cantidad_entrada'] += $movimiento['cantidad'];
            } else {
                $datosActividad[$key]['cantidad_salida'] += $movimiento['cantidad'];
            }
        }

        return array_values($datosActividad);
    }

    /**
     * Calcula un resumen de movimientos por almacén
     *
     * @param array $movimientos Lista de movimientos
     * @return array Resumen por almacén
     */
    private function calcularResumenPorAlmacen(array $movimientos): array
    {
        $resumen = [];

        foreach ($movimientos as $movimiento) {
            $idAlmacen = $movimiento['id_almacen_origen'];
            if (!isset($resumen[$idAlmacen])) {
                $resumen[$idAlmacen] = [
                    'nombre_almacen' => $movimiento['nombre_almacen_origen'],
                    'total_entradas' => 0,
                    'total_salidas' => 0
                ];
            }
            $resumen[$idAlmacen]['total_entradas'] += $movimiento['cantidad_entrada'];
            $resumen[$idAlmacen]['total_salidas'] += $movimiento['cantidad_salida'];
        }

        return $resumen;
    }

    /**
     * Calcula un resumen de movimientos por planta
     *
     * @param array $movimientos Lista de movimientos
     * @return array Resumen por planta
     */
    private function calcularResumenPorPlanta(array $movimientos): array
    {
        $resumen = [];

        foreach ($movimientos as $movimiento) {
            $idPlanta = $movimiento['id_planta'];
            if (!isset($resumen[$idPlanta])) {
                $resumen[$idPlanta] = [
                    'nombre_planta' => $movimiento['nombre_planta'],
                    'total_entradas' => 0,
                    'total_salidas' => 0
                ];
            }
            $resumen[$idPlanta]['total_entradas'] += $movimiento['cantidad_entrada'];
            $resumen[$idPlanta]['total_salidas'] += $movimiento['cantidad_salida'];
        }

        return $resumen;
    }

    /**
     * Descarga un informe de actividad en formato CSV
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadCSV(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            $filename = "informe_actividad_" . date('YmdHis') . ".csv";
            $filepath = $this->downloadsDir . $filename;

            $file = fopen($filepath, 'w');
            fputcsv($file, ['ID Almacén', 'Nombre Almacén', 'ID Botiquín', 'Nombre Botiquín', 'ID Planta', 'Nombre Planta', 'ID Producto', 'Código Producto', 'Nombre Producto', 'Cantidad Entrada', 'Cantidad Salida', 'Fecha']);

            foreach ($datos['movimientos'] as $movimiento) {
                fputcsv($file, [
                    $movimiento['id_almacen_origen'],
                    $movimiento['nombre_almacen_origen'],
                    $movimiento['id_botiquin'],
                    $movimiento['nombre_botiquin'],
                    $movimiento['id_planta'],
                    $movimiento['nombre_planta'],
                    $movimiento['id_producto'],
                    $movimiento['codigo_producto'],
                    $movimiento['nombre_producto'],
                    $movimiento['cantidad_entrada'],
                    $movimiento['cantidad_salida'],
                    $movimiento['fecha']
                ]);
            }

            fclose($file);

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            readfile($filepath);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato PDF
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadPDF(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            // Aquí se generaría el PDF utilizando una librería como TCPDF o FPDF
            // Por simplicidad, solo se muestra un mensaje de éxito
            echo "PDF generado correctamente (simulación).";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato Excel
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadExcel(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            // Aquí se generaría el Excel utilizando una librería como PhpSpreadsheet
            // Por simplicidad, solo se muestra un mensaje de éxito
            echo "Excel generado correctamente (simulación).";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato JSON
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadJSON(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            header('Content-Type: application/json');
            echo json_encode($datos['movimientos'], JSON_PRETTY_PRINT);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato XML
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadXML(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            header('Content-Type: application/xml');
            $xml = new \SimpleXMLElement('<informe_actividad/>');

            foreach ($datos['movimientos'] as $movimiento) {
                $item = $xml->addChild('movimiento');
                foreach ($movimiento as $key => $value) {
                    $item->addChild($key, htmlspecialchars($value));
                }
            }

            echo $xml->asXML();
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato HTML
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadHTML(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            header('Content-Type: text/html');
            echo "<h1>Informe de Actividad</h1>";
            echo "<table border='1'>";
            echo "<tr><th>ID Almacén</th><th>Nombre Almacén</th><th>ID Botiquín</th><th>Nombre Botiquín</th><th>ID Planta</th><th>Nombre Planta</th><th>ID Producto</th><th>Código Producto</th><th>Nombre Producto</th><th>Cantidad Entrada</th><th>Cantidad Salida</th><th>Fecha</th></tr>";

            foreach ($datos['movimientos'] as $movimiento) {
                echo "<tr>";
                echo "<td>{$movimiento['id_almacen_origen']}</td>";
                echo "<td>{$movimiento['nombre_almacen_origen']}</td>";
                echo "<td>{$movimiento['id_botiquin']}</td>";
                echo "<td>{$movimiento['nombre_botiquin']}</td>";
                echo "<td>{$movimiento['id_planta']}</td>";
                echo "<td>{$movimiento['nombre_planta']}</td>";
                echo "<td>{$movimiento['id_producto']}</td>";
                echo "<td>{$movimiento['codigo_producto']}</td>";
                echo "<td>{$movimiento['nombre_producto']}</td>";
                echo "<td>{$movimiento['cantidad_entrada']}</td>";
                echo "<td>{$movimiento['cantidad_salida']}</td>";
                echo "<td>{$movimiento['fecha']}</td>";
                echo "</tr>";
            }

            echo "</table>";
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato TXT
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadTXT(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            header('Content-Type: text/plain');
            foreach ($datos['movimientos'] as $movimiento) {
                echo "ID Almacén: {$movimiento['id_almacen_origen']}, Nombre Almacén: {$movimiento['nombre_almacen_origen']}, ID Botiquín: {$movimiento['id_botiquin']}, Nombre Botiquín: {$movimiento['nombre_botiquin']}, ID Planta: {$movimiento['id_planta']}, Nombre Planta: {$movimiento['nombre_planta']}, ID Producto: {$movimiento['id_producto']}, Código Producto: {$movimiento['codigo_producto']}, Nombre Producto: {$movimiento['nombre_producto']}, Cantidad Entrada: {$movimiento['cantidad_entrada']}, Cantidad Salida: {$movimiento['cantidad_salida']}, Fecha: {$movimiento['fecha']}\n";
            }
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato YAML
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadYAML(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            header('Content-Type: application/x-yaml');
            echo yaml_emit($datos['movimientos']);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato PDF con una librería externa
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadPDFConLibreria(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            // Aquí se generaría el PDF utilizando una librería externa como TCPDF o FPDF
            // Por simplicidad, solo se muestra un mensaje de éxito
            echo "PDF generado correctamente con librería externa (simulación).";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato CSV con una librería externa
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadCSVConLibreria(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            // Aquí se generaría el CSV utilizando una librería externa como PhpSpreadsheet
            // Por simplicidad, solo se muestra un mensaje de éxito
            echo "CSV generado correctamente con librería externa (simulación).";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato Excel con una librería externa
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadExcelConLibreria(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            // Aquí se generaría el Excel utilizando una librería externa como PhpSpreadsheet
            // Por simplicidad, solo se muestra un mensaje de éxito
            echo "Excel generado correctamente con librería externa (simulación).";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato JSON con una librería externa
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadJSONConLibreria(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            header('Content-Type: application/json');
            echo json_encode($datos['movimientos'], JSON_PRETTY_PRINT);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato XML con una librería externa
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadXMLConLibreria(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            header('Content-Type: application/xml');
            $xml = new \SimpleXMLElement('<informe_actividad/>');

            foreach ($datos['movimientos'] as $movimiento) {
                $item = $xml->addChild('movimiento');
                foreach ($movimiento as $key => $value) {
                    $item->addChild($key, htmlspecialchars($value));
                }
            }

            echo $xml->asXML();
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Descarga un informe de actividad en formato HTML con una librería externa
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadHTMLConLibreria(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            header('Content-Type: text/html');
            echo "<h1>Informe de Actividad</h1>";
            echo "<table border='1'>";
            echo "<tr><th>ID Almacén</th><th>Nombre Almacén</th><th>ID Botiquín</th><th>Nombre Botiquín</th><th>ID Planta</th><th>Nombre Planta</th><th>ID Producto</th><th>Código Producto</th><th>Nombre Producto</th><th>Cantidad Entrada</th><th>Cantidad Salida</th><th>Fecha</th></tr>";

            foreach ($datos['movimientos'] as $movimiento) {
                echo "<tr>";
                echo "<td>{$movimiento['id_almacen_origen']}</td>";
                echo "<td>{$movimiento['nombre_almacen_origen']}</td>";
                echo "<td>{$movimiento['id_botiquin']}</td>";
                echo "<td>{$movimiento['nombre_botiquin']}</td>";
                echo "<td>{$movimiento['id_planta']}</td>";
                echo "<td>{$movimiento['nombre_planta']}</td>";
                echo "<td>{$movimiento['id_producto']}</td>";
                echo "<td>{$movimiento['codigo_producto']}</td>";
                echo "<td>{$movimiento['nombre_producto']}</td>";
                echo "<td>{$movimiento['cantidad_entrada']}</td>";
                echo "<td>{$movimiento['cantidad_salida']}</td>";
                echo "<td>{$movimiento['fecha']}</td>";
                echo "</tr>";
            }

            echo "</table>";
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }
    /**
     * Descarga un informe de actividad en formato TXT con una librería externa
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividadTXTConLibreria(string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        try {
            $datos = $this->getActividadAlmacenesYPlantas($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);

            if (!$datos['success']) {
                throw new Exception($datos['error']);
            }

            header('Content-Type: text/plain');
            foreach ($datos['movimientos'] as $movimiento) {
                echo "ID Almacén: {$movimiento['id_almacen_origen']}, Nombre Almacén: {$movimiento['nombre_almacen_origen']}, ID Botiquín: {$movimiento['id_botiquin']}, Nombre Botiquín: {$movimiento['nombre_botiquin']}, ID Planta: {$movimiento['id_planta']}, Nombre Planta: {$movimiento['nombre_planta']}, ID Producto: {$movimiento['id_producto']}, Código Producto: {$movimiento['codigo_producto']}, Nombre Producto: {$movimiento['nombre_producto']}, Cantidad Entrada: {$movimiento['cantidad_entrada']}, Cantidad Salida: {$movimiento['cantidad_salida']}, Fecha: {$movimiento['fecha']}\n";
            }
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al generar el informe: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Método para descargar un informe de actividad en múltiples formatos
     *
     * @param string $formato Formato del informe (csv, pdf, excel, json, xml, html, txt, yaml)
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     */
    public function descargarInformeActividad(string $formato, string $fechaDesde, string $fechaHasta, ?int $idAlmacen = null, ?int $idPlanta = null): void
    {
        switch (strtolower($formato)) {
            case 'csv':
                $this->descargarInformeActividadCSV($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);
                break;
            case 'pdf':
                $this->descargarInformeActividadPDF($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);
                break;
            case 'excel':
                $this->descargarInformeActividadExcel($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);
                break;
            case 'json':
                $this->descargarInformeActividadJSON($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);
                break;
            case 'xml':
                $this->descargarInformeActividadXML($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);
                break;
            case 'html':
                $this->descargarInformeActividadHTML($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);
                break;
            case 'txt':
                $this->descargarInformeActividadTXT($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);
                break;
            case 'yaml':
                $this->descargarInformeActividadYAML($fechaDesde, $fechaHasta, $idAlmacen, $idPlanta);
                break;
            default:
                http_response_code(400);
                echo "Formato no soportado.";
        }
    }
    /**
     * Obtiene la actividad de almacenes y plantas en un rango de fechas
     *
     * @param string $fechaDesde Fecha inicial del informe (formato Y-m-d)
     * @param string $fechaHasta Fecha final del informe (formato Y-m-d)
     * @param int|null $idAlmacen ID del almacén para filtrar
     * @param int|null $idPlanta ID de la planta para filtrar
     * @return array Datos de actividad
     */
}