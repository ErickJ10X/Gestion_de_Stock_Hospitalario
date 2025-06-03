<?php
namespace controller;

require_once(__DIR__ . '/Usuario_UbicacionesController.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

use model\entity\Usuario_Ubicacion;
use util\Session;
use util\AuthGuard;
use Exception;

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurar cabeceras para JSON
header('Content-Type: application/json; charset=utf-8');

// Crear el controlador
$ubicacionesApi = new Usuario_UbicacionesApi();

// Verificar permisos
$authGuard = new AuthGuard();
$authGuard->requireAdministrador();

try {
    // Procesar peticiones GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'getByUsuario':
                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                echo json_encode($ubicacionesApi->getUbicacionesByUsuario($id));
                break;
                
            case 'getAll':
                echo json_encode($ubicacionesApi->getAllUbicaciones());
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción no válida'
                ]);
                break;
        }
    }
    // Procesar peticiones POST
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'asignar':
                $usuarioId = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
                $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
                $ubicacionId = isset($_POST['ubicacion_id']) ? (int)$_POST['ubicacion_id'] : 0;
                
                echo json_encode($ubicacionesApi->asignarUbicacion($usuarioId, $tipoUbicacion, $ubicacionId));
                break;
                
            case 'desasignar':
                $usuarioId = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
                $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
                $ubicacionId = isset($_POST['ubicacion_id']) ? (int)$_POST['ubicacion_id'] : 0;
                
                echo json_encode($ubicacionesApi->desasignarUbicacion($usuarioId, $tipoUbicacion, $ubicacionId));
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción no válida'
                ]);
                break;
        }
    }
    else {
        echo json_encode([
            'success' => false,
            'message' => 'Método de solicitud no permitido'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

class Usuario_UbicacionesApi
{
    private Usuario_UbicacionesController $controller;
    private Session $session;

    public function __construct()
    {
        $this->controller = new Usuario_UbicacionesController();
        $this->session = new Session();
    }

    /**
     * Obtiene todas las ubicaciones asignadas
     * @return array Respuesta con los datos
     */
    public function getAllUbicaciones(): array
    {
        return $this->controller->getAllUsuarioUbicaciones();
    }

    /**
     * Obtiene las ubicaciones asignadas a un usuario
     * @param int $usuarioId ID del usuario
     * @return array Respuesta con los datos
     */
    public function getUbicacionesByUsuario(int $usuarioId): array
    {
        try {
            if ($usuarioId <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            return $this->controller->getUbicacionesByUsuario($usuarioId);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Asigna una ubicación a un usuario
     * @param int $usuarioId ID del usuario
     * @param string $tipoUbicacion Tipo de ubicación (almacen/botiquin)
     * @param int $ubicacionId ID de la ubicación
     * @return array Respuesta con resultado
     */
    public function asignarUbicacion(int $usuarioId, string $tipoUbicacion, int $ubicacionId): array
    {
        try {
            // Validar parámetros
            if ($usuarioId <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            if (empty($tipoUbicacion) || !in_array($tipoUbicacion, ['almacen', 'botiquin'])) {
                throw new Exception("Tipo de ubicación no válido");
            }
            
            if ($ubicacionId <= 0) {
                throw new Exception("ID de ubicación no válido");
            }
            
            return $this->controller->createUsuarioUbicacion($usuarioId, $tipoUbicacion, $ubicacionId);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Desasigna una ubicación a un usuario
     * @param int $usuarioId ID del usuario
     * @param string $tipoUbicacion Tipo de ubicación (almacen/botiquin)
     * @param int $ubicacionId ID de la ubicación
     * @return array Respuesta con resultado
     */
    public function desasignarUbicacion(int $usuarioId, string $tipoUbicacion, int $ubicacionId): array
    {
        try {
            // Validar parámetros
            if ($usuarioId <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            if (empty($tipoUbicacion) || !in_array($tipoUbicacion, ['almacen', 'botiquin'])) {
                throw new Exception("Tipo de ubicación no válido");
            }
            
            if ($ubicacionId <= 0) {
                throw new Exception("ID de ubicación no válido");
            }
            
            return $this->controller->deleteUsuarioUbicacion($usuarioId, $tipoUbicacion, $ubicacionId);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
?>
