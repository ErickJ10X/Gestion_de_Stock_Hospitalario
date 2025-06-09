<?php

namespace controller;

require_once __DIR__ . '/../model/service/EtiquetaService.php';
require_once __DIR__ . '/../util/Session.php';
require_once __DIR__ . '/../util/AuthGuard.php';
require_once __DIR__ . '/../util/Redirect.php';

use model\service\EtiquetaService;
use util\Session;
use util\AuthGuard;
use util\Redirect;
use Exception;

class EtiquetasController {
    private EtiquetaService $etiquetaService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct() {
        $this->etiquetaService = new EtiquetaService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Obtiene todas las etiquetas
     */
    public function index(): array {
        try {
            $etiquetas = $this->etiquetaService->getAllEtiquetas();
            return [
                'error' => false,
                'etiquetas' => $etiquetas
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener las etiquetas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene una etiqueta por ID
     */
    public function show(int $id): array {
        try {
            $etiqueta = $this->etiquetaService->getEtiquetaById($id);
            if ($etiqueta) {
                return [
                    'error' => false,
                    'etiqueta' => $etiqueta
                ];
            }
            return [
                'error' => true,
                'mensaje' => 'Etiqueta no encontrada'
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener la etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crear una nueva etiqueta
     */
    public function store(int $idProducto, int $idReposicion, string $tipo, string $prioridad, bool $impresa = false): array {
        try {
            // Crear un array con los datos en el formato esperado por el servicio
            $data = [
                'id_producto' => $idProducto,
                'id_reposicion' => $idReposicion,
                'tipo' => $tipo,
                'prioridad' => $prioridad,
                'impresa' => $impresa
            ];
            
            $etiqueta = $this->etiquetaService->createEtiqueta($data);
            return [
                'error' => false,
                'mensaje' => 'Etiqueta creada correctamente',
                'etiqueta' => $etiqueta
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al crear la etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Marca una etiqueta como impresa
     */
    public function marcarComoImpresa(int $id): array {
        try {
            $resultado = $this->etiquetaService->marcarComoImpresa($id);
            if ($resultado) {
                return [
                    'error' => false,
                    'mensaje' => 'Etiqueta marcada como impresa correctamente'
                ];
            }
            return [
                'error' => true,
                'mensaje' => 'No se pudo marcar la etiqueta como impresa'
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al marcar la etiqueta como impresa: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Elimina una etiqueta
     */
    public function destroy(int $id): array {
        try {
            $resultado = $this->etiquetaService->deleteEtiqueta($id);
            if ($resultado) {
                return [
                    'error' => false,
                    'mensaje' => 'Etiqueta eliminada correctamente'
                ];
            }
            return [
                'error' => true,
                'mensaje' => 'No se pudo eliminar la etiqueta'
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al eliminar la etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Procesa una solicitud
     */
    public function processRequest(): void {
        try {
            $this->authGuard->requireGestorHospital();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                switch ($action) {
                    case 'crear':
                        if (
                            isset($_POST['id_producto']) && 
                            isset($_POST['id_reposicion']) && 
                            isset($_POST['tipo']) && 
                            isset($_POST['prioridad'])
                        ) {
                            $idProducto = (int)$_POST['id_producto'];
                            $idReposicion = (int)$_POST['id_reposicion'];
                            $tipo = $_POST['tipo'];
                            $prioridad = $_POST['prioridad'];
                            
                            $resultado = $this->store($idProducto, $idReposicion, $tipo, $prioridad, false);
                            
                            if (!$resultado['error']) {
                                $this->session->setMessage('success', $resultado['mensaje']);
                            } else {
                                $this->session->setMessage('error', $resultado['mensaje']);
                            }
                        } else {
                            $this->session->setMessage('error', 'Todos los campos son obligatorios');
                        }
                        Redirect::to('/src/view/etiquetas/?tab=generar');
                        break;
                    
                    case 'imprimir':
                        if (isset($_POST['id_etiqueta'])) {
                            $id = (int)$_POST['id_etiqueta'];
                            $resultado = $this->marcarComoImpresa($id);
                            if (!$resultado['error']) {
                                $this->session->setMessage('success', $resultado['mensaje']);
                            } else {
                                $this->session->setMessage('error', $resultado['mensaje']);
                            }
                        } else {
                            $this->session->setMessage('error', 'ID de etiqueta no especificado');
                        }
                        Redirect::to('/src/view/etiquetas/');
                        break;
                    
                    case 'eliminar':
                        if (isset($_POST['id_etiqueta'])) {
                            $id = (int)$_POST['id_etiqueta'];
                            $resultado = $this->destroy($id);
                            if (!$resultado['error']) {
                                $this->session->setMessage('success', $resultado['mensaje']);
                            } else {
                                $this->session->setMessage('error', $resultado['mensaje']);
                            }
                        } else {
                            $this->session->setMessage('error', 'ID de etiqueta no especificado');
                        }
                        Redirect::to('/src/view/etiquetas/');
                        break;

                    default:
                        $this->session->setMessage('error', 'Acción no reconocida');
                        Redirect::to('/src/view/etiquetas/');
                        break;
                }
            }
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            Redirect::to('/src/view/etiquetas/');
        }
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new EtiquetasController();
    $controller->processRequest();
}
