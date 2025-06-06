<?php

namespace controller;

use Exception;
use model\entity\Producto;
use model\service\ProductoService;
use model\repository\ProductoRepository;
use util\Session;
use util\AuthGuard;

require_once(__DIR__ . '/../model/service/ProductoService.php');
require_once(__DIR__ . '/../model/repository/ProductoRepository.php');
require_once(__DIR__ . '/../model/entity/Producto.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

class ProductoController
{
    private ProductoService $productoService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct()
    {
        $this->productoService = new ProductoService(new ProductoRepository());
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Método principal para obtener los productos
     * @return array Datos para la vista de productos
     */
    public function index(): array
    {
        $this->authGuard->requireGestorHospital();
        
        $viewData = [
            'productos' => []
        ];

        try {
            $viewData['productos'] = $this->productoService->getAllProductos();
            return $viewData;
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al cargar los productos: ' . $e->getMessage());
            return $viewData;
        }
    }

    /**
     * Obtiene productos activos
     * @return array Lista de productos activos
     */
    public function getActiveProductos(): array
    {
        try {
            return $this->productoService->getActiveProductos();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al cargar los productos activos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un producto por su ID
     * @param int $id ID del producto
     * @return Producto|null El producto encontrado o null
     */
    public function getById(int $id): ?Producto
    {
        try {
            return $this->productoService->getProductoById($id);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Obtiene un producto por su código
     * @param string $codigo Código del producto
     * @return Producto|null El producto encontrado o null
     */
    public function getByCodigo(string $codigo): ?Producto
    {
        try {
            return $this->productoService->getProductoByCodigo($codigo);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Buscar productos por nombre o código
     * @param string $termino Término de búsqueda
     * @return array Lista de productos que coinciden con el término
     */
    public function search(string $termino): array
    {
        try {
            return $this->productoService->buscarProductos($termino);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Crea un nuevo producto
     */
    public function crear(): void
    {
        $this->authGuard->requireGestorHospital();
        
        try {
            $codigo = $_POST['codigo'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $unidadMedida = $_POST['unidad_medida'] ?? '';
            
            // Crear el producto
            $data = [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'unidad_medida' => $unidadMedida,
                'activo' => true
            ];
            
            $this->productoService->createProducto($data);
            
            $this->session->setMessage('success', 'Producto creado correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al crear producto: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Actualiza un producto existente
     */
    public function editar(): void
    {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $codigo = $_POST['codigo'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $unidadMedida = $_POST['unidad_medida'] ?? '';
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de producto inválido');
            }
            
            // Actualizar el producto
            $data = [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'unidad_medida' => $unidadMedida
            ];
            
            $this->productoService->updateProducto($id, $data);
            
            $this->session->setMessage('success', 'Producto actualizado correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al actualizar producto: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Elimina un producto
     */
    public function eliminar(): void
    {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de producto inválido');
            }
            
            // Eliminar el producto (soft delete)
            $eliminado = $this->productoService->desactivarProducto($id);
            
            if ($eliminado) {
                $this->session->setMessage('success', 'Producto eliminado correctamente');
            } else {
                throw new Exception('No se pudo eliminar el producto');
            }
            
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al eliminar producto: ' . $e->getMessage());
            $this->redirectToIndex();
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
                case 'getById':
                    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                    $producto = $this->getById($id);
                    
                    if ($producto) {
                        echo json_encode([
                            'error' => false,
                            'producto' => $producto->toArray()
                        ]);
                    } else {
                        echo json_encode([
                            'error' => true,
                            'mensaje' => 'Producto no encontrado'
                        ]);
                    }
                    break;
                    
                case 'getByCodigo':
                    $codigo = $_GET['codigo'] ?? '';
                    $producto = $this->getByCodigo($codigo);
                    
                    if ($producto) {
                        echo json_encode([
                            'error' => false,
                            'producto' => $producto->toArray()
                        ]);
                    } else {
                        echo json_encode([
                            'error' => true,
                            'mensaje' => 'Producto no encontrado'
                        ]);
                    }
                    break;
                    
                case 'search':
                    $termino = $_GET['termino'] ?? '';
                    $productos = $this->search($termino);
                    
                    echo json_encode([
                        'error' => false,
                        'productos' => array_map(function($p) { return $p->toArray(); }, $productos)
                    ]);
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
            case 'crear':
                $this->crear();
                break;
            case 'editar':
                $this->editar();
                break;
            case 'eliminar':
                $this->eliminar();
                break;
            default:
                $this->session->setMessage('error', 'Acción no reconocida');
                $this->redirectToIndex();
                break;
        }
    }
    
    /**
     * Redirecciona a la página de índice de productos
     */
    private function redirectToIndex(): void
    {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/index.php');
        exit;
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new ProductoController();
    $controller->processRequest();
}
