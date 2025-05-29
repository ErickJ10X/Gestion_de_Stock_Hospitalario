<?php
session_start();
require_once(__DIR__ . '/../../controller/ProductoController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\ProductoController;
use util\Session;
use util\AuthGuard;

$productoController = new ProductoController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'crear_producto':
            crearProducto();
            break;
        case 'editar_producto':
            editarProducto();
            break;
        case 'eliminar_producto':
            eliminarProducto();
            break;
        default:
            $session->setMessage('error', 'Acción de producto no reconocida');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
            exit;
    }
} else {
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
    exit;
}

function crearProducto() {
    global $productoController, $session;
    
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $unidadMedida = isset($_POST['unidad_medida']) ? trim($_POST['unidad_medida']) : '';
    
    if (empty($nombre) || empty($codigo)) {
        $session->setMessage('modal_error_producto', 'El nombre y código del producto son obligatorios');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
        exit;
    }
    
    $response = $productoController->store($codigo, $nombre, $descripcion, $unidadMedida);
    
    if ($response['error']) {
        $session->setMessage('modal_error_producto', $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
    exit;
}

function editarProducto() {
    global $productoController, $session;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $unidadMedida = isset($_POST['unidad_medida']) ? trim($_POST['unidad_medida']) : '';
    
    if ($id <= 0 || empty($nombre) || empty($codigo)) {
        $session->setMessage('modal_error_producto_' . $id, 'El ID, nombre y código del producto son obligatorios');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
        exit;
    }
    
    $response = $productoController->update($id, $codigo, $nombre, $descripcion, $unidadMedida);
    
    if ($response['error']) {
        $session->setMessage('modal_error_producto_' . $id, $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
    exit;
}

function eliminarProducto() {
    global $productoController, $session;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id <= 0) {
        $session->setMessage('error', 'ID de producto inválido');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
        exit;
    }
    
    $response = $productoController->destroy($id);
    
    if ($response['error']) {
        $session->setMessage('error', $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
    exit;
}
