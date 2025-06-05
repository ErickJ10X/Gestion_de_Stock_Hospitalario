<?php

namespace controller;

use model\entity\Usuario;
use model\entity\UsuarioUbicacion;
use model\service\UsuarioService;
use model\service\UsuarioUbicacionService;
use model\enum\RolEnum;
use util\Session;
use util\AuthGuard;
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
     * Procesa solicitudes HTTP y ejecuta la acción correspondiente
     */
    public function processRequest(): void {
        $this->authGuard->requireAdministrador();
        
        // Determinar si es una solicitud API o normal
        $isApiRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isApiRequest) {
            $this->handleApiRequest();
            return;
        }

        // Solicitudes normales
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'crear':
                    $this->createUser();
                    break;
                case 'editar':
                    $this->updateUser();
                    break;
                case 'eliminar':
                    $this->deleteUser();
                    break;
                default:
                    $this->session->setMessage('error', 'Acción no válida');
                    $this->redirect('index.php');
                    break;
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
            $action = $_GET['action'];
            
            switch ($action) {
                case 'edit':
                    $this->showEditForm();
                    break;
                case 'delete':
                    $this->deleteUserConfirm();
                    break;
                default:
                    $this->redirect('index.php');
                    break;
            }
        }
    }

    /**
     * Prepara los datos necesarios para mostrar en las vistas
     */
    public function prepareDataForView(): array {
        $this->authGuard->requireAdministrador();
        
        $viewData = [
            'usuarios' => [],
            'roles' => RolEnum::getValues()
        ];

        try {
            $viewData['usuarios'] = $this->usuarioService->getAllUsuarios();
            
            // Cargar usuario a editar si existe en la URL
            if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
                $id = (int)$_GET['edit'];
                $usuario = $this->usuarioService->getUsuarioById($id);
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
     * Crea un nuevo usuario
     */
    private function createUser(): void {
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
    private function updateUser(): void {
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
            if (!empty($contrasena) && $contrasena !== $confirmarContrasena) {
                throw new Exception("Las contraseñas no coinciden");
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
    private function deleteUser(): void {
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
     * Obtiene un usuario por su ID incluyendo sus ubicaciones
     */
    public function getUserById(int $id): ?Usuario {
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
     * Maneja la confirmación de eliminación de usuario
     */
    private function deleteUserConfirm(): void {
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
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
     * Muestra el formulario de edición con los datos del usuario
     */
    private function showEditForm(): void {
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($id <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            $usuario = $this->getUserById($id);
            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }
            
            $this->redirect('index.php?tab=crear-editar&edit=' . $id);
            
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('index.php');
        }
    }

    /**
     * Maneja solicitudes de la API
     */
    private function handleApiRequest(): void {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $this->authGuard->requireAdministrador();
            
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $this->handleApiGet();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleApiPost();
            } else {
                echo json_encode(['success' => false, 'message' => 'Método HTTP no soportado']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        
        exit(); // Terminar la ejecución después de manejar la solicitud API
    }

    /**
     * Maneja solicitudes GET de la API
     */
    private function handleApiGet(): void {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'getById':
                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                $this->apiGetUserById($id);
                break;
                
            case 'getByUsuario':
                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                $this->apiGetUbicacionesByUsuario($id);
                break;
                
            case 'getAll':
                $this->apiGetAllUsers();
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
                break;
        }
    }

    /**
     * Maneja solicitudes POST de la API
     */
    private function handleApiPost(): void {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'asignar':
                $usuarioId = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
                $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
                $ubicacionId = isset($_POST['ubicacion_id']) ? (int)$_POST['ubicacion_id'] : 0;
                
                $this->apiAsignarUbicacion($usuarioId, $tipoUbicacion, $ubicacionId);
                break;
                
            case 'desasignar':
                $usuarioId = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
                $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
                $ubicacionId = isset($_POST['ubicacion_id']) ? (int)$_POST['ubicacion_id'] : 0;
                
                $this->apiDesasignarUbicacion($usuarioId, $tipoUbicacion, $ubicacionId);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
                break;
        }
    }

    /**
     * API: Obtiene un usuario por ID
     */
    private function apiGetUserById(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            $usuario = $this->getUserById($id);
            
            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }
            
            echo json_encode(['success' => true, 'data' => $usuario->toArray()]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Obtiene todos los usuarios
     */
    private function apiGetAllUsers(): void {
        try {
            $usuarios = $this->usuarioService->getAllUsuarios();
            $result = [];
            
            foreach ($usuarios as $usuario) {
                $result[] = $usuario->toArray();
            }
            
            echo json_encode(['success' => true, 'data' => $result]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Obtiene las ubicaciones de un usuario
     */
    private function apiGetUbicacionesByUsuario(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            $ubicaciones = $this->ubicacionService->getUbicacionesByUsuario($id);
            $result = [];
            
            foreach ($ubicaciones as $ubicacion) {
                $result[] = [
                    'id_usuario' => $ubicacion->getIdUsuario(),
                    'tipo_ubicacion' => $ubicacion->getTipoUbicacion(),
                    'id_ubicacion' => $ubicacion->getIdUbicacion()
                ];
            }
            
            echo json_encode(['success' => true, 'data' => $result]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Asigna una ubicación a un usuario
     */
    private function apiAsignarUbicacion(int $usuarioId, string $tipoUbicacion, int $ubicacionId): void {
        try {
            if ($usuarioId <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            if (empty($tipoUbicacion)) {
                throw new Exception("Tipo de ubicación no válido");
            }
            
            if ($ubicacionId <= 0) {
                throw new Exception("ID de ubicación no válido");
            }
            
            // Convertir tipos para compatibilidad con UsuarioUbicacion::TIPOS_VALIDOS
            $normalizedTipo = $this->normalizarTipoUbicacion($tipoUbicacion);
            
            // Asignar ubicación
            $resultado = $this->ubicacionService->asignarUbicacion($usuarioId, $normalizedTipo, $ubicacionId);
            
            echo json_encode([
                'success' => $resultado,
                'message' => $resultado ? 'Ubicación asignada correctamente' : 'Error al asignar la ubicación'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Elimina una ubicación de un usuario
     */
    private function apiDesasignarUbicacion(int $usuarioId, string $tipoUbicacion, int $ubicacionId): void {
        try {
            if ($usuarioId <= 0) {
                throw new Exception("ID de usuario no válido");
            }
            
            if (empty($tipoUbicacion)) {
                throw new Exception("Tipo de ubicación no válido");
            }
            
            if ($ubicacionId <= 0) {
                throw new Exception("ID de ubicación no válido");
            }
            
            // Convertir tipos para compatibilidad con UsuarioUbicacion::TIPOS_VALIDOS
            $normalizedTipo = $this->normalizarTipoUbicacion($tipoUbicacion);
            
            // Desasignar ubicación
            $resultado = $this->ubicacionService->eliminarUbicacion($usuarioId, $normalizedTipo, $ubicacionId);
            
            echo json_encode([
                'success' => $resultado,
                'message' => $resultado ? 'Ubicación eliminada correctamente' : 'Error al eliminar la ubicación'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
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
     * Redirige a una URL relativa a la sección de usuarios
     */
    private function redirect(string $path): void {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/' . $path);
        exit();
    }
}
