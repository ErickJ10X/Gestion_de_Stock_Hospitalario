<?php
namespace controller;

require_once(__DIR__ . '/../model/service/UsuarioService.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

use Exception;
use model\service\UsuarioService;
use util\Session;
use util\AuthGuard;

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurar cabeceras para JSON
header('Content-Type: application/json; charset=utf-8');

// Crear el controlador
$usuarioApi = new UsuarioApi();

// Verificar la acción solicitada
$action = $_GET['action'] ?? '';

// Ejecutar la acción correspondiente
try {
    switch ($action) {
        case 'getById':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            echo json_encode($usuarioApi->getUserById($id));
            break;
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

class UsuarioApi
{
    private UsuarioService $userService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct()
    {
        $this->userService = new UsuarioService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
        
        // Verificar autenticación para todos los endpoints
        $this->authGuard->requireAdministrador();
    }

    /**
     * Obtiene un usuario por su ID
     * @param int $id ID del usuario
     * @return array Respuesta con los datos del usuario
     */
    public function getUserById(int $id): array
    {
        try {
            if ($id <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            $usuario = $this->userService->getUserById($id);
            
            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }
            
            // Convertir el usuario a un array para la respuesta JSON
            $userData = [
                'id_usuario' => $usuario->getIdUsuario(),
                'nombre' => $usuario->getNombre(),
                'email' => $usuario->getEmail(),
                'rol' => $usuario->getRol(),
                'activo' => $usuario->getActivo()
            ];
            
            return [
                'success' => true,
                'data' => $userData
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
