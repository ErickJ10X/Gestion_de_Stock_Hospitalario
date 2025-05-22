<?php
session_start();
require_once(__DIR__ . '/../../controller/ProductosController.php');
require_once(__DIR__ . '/../../util/Session.php');
require_once(__DIR__ . '/../../util/Redirect.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\ProductosController;
use util\Session;
use util\Redirect;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->requireAuth();

$productosController = new ProductosController();
$session = new Session();
$redirect = new Redirect();

// Verificar que la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $redirect->to('/src/view/productos/lista_productos.php?error=1&message=' . urlencode('Método no permitido'));
    exit();
}

// Obtener la acción a realizar
$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    switch ($action) {
        case 'create':
            // Validar y obtener los datos del formulario
            $codigo = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_STRING);
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
            $unidad_medida = filter_input(INPUT_POST, 'unidad_medida', FILTER_SANITIZE_STRING);
            
            if (empty($codigo) || empty($nombre) || empty($descripcion) || empty($unidad_medida)) {
                throw new Exception("Todos los campos son obligatorios");
            }
            
            $result = $productosController->createProducto($codigo, $nombre, $descripcion, $unidad_medida);
            
            if ($result) {
                $session->setMessage('success', 'Producto creado exitosamente');
            } else {
                $session->setMessage('error', 'Error al crear el producto');
            }
            break;
            
        case 'update':
            // Validar y obtener los datos del formulario
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $codigo = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_STRING);
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
            $unidad_medida = filter_input(INPUT_POST, 'unidad_medida', FILTER_SANITIZE_STRING);
            
            if (!$id || empty($codigo) || empty($nombre) || empty($descripcion) || empty($unidad_medida)) {
                throw new Exception("Todos los campos son obligatorios");
            }
            
            $result = $productosController->updateProducto($id, $codigo, $nombre, $descripcion, $unidad_medida);
            
            if ($result) {
                $session->setMessage('success', 'Producto actualizado exitosamente');
            } else {
                $session->setMessage('error', 'Error al actualizar el producto');
            }
            break;
            
        case 'delete':
            // Validar y obtener el ID del producto
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            
            if (!$id) {
                throw new Exception("ID de producto no válido");
            }

            $result = $productosController->deleteProducto($id);
            
            if ($result) {
                $session->setMessage('success', 'Producto eliminado exitosamente');
            } else {
                $session->setMessage('error', 'Error al eliminar el producto');
            }
            break;
            
        default:
            throw new Exception("Acción no reconocida");
    }
    
    // Redirigir de vuelta a la lista de productos
    $redirect->to('/src/view/productos/lista_productos.php');
    
} catch (Exception $e) {
    $session->setMessage('error', 'Error: ' . $e->getMessage());
    $redirect->to('/src/view/productos/lista_productos.php');
}
