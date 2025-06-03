<?php

namespace controller;

use Exception;

require_once(__DIR__ . '/BotiquinController.php');
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
$botiquinController = new BotiquinController();
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
 * Procesa la creación de un nuevo botiquín
 */
function procesarCrear() {
    global $botiquinController, $session;
    
    $nombre = $_POST['nombre'] ?? '';
    $plantaId = $_POST['planta_id'] ?? 0;
    
    $resultado = $botiquinController->store($nombre, $plantaId);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la edición de un botiquín existente
 */
function procesarEditar() {
    global $botiquinController, $session;
    
    $id = $_POST['id'] ?? 0;
    $nombre = $_POST['nombre'] ?? '';
    $plantaId = $_POST['planta_id'] ?? 0;
    
    $resultado = $botiquinController->update($id, $nombre, $plantaId);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la eliminación de un botiquín
 */
function procesarEliminar() {
    global $botiquinController, $session;
    
    $id = $_POST['id'] ?? 0;
    
    $resultado = $botiquinController->destroy($id);
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Redirige al usuario a la página de botiquines
 */
function redirigir() {
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/');
    exit;
}
