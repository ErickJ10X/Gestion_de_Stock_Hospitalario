<?php

namespace controller;

use Exception;
use model\entity\CatalogoProducto;
use model\service\CatalogoProductoService;
use model\repository\CatalogoProductoRepository;
use util\Session;
use util\AuthGuard;

require_once(__DIR__ . '/../model/service/CatalogoProductoService.php');
require_once(__DIR__ . '/../model/repository/CatalogoProductoRepository.php');
require_once(__DIR__ . '/../model/entity/CatalogoProducto.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

class CatalogosController
{
    private CatalogoProductoService $catalogoService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct()
    {
        $this->catalogoService = new CatalogoProductoService(new CatalogoProductoRepository());
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Método principal para obtener los catálogos
     * @return array Datos para la vista de catálogos
     */
    public function index(): array
    {
        $this->authGuard->requireGestorHospital();
        
        $viewData = [
            'catalogos' => []
        ];

        try {
            $viewData['catalogos'] = $this->catalogoService->getAllCatalogos();
            return $viewData;
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al cargar los catálogos: ' . $e->getMessage());
            return $viewData;
        }
    }

    /**
     * Obtiene catálogos por planta
     * @param int $idPlanta ID de la planta
     * @return array Lista de catálogos de una planta específica
     */
    public function getByPlanta(int $idPlanta): array
    {
        try {
            return $this->catalogoService->getCatalogosByPlanta($idPlanta);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtiene catálogos por producto
     * @param int $idProducto ID del producto
     * @return array Lista de catálogos de un producto específico
     */
    public function getByProducto(int $idProducto): array
    {
        try {
            return $this->catalogoService->getCatalogosByProducto($idProducto);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Verifica si un producto está en el catálogo de una planta
     * @param int $idProducto ID del producto
     * @param int $idPlanta ID de la planta
     * @return bool true si el producto está en el catálogo, false en caso contrario
     */
    public function verificarProductoEnCatalogo(int $idProducto, int $idPlanta): bool
    {
        try {
            return $this->catalogoService->verificarProductoEnCatalogo($idProducto, $idPlanta);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Crea un nuevo catálogo
     */
    public function crear(): void
    {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $idProducto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
            $idPlanta = isset($_POST['id_planta']) ? (int)$_POST['id_planta'] : 0;
            
            // Validaciones
            if ($idProducto <= 0) {
                throw new Exception('El producto es obligatorio');
            }
            
            if ($idPlanta <= 0) {
                throw new Exception('La planta es obligatoria');
            }
            
            // Verificar si ya existe el catálogo
            if ($this->verificarProductoEnCatalogo($idProducto, $idPlanta)) {
                throw new Exception('Este producto ya está en el catálogo de la planta');
            }
            
            // Crear el catálogo
            $data = [
                'id_producto' => $idProducto,
                'id_planta' => $idPlanta,
                'activo' => true
            ];
            
            $this->catalogoService->createCatalogo($data);
            
            $this->session->setMessage('success', 'Producto añadido al catálogo correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al añadir producto al catálogo: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Actualiza un catálogo existente
     */
    public function editar(): void
    {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $idProducto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
            $idPlanta = isset($_POST['id_planta']) ? (int)$_POST['id_planta'] : 0;
            $activo = isset($_POST['activo']) ? (bool)$_POST['activo'] : true;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de catálogo inválido');
            }
            
            if ($idProducto <= 0) {
                throw new Exception('El producto es obligatorio');
            }
            
            if ($idPlanta <= 0) {
                throw new Exception('La planta es obligatoria');
            }
            
            // Actualizar el catálogo
            $data = [
                'id_producto' => $idProducto,
                'id_planta' => $idPlanta,
                'activo' => $activo
            ];
            
            $this->catalogoService->updateCatalogo($id, $data);
            
            $this->session->setMessage('success', 'Catálogo actualizado correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al actualizar catálogo: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Elimina un catálogo
     */
    public function eliminar(): void
    {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de catálogo inválido');
            }
            
            // Eliminar el catálogo (soft delete)
            $eliminado = $this->catalogoService->desactivarCatalogo($id);
            
            if ($eliminado) {
                $this->session->setMessage('success', 'Producto eliminado del catálogo correctamente');
            } else {
                throw new Exception('No se pudo eliminar el producto del catálogo');
            }
            
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al eliminar producto del catálogo: ' . $e->getMessage());
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
                case 'getByPlanta':
                    $idPlanta = isset($_GET['idPlanta']) ? (int)$_GET['idPlanta'] : 0;
                    $catalogos = $this->getByPlanta($idPlanta);
                    
                    echo json_encode([
                        'error' => false,
                        'catalogos' => array_map(function($c) { return $c->toArray(); }, $catalogos)
                    ]);
                    break;
                    
                case 'getByProducto':
                    $idProducto = isset($_GET['idProducto']) ? (int)$_GET['idProducto'] : 0;
                    $catalogos = $this->getByProducto($idProducto);
                    
                    echo json_encode([
                        'error' => false,
                        'catalogos' => array_map(function($c) { return $c->toArray(); }, $catalogos)
                    ]);
                    break;
                    
                case 'verificarProductoEnCatalogo':
                    $idProducto = isset($_GET['idProducto']) ? (int)$_GET['idProducto'] : 0;
                    $idPlanta = isset($_GET['idPlanta']) ? (int)$_GET['idPlanta'] : 0;
                    $enCatalogo = $this->verificarProductoEnCatalogo($idProducto, $idPlanta);
                    
                    echo json_encode([
                        'error' => false,
                        'enCatalogo' => $enCatalogo
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
     * Redirecciona a la página de índice de catálogos
     */
    private function redirectToIndex(): void
    {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/catalogos/index.php');
        exit;
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new CatalogosController();
    $controller->processRequest();
}
