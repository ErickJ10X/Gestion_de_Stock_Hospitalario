<?php

namespace controller;

use Exception;

require_once(__DIR__ . '/HospitalController.php');
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
$hospitalController = new HospitalController();
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
 * Procesa la creación de un nuevo hospital
 */
function procesarCrear() {
    global $hospitalController, $session;
    
    $nombre = $_POST['nombre'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    
    $resultado = $hospitalController->store($nombre, $ubicacion);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la edición de un hospital existente
 */
function procesarEditar() {
    global $hospitalController, $session;
    
    $id = $_POST['id'] ?? 0;
    $nombre = $_POST['nombre'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    
    $resultado = $hospitalController->update($id, $nombre, $ubicacion);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la eliminación de un hospital
 */
function procesarEliminar() {
    global $hospitalController, $session;
    
    $id = $_POST['id'] ?? 0;
    
    $resultado = $hospitalController->destroy($id);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Redirige al usuario a la página de hospitales
 */
function redirigir() {
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/');
    exit;
}
