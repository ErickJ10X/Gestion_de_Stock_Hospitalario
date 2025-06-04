<?php

namespace controller;

use Exception;
use model\entity\Pacto;
use model\service\PactoService;
use model\repository\PactoRepository;
use util\Session;
use util\AuthGuard;

require_once(__DIR__ . '/../model/service/PactoService.php');
require_once(__DIR__ . '/../model/repository/PactoRepository.php');
require_once(__DIR__ . '/../model/entity/Pacto.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

class PactosController
{
    private PactoService $pactoService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct()
    {
        $this->pactoService = new PactoService(new PactoRepository());
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Método principal para obtener los pactos
     * @return array Datos para la vista de pactos
     */
    public function index(): array
    {
        $this->authGuard->requireGestorHospital();
        
        $viewData = [
            'pactos' => []
        ];

        try {
            $viewData['pactos'] = $this->pactoService->getAllPactos();
            return $viewData;
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al cargar los pactos: ' . $e->getMessage());
            return $viewData;
        }
    }

    /**
     * Obtiene un pacto por su ID
     * @param int $id ID del pacto
     * @return Pacto|null El pacto encontrado o null
     */
    public function getById(int $id): ?Pacto
    {
        try {
            return $this->pactoService->getPactoById($id);
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener el pacto: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene pactos por producto
     * @param int $idProducto ID del producto
     * @return array Lista de pactos asociados al producto
     */
    public function getByProducto(int $idProducto): array
    {
        try {
            return $this->pactoService->getPactosByProducto($idProducto);
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener los pactos del producto: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene pactos por destino
     * @param string $tipoUbicacion Tipo de ubicación (Planta o Botiquin)
     * @param int $idDestino ID del destino
     * @return array Lista de pactos asociados al destino
     */
    public function getByDestino(string $tipoUbicacion, int $idDestino): array
    {
        try {
            return $this->pactoService->getPactosByDestino($tipoUbicacion, $idDestino);
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener los pactos del destino: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica si es necesaria una reposición
     * @param int $idProducto ID del producto
     * @param string $tipoUbicacion Tipo de ubicación
     * @param int $idDestino ID del destino
     * @param int $cantidadDisponible Cantidad disponible actualmente
     * @return bool True si se necesita reposición, false en caso contrario
     */
    public function verificarNecesidadReposicion(int $idProducto, string $tipoUbicacion, int $idDestino, int $cantidadDisponible): bool
    {
        try {
            return $this->pactoService->verificarNecesidadReposicion($idProducto, $tipoUbicacion, $idDestino, $cantidadDisponible);
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al verificar necesidad de reposición: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la cantidad pactada para un producto en un destino específico
     * @param int $idProducto ID del producto
     * @param string $tipoUbicacion Tipo de ubicación
     * @param int $idDestino ID del destino
     * @return int Cantidad pactada
     */
    public function getCantidadPactada(int $idProducto, string $tipoUbicacion, int $idDestino): int
    {
        try {
            return $this->pactoService->getCantidadPactada($idProducto, $tipoUbicacion, $idDestino);
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener cantidad pactada: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Crea un nuevo pacto
     */
    public function crear(): void
    {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $idProducto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
            $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
            $idDestino = isset($_POST['id_destino']) ? (int)$_POST['id_destino'] : 0;
            $cantidadPactada = isset($_POST['cantidad_pactada']) ? (int)$_POST['cantidad_pactada'] : 0;
            
            // Validaciones
            if ($idProducto <= 0) {
                throw new Exception('El producto es obligatorio');
            }
            
            if (empty($tipoUbicacion)) {
                throw new Exception('El tipo de ubicación es obligatorio');
            }
            
            if ($idDestino <= 0) {
                throw new Exception('El destino es obligatorio');
            }
            
            if ($cantidadPactada <= 0) {
                throw new Exception('La cantidad pactada debe ser mayor que cero');
            }
            
            // Crear el pacto
            $data = [
                'id_producto' => $idProducto,
                'tipo_ubicacion' => $tipoUbicacion,
                'id_destino' => $idDestino,
                'cantidad_pactada' => $cantidadPactada,
                'activo' => true
            ];
            
            $this->pactoService->createPacto($data);
            
            $this->session->setMessage('success', 'Pacto creado correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al crear pacto: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Actualiza un pacto existente
     */
    public function editar(): void
    {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $idProducto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
            $tipoUbicacion = $_POST['tipo_ubicacion'] ?? '';
            $idDestino = isset($_POST['id_destino']) ? (int)$_POST['id_destino'] : 0;
            $cantidadPactada = isset($_POST['cantidad_pactada']) ? (int)$_POST['cantidad_pactada'] : 0;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de pacto inválido');
            }
            
            if ($idProducto <= 0) {
                throw new Exception('El producto es obligatorio');
            }
            
            if (empty($tipoUbicacion)) {
                throw new Exception('El tipo de ubicación es obligatorio');
            }
            
            if ($idDestino <= 0) {
                throw new Exception('El destino es obligatorio');
            }
            
            if ($cantidadPactada <= 0) {
                throw new Exception('La cantidad pactada debe ser mayor que cero');
            }
            
            // Actualizar el pacto
            $data = [
                'id_producto' => $idProducto,
                'tipo_ubicacion' => $tipoUbicacion,
                'id_destino' => $idDestino,
                'cantidad_pactada' => $cantidadPactada
            ];
            
            $this->pactoService->updatePacto($id, $data);
            
            $this->session->setMessage('success', 'Pacto actualizado correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al actualizar pacto: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Elimina un pacto
     */
    public function eliminar(): void
    {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de pacto inválido');
            }
            
            // Eliminar el pacto
            $eliminado = $this->pactoService->deletePacto($id);
            
            if ($eliminado) {
                $this->session->setMessage('success', 'Pacto eliminado correctamente');
            } else {
                throw new Exception('No se pudo eliminar el pacto');
            }
            
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al eliminar pacto: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Desactiva un pacto
     */
    public function desactivar(): void
    {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de pacto inválido');
            }
            
            // Desactivar el pacto
            $desactivado = $this->pactoService->desactivarPacto($id);
            
            if ($desactivado) {
                $this->session->setMessage('success', 'Pacto desactivado correctamente');
            } else {
                throw new Exception('No se pudo desactivar el pacto');
            }
            
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al desactivar pacto: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Procesa solicitudes API para obtener datos
     */
    public function processApiRequest(): void
    {
        header('Content-Type: application/json');
        
        try {
            $action = $_GET['action'] ?? '';
            
            switch ($action) {
                case 'getById':
                    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                    $pacto = $this->getById($id);
                    
                    if ($pacto) {
                        echo json_encode([
                            'error' => false,
                            'pacto' => [
                                'id' => $pacto->getIdPacto(),
                                'id_producto' => $pacto->getIdProducto(),
                                'tipo_ubicacion' => $pacto->getTipoUbicacion(),
                                'id_destino' => $pacto->getIdDestino(),
                                'cantidad_pactada' => $pacto->getCantidadPactada(),
                                'activo' => $pacto->isActivo()
                            ]
                        ]);
                    } else {
                        echo json_encode([
                            'error' => true,
                            'mensaje' => 'Pacto no encontrado'
                        ]);
                    }
                    break;
                    
                case 'getByProducto':
                    $idProducto = isset($_GET['idProducto']) ? (int)$_GET['idProducto'] : 0;
                    $pactos = $this->getByProducto($idProducto);
                    
                    echo json_encode([
                        'error' => false,
                        'pactos' => array_map(function($p) {
                            return [
                                'id' => $p->getIdPacto(),
                                'id_producto' => $p->getIdProducto(),
                                'tipo_ubicacion' => $p->getTipoUbicacion(),
                                'id_destino' => $p->getIdDestino(),
                                'cantidad_pactada' => $p->getCantidadPactada(),
                                'activo' => $p->isActivo()
                            ];
                        }, $pactos)
                    ]);
                    break;
                    
                case 'getByDestino':
                    $tipoUbicacion = $_GET['tipoUbicacion'] ?? '';
                    $idDestino = isset($_GET['idDestino']) ? (int)$_GET['idDestino'] : 0;
                    $pactos = $this->getByDestino($tipoUbicacion, $idDestino);
                    
                    echo json_encode([
                        'error' => false,
                        'pactos' => array_map(function($p) {
                            return [
                                'id' => $p->getIdPacto(),
                                'id_producto' => $p->getIdProducto(),
                                'tipo_ubicacion' => $p->getTipoUbicacion(),
                                'id_destino' => $p->getIdDestino(),
                                'cantidad_pactada' => $p->getCantidadPactada(),
                                'activo' => $p->isActivo()
                            ];
                        }, $pactos)
                    ]);
                    break;
                    
                default:
                    echo json_encode([
                        'error' => true,
                        'mensaje' => 'Acción no reconocida'
                    ]);
                    break;
            }
        } catch (Exception $e) {
            echo json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Procesa las solicitudes POST y GET
     */
    public function processRequest(): void
    {
        // Determinar si es una solicitud API
        $isApiRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                        
        if ($isApiRequest && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->processApiRequest();
            return;
        }
        
        // Si no es una petición POST, no hay nada que hacer
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
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
            case 'desactivar':
                $this->desactivar();
                break;
            default:
                $this->session->setMessage('error', 'Acción no reconocida');
                $this->redirectToIndex();
                break;
        }
    }
    
    /**
     * Redirecciona a la página de índice de pactos
     */
    private function redirectToIndex(): void
    {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/Pactos/index.php');
        exit;
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new PactosController();
    $controller->processRequest();
}
