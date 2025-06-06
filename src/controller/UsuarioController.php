<?php

namespace controller;

use model\entity\Usuario;
use model\entity\UsuarioUbicacion;
use model\service\UsuarioService;
use model\service\UsuarioUbicacionService;
use model\enum\RolEnum;
use util\Session;
use util\AuthGuard;
use util\Redirect;
use Exception;

require_once(__DIR__ . '/../model/service/UsuarioService.php');
require_once(__DIR__ . '/../model/service/UsuarioUbicacionService.php');
require_once(__DIR__ . '/../model/entity/Usuario.php');
require_once(__DIR__ . '/../model/entity/UsuarioUbicacion.php');
require_once(__DIR__ . '/../model/enum/RolEnum.php');
include_once(__DIR__ . '/../util/Session.php');
include_once(__DIR__ . '/../util/AuthGuard.php');

class UsuarioController {
    private UsuarioService $usuarioService;
    private UsuarioUbicacionService $ubicacionService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct() {
        $this->usuarioService = new UsuarioService();
        $this->ubicacionService = new UsuarioUbicacionService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Méto do principal para obtener los datos utilizados en la vista index
     */
    public function index(): array {
        $this->authGuard->requireAdministrador();
        
        $viewData = [
            'usuarios' => [],
            'roles' => RolEnum::getValues()
        ];

        try {
            $viewData['usuarios'] = $this->usuarioService->getAllUsuarios();
            
            // Si hay un usuario para editar en la URL
            if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
                $id = (int)$_GET['edit'];
                $usuario = $this->getUserWithUbicaciones($id);
                if ($usuario) {
                    $viewData['usuario_editar'] = $usuario;
                }
            }
            
            return $viewData;
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al cargar datos: " . $e->getMessage());
            return $viewData;
        }
    }

    /**
     * Obtiene un usuario por su ID incluyendo sus ubicaciones
     */
    public function getUserWithUbicaciones(int $id): ?Usuario {
        try {
            $usuario = $this->usuarioService->getUsuarioById($id);
            if ($usuario) {
                $ubicaciones = $this->ubicacionService->getUbicacionesByUsuario($id);
                $usuario->setUbicaciones($ubicaciones);
            }
            return $usuario;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Obtiene un usuario por su ID
     */
    public function getById(int $id): array {
        try {
            $usuario = $this->getUserWithUbicaciones($id);
            
            if ($usuario) {
                $userData = $usuario->toArray();
                $ubicaciones = [];
                
                foreach ($usuario->getUbicaciones() as $ubicacion) {
                    $ubicaciones[] = $ubicacion->toArray();
                }
                
                $userData['ubicaciones'] = $ubicaciones;
                
                return [
                    'error' => false,
                    'usuario' => $userData
                ];
            } else {
                return [
                    'error' => true,
                    'mensaje' => 'Usuario no encontrado'
                ];
            }
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las ubicaciones de un usuario
     */
    public function getUbicaciones(int $idUsuario): array {
        try {
            $ubicaciones = $this->ubicacionService->getUbicacionesByUsuario($idUsuario);
            $result = [];
            
            foreach ($ubicaciones as $ubicacion) {
                $result[] = [
                    'id_usuario' => $ubicacion->getIdUsuario(),
                    'tipo_ubicacion' => $ubicacion->getTipoUbicacion(),
                    'id_ubicacion' => $ubicacion->getIdUbicacion()
                ];
            }
            
            return [
                'error' => false,
                'ubicaciones' => $result
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => $e->getMessage(),
                'ubicaciones' => []
            ];
        }
    }

    /**
     * Crea un nuevo usuario
     */
    public function crear(): void {
        $this->authGuard->requireAdministrador();
        
        try {
            // Validar datos
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';
            $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';
            $rol = $_POST['rol'] ?? '';
            
            if (empty($nombre) || empty($email) || empty($contrasena) || empty($confirmarContrasena) || empty($rol)) {
                throw new Exception("Todos los campos son obligatorios");
            }
            
            if ($contrasena !== $confirmarContrasena) {
                throw new Exception("Las contraseñas no coinciden");
            }
            
            // Crear usuario
            $userData = [
                'nombre' => $nombre,
                'email' => $email,
                'contrasena' => $contrasena,
                'rol' => $rol,
                'activo' => true
            ];
            
            $usuario = $this->usuarioService->createUsuario($userData);
            $this->session->setMessage('success', "Usuario creado correctamente");
            $this->redirect('index.php');
            
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('index.php?tab=crear-editar');
        }
    }

    /**
     * Actualiza un usuario existente
     */
    public function editar(): void {
        $this->authGuard->requireAdministrador();
        
        try {
            // Validar datos
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';
            $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';
            $rol = $_POST['rol'] ?? '';
            $activo = isset($_POST['activo']) ? (bool)$_POST['activo'] : true;
            
            if ($id <= 0 || empty($nombre) || empty($email) || empty($rol)) {
                throw new Exception("ID, nombre, email y rol son obligatorios");
            }
            
            // Si hay contraseña nueva, validar
            if (!empty($contrasena)) {
                if ($contrasena !== $confirmarContrasena) {
                    throw new Exception("Las contraseñas no coinciden");
                }
            }
            
            // Preparar datos para actualizar
            $userData = [
                'nombre' => $nombre,
                'email' => $email,
                'rol' => $rol,
                'activo' => $activo
            ];
            
            // Agregar contraseña si se proporcionó
            if (!empty($contrasena)) {
                $userData['contrasena'] = $contrasena;
            }
            
            $usuario = $this->usuarioService->updateUsuario($id, $userData);
            $this->session->setMessage('success', "Usuario actualizado correctamente");
            $this->redirect('index.php');
            
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('index.php?tab=crear-editar');
        }
    }

    /**
     * Elimina un usuario
     */
    public function eliminar(): void {
        $this->authGuard->requireAdministrador();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            if ($id <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            if ($id == $this->session->getUserData('id')) {
                throw new Exception("No puedes eliminar tu propio usuario");
            }
            
            // Eliminar ubicaciones y luego el usuario
            $this->ubicacionService->eliminarUbicacionesPorUsuario($id);
            $this->usuarioService->deleteUsuario($id);
            
            $this->session->setMessage('success', "Usuario eliminado correctamente");
            $this->redirect('index.php');
            
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('index.php');
        }
    }

    /**
     * Asigna una ubicación a un usuario
     */
    public function asignarUbicacion(): void {
        $this->authGuard->requireAdministrador();
        
        try {
            $usuarioId = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
            $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
            $ubicacionId = isset($_POST['ubicacion_id']) ? (int)$_POST['ubicacion_id'] : 0;
            
            if ($usuarioId <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            if (empty($tipoUbicacion)) {
                throw new Exception("Tipo de ubicación no válido");
            }
            
            if ($ubicacionId <= 0) {
                throw new Exception("ID de ubicación no válido");
            }
            
            // Normalizar el tipo de ubicación
            $tipoNormalizado = $this->normalizarTipoUbicacion($tipoUbicacion);
            
            // Asignar ubicación
            $this->ubicacionService->asignarUbicacion($usuarioId, $tipoNormalizado, $ubicacionId);
            
            if ($this->isApiRequest()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Ubicación asignada correctamente'
                ]);
                exit;
            } else {
                $this->session->setMessage('success', "Ubicación asignada correctamente");
                $this->redirect('index.php?tab=asignar-ubicaciones');
            }
            
        } catch (Exception $e) {
            if ($this->isApiRequest()) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            } else {
                $this->session->setMessage('error', $e->getMessage());
                $this->redirect('index.php?tab=asignar-ubicaciones');
            }
        }
    }

    /**
     * Elimina una ubicación de un usuario
     */
    public function eliminarUbicacion(): void {
        $this->authGuard->requireAdministrador();
        
        try {
            $usuarioId = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
            $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
            $ubicacionId = isset($_POST['ubicacion_id']) ? (int)$_POST['ubicacion_id'] : 0;
            
            if ($usuarioId <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            if (empty($tipoUbicacion)) {
                throw new Exception("Tipo de ubicación no válido");
            }
            
            if ($ubicacionId <= 0) {
                throw new Exception("ID de ubicación no válido");
            }
            
            // Normalizar el tipo de ubicación
            $tipoNormalizado = $this->normalizarTipoUbicacion($tipoUbicacion);
            
            // Eliminar ubicación
            $this->ubicacionService->eliminarUbicacion($usuarioId, $tipoNormalizado, $ubicacionId);
            
            if ($this->isApiRequest()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Ubicación eliminada correctamente'
                ]);
                exit;
            } else {
                $this->session->setMessage('success', "Ubicación eliminada correctamente");
                $this->redirect('index.php?tab=asignar-ubicaciones');
            }
            
        } catch (Exception $e) {
            if ($this->isApiRequest()) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            } else {
                $this->session->setMessage('error', $e->getMessage());
                $this->redirect('index.php?tab=asignar-ubicaciones');
            }
        }
    }
    
    /**
     * Normaliza los tipos de ubicación para que coincidan con UsuarioUbicacion::TIPOS_VALIDOS
     */
    private function normalizarTipoUbicacion(string $tipo): string {
        switch (strtolower($tipo)) {
            case 'hospital':
                return 'Hospital';
            case 'planta':
                return 'Planta';
            case 'botiquin':
            case 'botiquín':
                return 'Botiquin';
            case 'almacen':
            case 'almacén':
                return 'Hospital'; // Los almacenes se consideran tipo Hospital
            default:
                throw new Exception("Tipo de ubicación no reconocido: $tipo");
        }
    }

    /**
     * Procesa una solicitud desde la vista API
     */
    public function processApiRequest(): void {
        header('Content-Type: application/json');
        
        try {
            $this->authGuard->requireAdministrador();
            
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $action = $_GET['action'] ?? '';
                
                switch ($action) {
                    case 'getById':
                        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                        echo json_encode($this->getById($id));
                        break;
                    case 'getUbicaciones':
                        $idUsuario = isset($_GET['idUsuario']) ? (int)$_GET['idUsuario'] : 0;
                        echo json_encode($this->getUbicaciones($idUsuario));
                        break;
                    case 'getAll':
                        echo json_encode([
                            'error' => false,
                            'usuarios' => $this->usuarioService->getAllUsuarios()
                        ]);
                        break;
                    default:
                        echo json_encode([
                            'error' => true,
                            'mensaje' => 'Acción no reconocida'
                        ]);
                        break;
                }
            } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';
                
                switch ($action) {
                    case 'crear':
                        $this->crear();
                        break;
                    case 'editar':
                        $this->editar();
                        break;
                    case 'eliminar':
                        $this->eliminar();
                        break;
                    case 'asignarUbicacion':
                        $this->asignarUbicacion();
                        break;
                    case 'eliminarUbicacion':
                        $this->eliminarUbicacion();
                        break;
                    default:
                        echo json_encode([
                            'error' => true,
                            'mensaje' => 'Acción no reconocida'
                        ]);
                        break;
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    /**
     * Procesa la solicitud actual
     */
    public function processRequest(): void {
        // Determinar si es una solicitud API
        if ($this->isApiRequest()) {
            $this->processApiRequest();
            return;
        }

        // Procesar solicitudes normales
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'crear':
                    $this->crear();
                    break;
                case 'editar':
                    $this->editar();
                    break;
                case 'eliminar':
                    $this->eliminar();
                    break;
                case 'asignarUbicacion':
                    $this->asignarUbicacion();
                    break;
                case 'eliminarUbicacion':
                    $this->eliminarUbicacion();
                    break;
                default:
                    $this->session->setMessage('error', 'Acción no válida');
                    $this->redirect('index.php');
                    break;
            }
        }
    }
    
    /**
     * Verifica si la solicitud actual es una solicitud AJAX
     */
    private function isApiRequest(): bool {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Redirige a una URL relativa a la sección de usuarios
     */
    private function redirect(string $path): void {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/' . $path);
        exit();
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new UsuarioController();
    $controller->processRequest();
}
