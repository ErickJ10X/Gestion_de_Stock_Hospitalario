<?php

namespace controller;

use Exception;
use DateTime;
use model\entity\LecturaStock;
use model\service\LecturaStockService;
use model\repository\LecturaStockRepository;
use model\repository\BotiquinRepository;
use model\repository\ProductoRepository;
use util\Session;
use util\AuthGuard;

require_once(__DIR__ . '/../model/service/LecturaStockService.php');
require_once(__DIR__ . '/../model/repository/LecturaStockRepository.php');
require_once(__DIR__ . '/../model/repository/BotiquinRepository.php');
require_once(__DIR__ . '/../model/repository/ProductoRepository.php');
require_once(__DIR__ . '/../model/entity/LecturaStock.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

class LecturasStockController
{
    private LecturaStockService $lecturaService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct()
    {
        $botiquinRepository = new BotiquinRepository();
        $productoRepository = new ProductoRepository();
        $lecturaRepository = new LecturaStockRepository();
        
        $this->lecturaService = new LecturaStockService(
            $lecturaRepository,
            $botiquinRepository,
            $productoRepository
        );
        
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Método principal para obtener todas las lecturas de stock
     * @return array Datos para la vista de lecturas de stock
     */
    public function index(): array
    {
        $this->authGuard->requireGestorHospital();
        
        try {
            $lecturas = $this->lecturaService->getAllLecturas();
            
            return [
                'error' => false,
                'lecturas' => $lecturas
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al cargar las lecturas: ' . $e->getMessage());
            
            return [
                'error' => true,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene una lectura por su ID
     * @param int $id ID de la lectura
     * @return array Respuesta con la lectura encontrada o mensaje de error
     */
    public function show(int $id): array
    {
        try {
            $lectura = $this->lecturaService->getLecturaById($id);
            
            if (!$lectura) {
                return [
                    'error' => true,
                    'mensaje' => 'Lectura no encontrada'
                ];
            }
            
            // Obtener datos adicionales para mostrar
            $botiquin = $lectura->getBotiquin();
            $producto = $lectura->getProducto();
            $usuario = $lectura->getUsuario();
            
            $resultado = [
                'id_lectura' => $lectura->getIdLectura(),
                'id_producto' => $lectura->getIdProducto(),
                'id_botiquin' => $lectura->getIdBotiquin(),
                'cantidad_disponible' => $lectura->getCantidadDisponible(),
                'fecha_lectura' => $lectura->getFechaLectura()->format('Y-m-d H:i:s'),
                'registrado_por' => $lectura->getRegistradoPor()
            ];
            
            // Añadir datos adicionales si están disponibles
            if ($botiquin) {
                $resultado['nombre_botiquin'] = $botiquin->getNombre();
            }
            
            if ($producto) {
                $resultado['codigo_producto'] = $producto->getCodigo();
                $resultado['nombre_producto'] = $producto->getNombre();
                $resultado['unidad_medida'] = $producto->getUnidadMedida();
            }
            
            if ($usuario) {
                $resultado['nombre_usuario'] = $usuario->getNombre();
            }
            
            return [
                'error' => false,
                'lectura' => $resultado
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener la lectura: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene lecturas por botiquín
     * @param int $idBotiquin ID del botiquín
     * @return array Lista de lecturas del botiquín específico
     */
    public function getByBotiquin(int $idBotiquin): array
    {
        try {
            $lecturas = $this->lecturaService->getLecturasByBotiquin($idBotiquin);
            
            return [
                'error' => false,
                'lecturas' => $lecturas
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener las lecturas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene lecturas por producto
     * @param int $idProducto ID del producto
     * @return array Lista de lecturas del producto específico
     */
    public function getByProducto(int $idProducto): array
    {
        try {
            $lecturas = $this->lecturaService->getLecturasByProducto($idProducto);
            
            return [
                'error' => false,
                'lecturas' => $lecturas
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener las lecturas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las últimas lecturas por botiquín
     * @param int $idBotiquin ID del botiquín
     * @return array Lista de las últimas lecturas por producto para el botiquín
     */
    public function getUltimasLecturas(int $idBotiquin): array
    {
        try {
            $lecturas = $this->lecturaService->getUltimasLecturasPorBotiquin($idBotiquin);
            
            return [
                'error' => false,
                'lecturas' => $lecturas
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener las últimas lecturas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene datos para próximas lecturas
     * @param int|null $idBotiquin ID del botiquín (opcional)
     * @return array Datos para próximas lecturas
     */
    public function getProximasLecturas(?int $idBotiquin = null): array
    {
        try {
            // Lógica para determinar próximas lecturas
            // Por ahora, obtenemos las últimas lecturas y simulamos próximas fechas
            
            if ($idBotiquin) {
                $ultimasLecturas = $this->lecturaService->getUltimasLecturasPorBotiquin($idBotiquin);
            } else {
                // Lógica para obtener últimas lecturas de todos los botiquines
                // Esto podría implementarse en el servicio
                $ultimasLecturas = [];
                
                // Obtener todos los botiquines
                $botiquinRepo = new BotiquinRepository();
                $botiquines = $botiquinRepo->findAll();
                
                foreach ($botiquines as $botiquin) {
                    $lecturas = $this->lecturaService->getUltimasLecturasPorBotiquin($botiquin->getIdBotiquin());
                    $ultimasLecturas = array_merge($ultimasLecturas, $lecturas);
                }
            }
            
            // Preparar datos de respuesta con información adicional
            $resultado = [];
            
            foreach ($ultimasLecturas as $lectura) {
                $fechaUltimaLectura = $lectura->getFechaLectura();
                
                // Calcular próxima fecha (ejemplo: 15 días después de la última)
                $fechaProxima = clone $fechaUltimaLectura;
                $fechaProxima->modify('+15 days');
                
                // Obtener datos adicionales
                $botiquin = $lectura->getBotiquin();
                $producto = $lectura->getProducto();
                
                $item = [
                    'id_lectura' => $lectura->getIdLectura(),
                    'id_botiquin' => $lectura->getIdBotiquin(),
                    'nombre_botiquin' => $botiquin ? $botiquin->getNombre() : 'Desconocido',
                    'id_producto' => $lectura->getIdProducto(),
                    'codigo_producto' => $producto ? $producto->getCodigo() : 'Desconocido',
                    'nombre_producto' => $producto ? $producto->getNombre() : 'Desconocido',
                    'cantidad_disponible' => $lectura->getCantidadDisponible(),
                    'ultima_fecha_lectura' => $fechaUltimaLectura->format('Y-m-d H:i:s'),
                    'fecha_proxima_lectura' => $fechaProxima->format('Y-m-d H:i:s')
                ];
                
                $resultado[] = $item;
            }
            
            return [
                'error' => false,
                'lecturas' => $resultado
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener próximas lecturas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Registra una nueva lectura de stock
     */
    public function store(): void
    {
        $this->authGuard->requireGestorHospital();
        
        try {
            $idProducto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
            $idBotiquin = isset($_POST['id_botiquin']) ? (int)$_POST['id_botiquin'] : 0;
            $cantidadDisponible = isset($_POST['cantidad_disponible']) ? (int)$_POST['cantidad_disponible'] : 0;
            $fechaLectura = $_POST['fecha_lectura'] ?? null;
            $registradoPor = isset($_POST['registrado_por']) ? (int)$_POST['registrado_por'] : 0;
            
            // Validaciones
            if ($idProducto <= 0) {
                throw new Exception('El producto es obligatorio');
            }
            
            if ($idBotiquin <= 0) {
                throw new Exception('El botiquín es obligatorio');
            }
            
            if ($cantidadDisponible < 0) {
                throw new Exception('La cantidad no puede ser negativa');
            }
            
            if (!$registradoPor) {
                $registradoPor = $_SESSION['user_id'] ?? 0;
            }
            
            if (!$registradoPor) {
                throw new Exception('No se pudo identificar al usuario que registra');
            }
            
            // Preparar datos para crear la lectura
            $data = [
                'id_producto' => $idProducto,
                'id_botiquin' => $idBotiquin,
                'cantidad_disponible' => $cantidadDisponible,
                'registrado_por' => $registradoPor
            ];
            
            if ($fechaLectura) {
                $data['fecha_lectura'] = new DateTime($fechaLectura);
            }
            
            // Registrar la lectura
            $this->lecturaService->registrarLectura($data);
            
            $this->session->setMessage('success', 'Lectura de stock registrada correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al registrar lectura: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Elimina una lectura de stock
     * @param int $id ID de la lectura a eliminar
     * @return array Respuesta de la operación
     */
    public function destroy(int $id): array
    {
        $this->authGuard->requireGestorHospital();
        
        try {
            if ($id <= 0) {
                throw new Exception('ID de lectura inválido');
            }
            
            $eliminado = $this->lecturaService->eliminarLectura($id);
            
            if ($eliminado) {
                return [
                    'error' => false,
                    'mensaje' => 'Lectura eliminada correctamente'
                ];
            } else {
                return [
                    'error' => true,
                    'mensaje' => 'No se pudo eliminar la lectura'
                ];
            }
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al eliminar lectura: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Analiza el inventario de un botiquín
     * @param int $idBotiquin ID del botiquín
     * @return array Resultados del análisis
     */
    public function analizarInventario(int $idBotiquin): array
    {
        try {
            if ($idBotiquin <= 0) {
                throw new Exception('ID de botiquín inválido');
            }
            
            $resultados = $this->lecturaService->analizarInventario($idBotiquin);
            
            return [
                'error' => false,
                'resultados' => $resultados
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al analizar inventario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Procesa solicitudes API para obtener datos
     */
    public function processApiRequest(): void
    {
        header('Content-Type: application/json');
        
        try {
            $action = $_GET['action'] ?? '';
            
            switch ($action) {
                case 'show':
                    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                    echo json_encode($this->show($id));
                    break;
                    
                case 'getByBotiquin':
                    $idBotiquin = isset($_GET['botiquin_id']) ? (int)$_GET['botiquin_id'] : 0;
                    echo json_encode($this->getByBotiquin($idBotiquin));
                    break;
                    
                case 'getByProducto':
                    $idProducto = isset($_GET['producto_id']) ? (int)$_GET['producto_id'] : 0;
                    echo json_encode($this->getByProducto($idProducto));
                    break;
                    
                case 'getUltimasLecturas':
                    $idBotiquin = isset($_GET['botiquin_id']) ? (int)$_GET['botiquin_id'] : 0;
                    echo json_encode($this->getUltimasLecturas($idBotiquin));
                    break;
                    
                case 'getProximasLecturas':
                    $idBotiquin = isset($_GET['botiquin_id']) ? (int)$_GET['botiquin_id'] : null;
                    echo json_encode($this->getProximasLecturas($idBotiquin));
                    break;
                    
                case 'analizarInventario':
                    $idBotiquin = isset($_GET['botiquin_id']) ? (int)$_GET['botiquin_id'] : 0;
                    echo json_encode($this->analizarInventario($idBotiquin));
                    break;
                    
                default:
                    echo json_encode([
                        'error' => true,
                        'mensaje' => 'Acción no reconocida'
                    ]);
                    break;
            }
        } catch (Exception $e) {
            echo json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Procesa las solicitudes POST y GET
     */
    public function processRequest(): void
    {
        // Determinar si es una solicitud API
        $isApiRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                        
        if ($isApiRequest && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->processApiRequest();
            return;
        }
        
        // Si no es una petición POST, no hay nada que hacer
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'store':
                $this->store();
                break;
                
            default:
                $this->session->setMessage('error', 'Acción no reconocida');
                $this->redirectToIndex();
                break;
        }
    }
    
    /**
     * Redirecciona a la página de índice de lecturas de stock
     */
    private function redirectToIndex(): void
    {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/lecturaStock/index.php');
        exit;
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new LecturasStockController();
    $controller->processRequest();
}
