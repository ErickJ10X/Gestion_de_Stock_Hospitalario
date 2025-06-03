<?php

namespace controller;

use Exception;

require_once(__DIR__ . '/PlantaController.php');
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
$plantaController = new PlantaController();
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
 * Procesa la creación de una nueva planta
 */
function procesarCrear() {
    global $plantaController, $session;
    
    $nombre = $_POST['nombre'] ?? '';
    $idHospital = $_POST['id_hospital'] ?? 0;
    
    // Verificar que PlantaController tenga el método create
    if (method_exists($plantaController, 'create')) {
        $resultado = $plantaController->create($nombre, $idHospital);
    } else {
        // Alternativa si no existe el método create
        try {
            if (empty(trim($nombre))) {
                throw new Exception("El nombre de la planta es obligatorio");
            }
            
            if (!is_numeric($idHospital) || $idHospital <= 0) {
                throw new Exception("ID de hospital inválido");
            }
            
            // Implementación mínima para crear planta
            require_once(__DIR__ . '/../model/service/PlantaService.php');
            $plantaService = new \model\service\PlantaService();
            $resultado = $plantaService->createPlanta($nombre, $idHospital);
            
            if ($resultado) {
                $session->setMessage('success', "Planta creada correctamente");
            } else {
                $session->setMessage('error', "No se pudo crear la planta");
            }
        } catch (Exception $e) {
            $session->setMessage('error', $e->getMessage());
        }
        
        redirigir();
        return;
    }
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la edición de una planta existente
 */
function procesarEditar() {
    global $plantaController, $session;
    
    $id = $_POST['id'] ?? 0;
    $nombre = $_POST['nombre'] ?? '';
    $idHospital = $_POST['id_hospital'] ?? 0;
    
    // Verificar que PlantaController tenga el método update
    if (method_exists($plantaController, 'update')) {
        $resultado = $plantaController->update($id, $nombre, $idHospital);
    } else {
        // Alternativa si no existe el método update
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception("ID de planta inválido");
            }
            
            if (empty(trim($nombre))) {
                throw new Exception("El nombre de la planta es obligatorio");
            }
            
            if (!is_numeric($idHospital) || $idHospital <= 0) {
                throw new Exception("ID de hospital inválido");
            }
            
            // Implementación mínima para actualizar planta
            require_once(__DIR__ . '/../model/service/PlantaService.php');
            $plantaService = new \model\service\PlantaService();
            $resultado = $plantaService->updatePlanta($id, $nombre, $idHospital);
            
            if ($resultado) {
                $session->setMessage('success', "Planta actualizada correctamente");
            } else {
                $session->setMessage('error', "No se pudo actualizar la planta");
            }
        } catch (Exception $e) {
            $session->setMessage('error', $e->getMessage());
        }
        
        redirigir();
        return;
    }
    
    if ($resultado['error']) {
        $session->setMessage('error', $resultado['mensaje']);
    } else {
        $session->setMessage('success', $resultado['mensaje']);
    }
    
    redirigir();
}

/**
 * Procesa la eliminación de una planta
 */
function procesarEliminar() {
    global $plantaController, $session;
    
    $id = $_POST['id'] ?? 0;
    
    // Verificar que PlantaController tenga el método delete
    if (method_exists($plantaController, 'delete')) {
        $resultado = $plantaController->delete($id);
    } else {
        // Alternativa si no existe el método delete
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception("ID de planta inválido");
            }
            
            // Implementación mínima para eliminar planta
            require_once(__DIR__ . '/../model/service/PlantaService.php');
            $plantaService = new \model\service\PlantaService();
            $resultado = $plantaService->deletePlanta($id);
            
            if ($resultado) {
                $session->setMessage('success', "Planta eliminada correctamente");
            } else {
                $session->setMessage('error', "No se pudo eliminar la planta");
            }
        } catch (Exception $e) {
            $session->setMessage('error', $e->getMessage());
        }
        
        redirigir();
        return;
    }
    
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
