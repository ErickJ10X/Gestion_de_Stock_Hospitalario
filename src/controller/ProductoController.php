<?php

namespace controller;

require_once __DIR__ . '/../model/service/ProductoService.php';
require_once __DIR__ . '/../util/Session.php';
require_once __DIR__ . '/../util/AuthGuard.php';
require_once __DIR__ . '/../util/Redirect.php';

use model\service\ProductoService;
use util\Session;
use util\AuthGuard;
use util\Redirect;
use Exception;

class ProductoController {
    private ProductoService $productoService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct() {
        $this->productoService = new ProductoService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Obtiene todos los productos
     */
    public function index(): array {
        try {
            $productos = $this->productoService->getAllProductos();
            return [
                'error' => false,
                'productos' => $productos
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener los productos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene un producto por su ID
     */
    public function getById(int $id): array {
        try {
            $producto = $this->productoService->getProductoById($id);
            if ($producto) {
                return [
                    'error' => false,
                    'producto' => $producto
                ];
            }
            return [
                'error' => true,
                'mensaje' => 'Producto no encontrado'
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener el producto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crea un nuevo producto
     */
    public function store(array $data): array {
        try {
            $this->authGuard->requireGestorHospital();
            
            // Validar datos
            if (empty($data['nombre'])) {
                return [
                    'error' => true,
                    'mensaje' => 'El nombre del producto es obligatorio'
                ];
            }
            
            $producto = $this->productoService->createProducto($data);
            return [
                'error' => false,
                'mensaje' => 'Producto creado correctamente',
                'producto' => $producto
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al crear el producto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualiza un producto existente
     */
    public function update(int $id, array $data): array {
        try {
            $this->authGuard->requireGestorHospital();
            
            // Validar datos
            if (empty($data['nombre'])) {
                return [
                    'error' => true,
                    'mensaje' => 'El nombre del producto es obligatorio'
                ];
            }
            
            $producto = $this->productoService->updateProducto($id, $data);
            return [
                'error' => false,
                'mensaje' => 'Producto actualizado correctamente',
                'producto' => $producto
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al actualizar el producto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Elimina un producto
     */
    public function destroy(int $id): array {
        try {
            $this->authGuard->requireGestorHospital();
            
            $resultado = $this->productoService->deleteProducto($id);
            if ($resultado) {
                return [
                    'error' => false,
                    'mensaje' => 'Producto eliminado correctamente'
                ];
            }
            return [
                'error' => true,
                'mensaje' => 'No se pudo eliminar el producto'
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al eliminar el producto: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Busca productos por término de búsqueda
     */
    public function search(string $term): array {
        try {
            $productos = $this->productoService->searchProductos($term);
            return [
                'error' => false,
                'productos' => $productos
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al buscar productos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Procesa la solicitud HTTP
     */
    public function processRequest(): void {
        try {
            $action = $_GET['action'] ?? '';
            
            switch ($action) {
                case 'create':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $resultado = $this->store($_POST);
                        if (!$resultado['error']) {
                            $this->session->setMessage('success', $resultado['mensaje']);
                            Redirect::to('/src/view/productos/');
                        } else {
                            $this->session->setMessage('error', $resultado['mensaje']);
                            Redirect::to('/src/view/productos/create.php');
                        }
                    } else {
                        Redirect::to('/src/view/productos/create.php');
                    }
                    break;

                case 'edit':
                    if (!isset($_GET['id'])) {
                        $this->session->setMessage('error', 'ID de producto no especificado');
                        Redirect::to('/src/view/productos/');
                    }
                    
                    $id = (int)$_GET['id'];
                    
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $resultado = $this->update($id, $_POST);
                        if (!$resultado['error']) {
                            $this->session->setMessage('success', $resultado['mensaje']);
                            Redirect::to('/src/view/productos/');
                        } else {
                            $this->session->setMessage('error', $resultado['mensaje']);
                            Redirect::to("/src/view/productos/edit.php?id={$id}");
                        }
                    } else {
                        Redirect::to("/src/view/productos/edit.php?id={$id}");
                    }
                    break;

                case 'delete':
                    if (!isset($_GET['id'])) {
                        $this->session->setMessage('error', 'ID de producto no especificado');
                        Redirect::to('/src/view/productos/');
                    }
                    
                    $id = (int)$_GET['id'];
                    $resultado = $this->destroy($id);
                    
                    if (!$resultado['error']) {
                        $this->session->setMessage('success', $resultado['mensaje']);
                    } else {
                        $this->session->setMessage('error', $resultado['mensaje']);
                    }
                    Redirect::to('/src/view/productos/');
                    break;

                case 'search':
                    if (!isset($_GET['term'])) {
                        $this->session->setMessage('error', 'Término de búsqueda no especificado');
                        Redirect::to('/src/view/productos/');
                    }
                    
                    $term = trim($_GET['term']);
                    $resultado = $this->search($term);
                    
                    if (!$resultado['error']) {
                        // Aquí podrías devolver JSON para una búsqueda AJAX, por ejemplo
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'productos' => array_map(function($p) {
                                return [
                                    'id' => $p->getId(),
                                    'nombre' => $p->getNombre(),
                                    'descripcion' => $p->getDescripcion(),
                                    'precio' => $p->getPrecio()
                                ];
                            }, $resultado['productos'])
                        ]);
                        exit;
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => $resultado['mensaje']
                        ]);
                        exit;
                    }
                    break;
                
                default:
                    // Acción por defecto: mostrar lista
                    Redirect::to('/src/view/productos/');
                    break;
            }
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            Redirect::to('/src/view/productos/');
        }
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new ProductoController();
    $controller->processRequest();
}
