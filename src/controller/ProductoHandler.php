<?php

namespace controller;

use Exception;

require_once(__DIR__ . '/ProductoController.php');
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
$productoController = new ProductoController();
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
 * Procesa la creación de un nuevo producto
 */
function procesarCrear() {
    global $productoController, $session;
    
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $unidad_medida = $_POST['unidad_medida'] ?? '';
    
    $resultado = $productoController->store($codigo, $nombre, $descripcion, $unidad_medida);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la edición de un producto existente
 */
function procesarEditar() {
    global $productoController, $session;
    
    $id = $_POST['id'] ?? 0;
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $unidad_medida = $_POST['unidad_medida'] ?? '';
    
    $resultado = $productoController->update($id, $codigo, $nombre, $descripcion, $unidad_medida);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la eliminación de un producto
 */
function procesarEliminar() {
    global $productoController, $session;
    
    $id = $_POST['id'] ?? 0;
    
    $resultado = $productoController->destroy($id);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Redirige al usuario a la página de productos
 */
function redirigir() {
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/');
    exit;
}
