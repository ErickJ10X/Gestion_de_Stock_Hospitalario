<?php
session_start();

require_once(__DIR__ . '/../../controller/ProductoController.php');
require_once(__DIR__ . '/../../util/Session.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\ProductoController;
use util\Session;
use util\AuthGuard;

$productoController = new ProductoController();
$session = new Session();
$authGuard = new AuthGuard();

// Verificar permisos
$authGuard->requireHospitalGestor();

// Determinar la acción a realizar
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'crear_producto':
        // Validar datos del formulario
        if (empty($_POST['nombre']) || empty($_POST['descripcion']) || empty($_POST['categoria'])) {
            $session->setMessage('error', 'Todos los campos son obligatorios');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
            exit;
        }

        // Procesar la creación
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING);

        $resultado = $productoController->store($nombre, $descripcion, $categoria);

        if (!$resultado['error']) {
            $session->setMessage('success', $resultado['mensaje']);
        } else {
            $session->setMessage('error', $resultado['mensaje']);
        }

        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
        break;

    case 'editar_producto':
        // Validar datos del formulario
        if (empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['descripcion']) || empty($_POST['categoria'])) {
            $session->setMessage('error', 'Todos los campos son obligatorios');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
            exit;
        }

        // Procesar la actualización
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING);

        $resultado = $productoController->update($id, $nombre, $descripcion, $categoria);

        if (!$resultado['error']) {
            $session->setMessage('success', $resultado['mensaje']);
        } else {
            $session->setMessage('error', $resultado['mensaje']);
        }

        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
        break;

    case 'eliminar_producto':
        // Validar datos del formulario
        if (empty($_POST['id'])) {
            $session->setMessage('error', 'ID del producto no proporcionado');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
            exit;
        }

        // Procesar la eliminación
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $resultado = $productoController->destroy($id);

        if (!$resultado['error']) {
            $session->setMessage('success', $resultado['mensaje']);
        } else {
            $session->setMessage('error', $resultado['mensaje']);
        }

        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
        break;

    default:
        $session->setMessage('error', 'Acción no válida');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
        break;
}
