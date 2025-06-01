<?php
session_start();
require_once(__DIR__ . '/../../controller/Usuario_UbicacionesController.php');
require_once(__DIR__ . '/../../util/Session.php');

use controller\Usuario_UbicacionesController;
use util\Session;

$controller = new Usuario_UbicacionesController();
$session = new Session();

// Verificar que se haya enviado una acción
if (!isset($_POST['action'])) {
    $session->setMessage('error', 'Acción no especificada');
    header('Location: lista-usuarios.php');
    exit;
}

$action = $_POST['action'];

switch ($action) {
    case 'asignar':
        handleAsignar();
        break;
    case 'editar':
        handleEditar();
        break;
    case 'eliminar':
        handleEliminar();
        break;
    default:
        $session->setMessage('error', 'Acción desconocida');
        header('Location: lista-usuarios.php');
        exit;
}

// Función para manejar la asignación de ubicaciones a usuarios
function handleAsignar() {
    global $controller, $session;
    
    // Validar datos recibidos
    if (!isset($_POST['usuario_id']) || !isset($_POST['tipo_ubicacion']) || !isset($_POST['ubicacion_id'])) {
        $session->setMessage('error', 'Faltan datos requeridos');
        header('Location: lista-usuarios.php');
        exit;
    }
    
    $idUsuario = (int) $_POST['usuario_id'];
    $tipoUbicacion = $_POST['tipo_ubicacion'];
    $idUbicacion = (int) $_POST['ubicacion_id'];
    
    // Crear asignación
    $result = $controller->createUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
    
    if ($result['success']) {
        $session->setMessage('success', $result['message']);
    } else {
        $session->setMessage('error', $result['message']);
    }
    
    header('Location: lista-usuarios.php');
    exit;
}

// Función para manejar la edición de asignaciones
function handleEditar() {
    global $controller, $session;
    
    // Validar datos recibidos
    if (!isset($_POST['usuario_id']) || 
        !isset($_POST['tipo_ubicacion']) || 
        !isset($_POST['ubicacion_id']) ||
        !isset($_POST['tipo_ubicacion_original']) ||
        !isset($_POST['ubicacion_id_original'])) {
        $session->setMessage('error', 'Faltan datos requeridos');
        header('Location: lista-usuarios.php');
        exit;
    }
    
    $idUsuario = (int) $_POST['usuario_id'];
    $tipoUbicacionOriginal = $_POST['tipo_ubicacion_original'];
    $idUbicacionOriginal = (int) $_POST['ubicacion_id_original'];
    
    // Primero eliminamos la asignación original
    $controller->deleteUsuarioUbicacion($idUsuario, $tipoUbicacionOriginal, $idUbicacionOriginal);
    
    // Luego creamos la nueva asignación
    $tipoUbicacion = $_POST['tipo_ubicacion'];
    $idUbicacion = (int) $_POST['ubicacion_id'];
    
    $result = $controller->createUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
    
    if ($result['success']) {
        $session->setMessage('success', 'Asignación de ubicación actualizada con éxito');
    } else {
        $session->setMessage('error', $result['message']);
    }
    
    header('Location: lista-usuarios.php');
    exit;
}

// Función para manejar la eliminación de asignaciones
function handleEliminar() {
    global $controller, $session;
    
    // Validar datos recibidos
    if (!isset($_POST['usuario_id']) || !isset($_POST['tipo_ubicacion']) || !isset($_POST['ubicacion_id'])) {
        $session->setMessage('error', 'Faltan datos requeridos');
        header('Location: lista-usuarios.php');
        exit;
    }
    
    $idUsuario = (int) $_POST['usuario_id'];
    $tipoUbicacion = $_POST['tipo_ubicacion'];
    $idUbicacion = (int) $_POST['ubicacion_id'];
    
    // Eliminar asignación
    $result = $controller->deleteUsuarioUbicacion($idUsuario, $tipoUbicacion, $idUbicacion);
    
    if ($result['success']) {
        $session->setMessage('success', $result['message']);
    } else {
        $session->setMessage('error', $result['message']);
    }
    
    header('Location: lista-usuarios.php');
    exit;
}
