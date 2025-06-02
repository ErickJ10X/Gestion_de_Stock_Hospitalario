<?php
session_start();
require_once(__DIR__ . '/../controller/CatalogosController.php');
require_once(__DIR__ . '/../controller/ProductoController.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

use controller\CatalogosController;
use controller\ProductoController;
use util\AuthGuard;

header('Content-Type: application/json');

$authGuard = new AuthGuard();
// Verificar que el usuario esté autenticado
if (!$authGuard->isHospitalGestor()) {
    echo json_encode(['error' => true, 'mensaje' => 'No tiene permisos para realizar esta acción']);
    exit;
}

$catalogosController = new CatalogosController();
$productoController = new ProductoController();

// Obtener productos por planta
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['planta_id'])) {
    $plantaId = intval($_GET['planta_id']);
    
    if (!$plantaId) {
        echo json_encode(['error' => true, 'mensaje' => 'ID de planta inválido']);
        exit;
    }
    
    $response = $catalogosController->getByPlanta($plantaId);
    
    if ($response['error']) {
        echo json_encode($response);
        exit;
    }
    
    $catalogos = $response['catalogos'];
    $productos = [];
    
    foreach ($catalogos as $catalogo) {
        $productoInfo = $productoController->show($catalogo->getIdProducto());
        
        if (!$productoInfo['error'] && isset($productoInfo['producto'])) {
            $producto = $productoInfo['producto'];
            $productos[] = [
                'id_catalogo' => $catalogo->getIdCatalogo(),
                'id_producto' => $producto->getIdProducto(),
                'nombre' => $producto->getNombre(),
                'descripcion' => $producto->getDescripcion(),
                'fabricante' => $producto->getFabricante(),
                'referencia' => $producto->getReferencia()
            ];
        }
    }
    
    echo json_encode(['error' => false, 'productos' => $productos]);
    exit;
}

// Eliminar producto del catálogo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['catalogo_id'])) {
    $catalogoId = intval($_POST['catalogo_id']);
    
    if (!$catalogoId) {
        echo json_encode(['error' => true, 'mensaje' => 'ID de catálogo inválido']);
        exit;
    }
    
    $response = $catalogosController->destroy($catalogoId);
    echo json_encode($response);
    exit;
}

// Agregar producto al catálogo (si quieres implementar esta funcionalidad)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add' && isset($_POST['producto_id']) && isset($_POST['planta_id'])) {
    $productoId = intval($_POST['producto_id']);
    $plantaId = intval($_POST['planta_id']);
    
    if (!$productoId || !$plantaId) {
        echo json_encode(['error' => true, 'mensaje' => 'ID de producto o planta inválido']);
        exit;
    }
    
    $response = $catalogosController->store($productoId, $plantaId);
    echo json_encode($response);
    exit;
}

// Si no se ha manejado ninguna de las peticiones anteriores
echo json_encode(['error' => true, 'mensaje' => 'Petición no válida']);
