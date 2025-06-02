<?php
session_start();
require_once(__DIR__ . '/../../controller/PactosController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\PactosController;
use util\Session;
use util\AuthGuard;

$pactosController = new PactosController();
$session = new Session();
$authGuard = new AuthGuard();

// Verificar permisos
$authGuard->requireHospitalGestor();

// Determinar la redirección base
$redirectBase = '/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php?tab=pactos';

// Procesar la acción solicitada
$action = $_POST['action'] ?? '';

try {
    // Debug para ver qué datos están llegando
    error_log("Acción recibida: " . $action);
    error_log("Datos POST: " . print_r($_POST, true));
    
    switch ($action) {
        case 'crear_pacto':
            // Validar y obtener datos del formulario
            $idProducto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
            $tipoUbicacion = filter_input(INPUT_POST, 'tipo_ubicacion', FILTER_SANITIZE_SPECIAL_CHARS);
            $idDestino = filter_input(INPUT_POST, 'id_destino', FILTER_VALIDATE_INT);
            $cantidadPactada = filter_input(INPUT_POST, 'cantidad_pactada', FILTER_VALIDATE_INT);
            
            // Debug para ver qué datos se están procesando
            error_log("ID Producto: {$idProducto}");
            error_log("Tipo Ubicación: {$tipoUbicacion}");
            error_log("ID Destino: {$idDestino}");
            error_log("Cantidad Pactada: {$cantidadPactada}");
            
            // Validar datos
            if (!$idProducto || !$tipoUbicacion || !$idDestino || !$cantidadPactada) {
                $session->setMessage('modal_error_pacto', 'Todos los campos son obligatorios y deben ser válidos.');
                header("Location: $redirectBase");
                exit;
            }
            
            // Validar que tipo_ubicacion sea 'Planta' o 'Botiquin'
            if ($tipoUbicacion !== 'Planta' && $tipoUbicacion !== 'Botiquin') {
                $session->setMessage('modal_error_pacto', 'El tipo de ubicación debe ser Planta o Botiquin.');
                header("Location: $redirectBase");
                exit;
            }
            
            // Crear el pacto
            $resultado = $pactosController->store($idProducto, $tipoUbicacion, $idDestino, $cantidadPactada);
            
            if (!$resultado['error']) {
                $session->setMessage('success', '¡Pacto creado exitosamente!');
            } else {
                $session->setMessage('error', 'Error al crear el pacto: ' . $resultado['mensaje']);
            }
            break;
            
        case 'editar_pacto':
            // Validar y obtener datos del formulario
            $idPacto = filter_input(INPUT_POST, 'id_pacto', FILTER_VALIDATE_INT);
            $idProducto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
            $tipoUbicacion = filter_input(INPUT_POST, 'tipo_ubicacion', FILTER_SANITIZE_SPECIAL_CHARS);
            $idDestino = filter_input(INPUT_POST, 'id_destino', FILTER_VALIDATE_INT);
            $cantidadPactada = filter_input(INPUT_POST, 'cantidad_pactada', FILTER_VALIDATE_INT);
            
            // Debug para ver qué datos se están procesando
            error_log("ID Pacto: {$idPacto}");
            error_log("ID Producto: {$idProducto}");
            error_log("Tipo Ubicación: {$tipoUbicacion}");
            error_log("ID Destino: {$idDestino}");
            error_log("Cantidad Pactada: {$cantidadPactada}");
            
            // Validar datos
            if (!$idPacto || !$idProducto || !$tipoUbicacion || !$idDestino || !$cantidadPactada) {
                $session->setMessage('modal_error_pacto_' . $idPacto, 'Todos los campos son obligatorios y deben ser válidos.');
                header("Location: $redirectBase");
                exit;
            }
            
            // Validar que tipo_ubicacion sea 'Planta' o 'Botiquin'
            if ($tipoUbicacion !== 'Planta' && $tipoUbicacion !== 'Botiquin') {
                $session->setMessage('modal_error_pacto_' . $idPacto, 'El tipo de ubicación debe ser Planta o Botiquin.');
                header("Location: $redirectBase");
                exit;
            }
            
            // Actualizar el pacto
            $resultado = $pactosController->update($idPacto, $idProducto, $tipoUbicacion, $idDestino, $cantidadPactada);
            
            if (!$resultado['error']) {
                $session->setMessage('success', '¡Pacto actualizado exitosamente!');
            } else {
                $session->setMessage('error', 'Error al actualizar el pacto: ' . $resultado['mensaje']);
            }
            break;
            
        case 'eliminar_pacto':
            // Validar y obtener datos del formulario
            $idPacto = filter_input(INPUT_POST, 'id_pacto', FILTER_VALIDATE_INT);
            
            // Debug para ver qué datos se están procesando
            error_log("ID Pacto a eliminar: {$idPacto}");
            
            // Validar datos
            if (!$idPacto) {
                $session->setMessage('error', 'ID de pacto inválido.');
                header("Location: $redirectBase");
                exit;
            }
            
            // Eliminar el pacto
            $resultado = $pactosController->destroy($idPacto);
            
            if (!$resultado['error']) {
                $session->setMessage('success', '¡Pacto eliminado exitosamente!');
            } else {
                $session->setMessage('error', 'Error al eliminar el pacto: ' . $resultado['mensaje']);
            }
            break;
            
        default:
            $session->setMessage('error', 'Acción no válida');
            break;
    }
} catch (Exception $e) {
    error_log("Excepción: " . $e->getMessage());
    $session->setMessage('error', 'Error: ' . $e->getMessage());
}

// Redireccionar a la lista de productos con la pestaña de pactos activa
header("Location: $redirectBase");
exit;
