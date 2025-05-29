<?php
session_start();

require_once(__DIR__ . '/../../controller/PactosController.php');
require_once(__DIR__ . '/../../util/Session.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\PactosController;
use util\Session;
use util\AuthGuard;

$pactosController = new PactosController();
$session = new Session();
$authGuard = new AuthGuard();

// Verificar permisos
$authGuard->requireHospitalGestor();

// Determinar la acción a realizar
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'crear_pacto':
        // Validar datos del formulario
        if (empty($_POST['id_producto']) || empty($_POST['tipo_ubicacion']) || empty($_POST['id_destino']) || !isset($_POST['cantidad_pactada'])) {
            $session->setMessage('error', 'Todos los campos son obligatorios');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
            exit;
        }

        // Procesar la creación
        $idProducto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        $tipoUbicacion = filter_input(INPUT_POST, 'tipo_ubicacion', FILTER_SANITIZE_STRING);
        $idDestino = filter_input(INPUT_POST, 'id_destino', FILTER_VALIDATE_INT);
        $cantidadPactada = filter_input(INPUT_POST, 'cantidad_pactada', FILTER_VALIDATE_INT);

        $resultado = $pactosController->store($idProducto, $tipoUbicacion, $idDestino, $cantidadPactada);

        if (!$resultado['error']) {
            $session->setMessage('success', $resultado['mensaje']);
        } else {
            $session->setMessage('error', $resultado['mensaje']);
        }

        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
        break;

    case 'editar_pacto':
        // Validar datos del formulario
        if (empty($_POST['id']) || empty($_POST['id_producto']) || empty($_POST['tipo_ubicacion']) || empty($_POST['id_destino']) || !isset($_POST['cantidad_pactada'])) {
            $session->setMessage('error', 'Todos los campos son obligatorios');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
            exit;
        }

        // Procesar la actualización
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $idProducto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        $tipoUbicacion = filter_input(INPUT_POST, 'tipo_ubicacion', FILTER_SANITIZE_STRING);
        $idDestino = filter_input(INPUT_POST, 'id_destino', FILTER_VALIDATE_INT);
        $cantidadPactada = filter_input(INPUT_POST, 'cantidad_pactada', FILTER_VALIDATE_INT);

        $resultado = $pactosController->update($id, $idProducto, $tipoUbicacion, $idDestino, $cantidadPactada);

        if (!$resultado['error']) {
            $session->setMessage('success', $resultado['mensaje']);
        } else {
            $session->setMessage('error', $resultado['mensaje']);
        }

        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
        break;

    case 'eliminar_pacto':
        // Validar datos del formulario
        if (empty($_POST['id'])) {
            $session->setMessage('error', 'ID del pacto no proporcionado');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php');
            exit;
        }

        // Procesar la eliminación
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $resultado = $pactosController->destroy($id);

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
