<?php

namespace controller;

use Exception;
use model\service\MovimientosService;
use model\service\ReposicionesService;

require_once(__DIR__ . '/../model/service/MovimientosService.php');
require_once(__DIR__ . '/../controller/HospitalController.php');
require_once(__DIR__ . '/../controller/PlantaController.php');
require_once(__DIR__ . '/../controller/AlmacenesController.php');

class InformesController
{
    private MovimientosService $movimientosService;
    
    public function __construct()
    {
        $this->movimientosService = new MovimientosService();
    }

    public function index(): void
    {
        include(__DIR__ . '/../view/informes/index.php');
    }

    public function actividadPorAlmacenPlanta(): array
    {
        try {
            $hospitalController = new HospitalController();
            $plantaController = new PlantaController();
            $almacenesController = new AlmacenesController();
            
            $hospitales = $hospitalController->index();
            
            if (isset($_GET['hospital_id']) && is_numeric($_GET['hospital_id'])) {
                $hospital_id = $_GET['hospital_id'];
                $plantas = $plantaController->getByHospital($hospital_id);
            } else {
                $plantas = ['error' => false, 'plantas' => []];
            }
            
            $almacenes = $almacenesController->index();
            
            $actividad = [];
            
            if (isset($_GET['almacen_id']) && is_numeric($_GET['almacen_id'])) {
                $almacen_id = $_GET['almacen_id'];
                $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d', strtotime('-30 days'));
                $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
                
                // Obtener movimientos de este almacén
                $actividad = $this->movimientosService->getMovimientosByAlmacen(
                    $almacen_id, 
                    $fecha_inicio, 
                    $fecha_fin
                );
            }
            
            // Incluir la vista
            include(__DIR__ . '/../view/informes/actividad_almacen_planta.php');
            return ['error' => false];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function exportarExcel($tipo): array
    {
        try {
            if ($tipo === 'actividad_almacen') {
                $almacen_id = $_GET['almacen_id'] ?? null;
                $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
                $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
                
                if (!$almacen_id) {
                    return ['error' => true, 'mensaje' => 'ID de almacén requerido'];
                }
                
                $actividad = $this->movimientosService->getMovimientosByAlmacen(
                    $almacen_id,
                    $fecha_inicio,
                    $fecha_fin
                );
                
                // Exportar a Excel
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=actividad_almacen_' . date('Y-m-d') . '.csv');
                
                $output = fopen('php://output', 'w');
                
                // Encabezados del CSV
                fputcsv($output, [
                    'Fecha',
                    'Hospital',
                    'Planta',
                    'Almacén',
                    'Producto',
                    'Tipo',
                    'Cantidad',
                    'Usuario'
                ]);
                
                // Datos
                foreach ($actividad as $movimiento) {
                    fputcsv($output, [
                        $movimiento['fecha_movimiento'],
                        $movimiento['nombre_hospital'],
                        $movimiento['nombre_planta'],
                        $movimiento['tipo_almacen'],
                        $movimiento['nombre_producto'],
                        $movimiento['tipo_movimiento'],
                        $movimiento['cantidad'],
                        $movimiento['nombre_usuario']
                    ]);
                }
                
                fclose($output);
                exit;
            }
            
            return ['error' => true, 'mensaje' => 'Tipo de exportación no soportado'];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
