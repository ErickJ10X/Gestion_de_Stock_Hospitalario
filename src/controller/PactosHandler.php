<?php

namespace controller;

use Exception;

require_once(__DIR__ . '/PactosController.php');
include_once(__DIR__ . '/../util/Session.php');
include_once(__DIR__ . '/../util/AuthGuard.php');

use util\Session;
use util\AuthGuard;

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar permisos
$authGuard = new AuthGuard();
$authGuard->requireHospitalGestor();

// Inicializar controlador y utilidades
$pactosController = new PactosController();
$session = new Session();

// Procesar la acción solicitada
try {
    $action = $_POST['action'] ?? null;
    
    if (!$action) {
        throw new Exception("No se especificó ninguna acción");
    }
    
    switch ($action) {
        case 'crear':
            procesarCrear();
            break;
            
        case 'editar':
            procesarEditar();
            break;
            
        case 'eliminar':
            procesarEliminar();
            break;
            
        default:
            throw new Exception("Acción no válida");
    }
} catch (Exception $e) {
    $session->setMessage('error', $e->getMessage());
    redirigir();
}

/**
 * Procesa la creación de un nuevo pacto
 */
function procesarCrear() {
    global $pactosController, $session;
    
    $idProducto = $_POST['id_producto'] ?? 0;
    $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
    $idDestino = $_POST['id_destino'] ?? 0;
    $cantidadPactada = $_POST['cantidad_pactada'] ?? 0;
    
    $resultado = $pactosController->store($idProducto, $tipoUbicacion, $idDestino, $cantidadPactada);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la edición de un pacto existente
 */
function procesarEditar() {
    global $pactosController, $session;
    
    $idPacto = $_POST['id'] ?? 0;
    $idProducto = $_POST['id_producto'] ?? 0;
    $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
    $idDestino = $_POST['id_destino'] ?? 0;
    $cantidadPactada = $_POST['cantidad_pactada'] ?? 0;
    
    $resultado = $pactosController->update($idPacto, $idProducto, $tipoUbicacion, $idDestino, $cantidadPactada);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la eliminación de un pacto
 */
function procesarEliminar() {
    global $pactosController, $session;
    
    $idPacto = $_POST['id'] ?? 0;
    
    $resultado = $pactosController->destroy($idPacto);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Redirige al usuario a la página de pactos
 */
function redirigir() {
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/pactos/');
    exit;
}
