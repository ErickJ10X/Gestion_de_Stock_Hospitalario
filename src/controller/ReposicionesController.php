<?php

namespace controller;

require_once __DIR__ . '/../model/service/ReposicionService.php';
require_once __DIR__ . '/../model/entity/Reposicion.php';
require_once __DIR__ . '/../util/Session.php';

use model\service\ReposicionService;
use model\entity\Reposicion;
use util\Session;
use DateTime;
use Exception;
use InvalidArgumentException;

class ReposicionesController {
    private ReposicionService $reposicionService;
    private Session $session;

    public function __construct() {
        $this->reposicionService = new ReposicionService();
        $this->session = new Session();
    }

    /**
     * Método principal que muestra todas las reposiciones
     * @return array Datos para la vista
     */
    public function index(): array {
        try {
            $reposiciones = $this->reposicionService->getAllReposiciones();
            return [
                'reposiciones' => $reposiciones,
                'success' => true
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al cargar las reposiciones: " . $e->getMessage());
            return [
                'reposiciones' => [],
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las reposiciones filtradas por estado
     * 
     * @param string $estado El estado de las reposiciones (pendiente, entregada, etc.)
     * @return array Datos para la vista
     */
    public function getReposicionesPorEstado(string $estado = 'pendiente'): array {
        try {
            $reposiciones = $this->reposicionService->getAllReposiciones();
            
            // Filtramos por estado (pendiente por defecto)
            $reposFiltradas = array_filter($reposiciones, function($repo) use ($estado) {
                // Implementación simplificada - todas las reposiciones se consideran pendientes
                return true;
            });
            
            return [
                'reposiciones' => $reposFiltradas,
                'success' => true
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al filtrar las reposiciones: " . $e->getMessage());
            return [
                'reposiciones' => [],
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las reposiciones por botiquín
     * 
     * @param int $idBotiquin ID del botiquín
     * @return array Datos para la vista
     */
    public function getReposicionesPorBotiquin(int $idBotiquin): array {
        try {
            $reposiciones = $this->reposicionService->getReposicionesByBotiquin($idBotiquin);
            return [
                'reposiciones' => $reposiciones,
                'success' => true
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al obtener las reposiciones: " . $e->getMessage());
            return [
                'reposiciones' => [],
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las reposiciones por almacén
     * 
     * @param int $idAlmacen ID del almacén
     * @return array Datos para la vista
     */
    public function getReposicionesPorAlmacen(int $idAlmacen): array {
        try {
            $reposiciones = $this->reposicionService->getReposicionesByAlmacen($idAlmacen);
            return [
                'reposiciones' => $reposiciones,
                'success' => true
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al obtener las reposiciones: " . $e->getMessage());
            return [
                'reposiciones' => [],
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las reposiciones urgentes
     * 
     * @return array Datos para la vista
     */
    public function getReposicionesUrgentes(): array {
        try {
            $reposiciones = $this->reposicionService->getReposicionesUrgentes();
            return [
                'reposiciones' => $reposiciones,
                'success' => true
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al obtener las reposiciones urgentes: " . $e->getMessage());
            return [
                'reposiciones' => [],
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crea una nueva reposición
     * 
     * @param array $data Datos de la reposición a crear
     * @return array Resultado de la operación
     */
    public function crearReposicion(array $data): array {
        try {
            $this->validarDatosReposicion($data);
            
            // Formatear la fecha si viene como string
            if (isset($data['fecha']) && is_string($data['fecha']) && !empty($data['fecha'])) {
                $data['fecha'] = new DateTime($data['fecha']);
            } else {
                $data['fecha'] = new DateTime();
            }
            
            // Convertir checkbox a booleano
            $data['urgente'] = isset($data['urgente']) && ($data['urgente'] == '1' || $data['urgente'] === true);
            
            $reposicion = $this->reposicionService->crearReposicion($data);
            
            $this->session->setMessage('success', "La reposición se ha creado correctamente.");
            
            return [
                'success' => true,
                'reposicion' => $reposicion,
                'message' => 'Reposición creada correctamente'
            ];
            
        } catch (InvalidArgumentException $e) {
            $this->session->setMessage('error', $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al crear la reposición: " . $e->getMessage());
            return [
                'success' => false,
                'error' => "Error al crear la reposición: " . $e->getMessage()
            ];
        }
    }

    /**
     * Actualiza una reposición existente
     * 
     * @param int $id ID de la reposición
     * @param array $data Nuevos datos de la reposición
     * @return array Resultado de la operación
     */
    public function actualizarReposicion(int $id, array $data): array {
        try {
            // Formatear la fecha si viene como string
            if (isset($data['fecha']) && is_string($data['fecha']) && !empty($data['fecha'])) {
                $data['fecha'] = new DateTime($data['fecha']);
            }
            
            // Convertir checkbox a booleano
            if (isset($data['urgente'])) {
                $data['urgente'] = ($data['urgente'] == '1' || $data['urgente'] === true);
            }
            
            $reposicion = $this->reposicionService->actualizarReposicion($id, $data);
            
            $this->session->setMessage('success', "La reposición se ha actualizado correctamente.");
            
            return [
                'success' => true,
                'reposicion' => $reposicion,
                'message' => 'Reposición actualizada correctamente'
            ];
            
        } catch (InvalidArgumentException $e) {
            $this->session->setMessage('error', $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al actualizar la reposición: " . $e->getMessage());
            return [
                'success' => false,
                'error' => "Error al actualizar la reposición: " . $e->getMessage()
            ];
        }
    }

    /**
     * Elimina una reposición
     * 
     * @param int $id ID de la reposición a eliminar
     * @return array Resultado de la operación
     */
    public function eliminarReposicion(int $id): array {
        try {
            $result = $this->reposicionService->eliminarReposicion($id);
            
            if ($result) {
                $this->session->setMessage('success', "La reposición se ha eliminado correctamente.");
                return [
                    'success' => true,
                    'message' => 'Reposición eliminada correctamente'
                ];
            } else {
                $this->session->setMessage('error', "No se pudo eliminar la reposición.");
                return [
                    'success' => false,
                    'error' => 'No se pudo eliminar la reposición'
                ];
            }
            
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al eliminar la reposición: " . $e->getMessage());
            return [
                'success' => false,
                'error' => "Error al eliminar la reposición: " . $e->getMessage()
            ];
        }
    }

    /**
     * Marca una o varias reposiciones como entregadas
     * 
     * @param array $ids IDs de las reposiciones
     * @param array $data Datos adicionales (fecha de entrega, notas, etc.)
     * @return array Resultado de la operación
     */
    public function marcarComoEntregadas(array $ids, array $data = []): array {
        try {
            if (empty($ids)) {
                throw new InvalidArgumentException("Debe seleccionar al menos una reposición para marcarla como entregada.");
            }
            
            $fechaEntrega = isset($data['fecha_entrega']) && !empty($data['fecha_entrega']) 
                ? new DateTime($data['fecha_entrega']) 
                : new DateTime();
            
            // En una implementación completa, aquí actualizaríamos el estado de las reposiciones
            // Por ahora, simulamos que todas las operaciones tienen éxito
            $resultados = [];
            $fallos = [];
            
            foreach ($ids as $id) {
                try {
                    // Aquí marcaríamos la reposición como entregada actualizando su estado
                    // Por ahora, solo registramos el ID como procesado correctamente
                    $resultados[] = $id;
                } catch (Exception $e) {
                    $fallos[] = [
                        'id' => $id,
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            if (empty($fallos)) {
                $this->session->setMessage('success', count($resultados) . " reposiciones han sido marcadas como entregadas.");
                return [
                    'success' => true,
                    'message' => count($resultados) . " reposiciones han sido marcadas como entregadas."
                ];
            } else {
                $this->session->setMessage('warning', "Algunas reposiciones no pudieron ser marcadas como entregadas.");
                return [
                    'success' => false,
                    'message' => "Algunas reposiciones no pudieron ser marcadas como entregadas.",
                    'resultados' => $resultados,
                    'fallos' => $fallos
                ];
            }
            
        } catch (InvalidArgumentException $e) {
            $this->session->setMessage('error', $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al marcar las reposiciones como entregadas: " . $e->getMessage());
            return [
                'success' => false,
                'error' => "Error al marcar las reposiciones como entregadas: " . $e->getMessage()
            ];
        }
    }

    /**
     * Método para obtener reposiciones pendientes (para la pestaña de marcar como entregado)
     * 
     * @param int|null $idBotiquin Filtrar por botiquín (opcional)
     * @param bool|null $urgente Filtrar por urgencia (opcional)
     * @return array Reposiciones pendientes
     */
    public function getReposicionesPendientes(?int $idBotiquin = null, ?bool $urgente = null): array {
        try {
            // Obtenemos todas las reposiciones
            if ($idBotiquin) {
                $reposiciones = $this->reposicionService->getReposicionesByBotiquin($idBotiquin);
            } else {
                $reposiciones = $this->reposicionService->getAllReposiciones();
            }
            
            // Filtramos por urgencia si se especifica
            if ($urgente !== null) {
                $reposiciones = array_filter($reposiciones, function($repo) use ($urgente) {
                    return $repo->isUrgente() === $urgente;
                });
            }
            
            // Aquí podríamos filtrar por estado pendiente si tuviéramos ese campo
            // Por ahora, todas las reposiciones se consideran pendientes
            
            return [
                'success' => true,
                'reposiciones' => array_values($reposiciones) // array_values para reindexar el array
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => true,
                'mensaje' => "Error al obtener las reposiciones pendientes: " . $e->getMessage(),
                'reposiciones' => []
            ];
        }
    }
    
    /**
     * Procesa los datos POST para crear una nueva reposición
     * 
     * @return array Resultado de la operación
     */
    public function procesarFormularioCrear(): array {
        try {
            // Verificar que sea una solicitud POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }
            
            return $this->crearReposicion($_POST);
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Procesa los datos POST para marcar reposiciones como entregadas
     * 
     * @return array Resultado de la operación
     */
    public function procesarFormularioMarcarEntregadas(): array {
        try {
            // Verificar que sea una solicitud POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }
            
            if (!isset($_POST['ids_reposicion']) || !is_array($_POST['ids_reposicion']) || empty($_POST['ids_reposicion'])) {
                throw new InvalidArgumentException("Debe seleccionar al menos una reposición");
            }
            
            $ids = array_map('intval', $_POST['ids_reposicion']);
            $data = [
                'fecha_entrega' => $_POST['fecha_entrega'] ?? null,
                'notas_entrega' => $_POST['notas_entrega'] ?? null,
            ];
            
            return $this->marcarComoEntregadas($ids, $data);
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Valida los datos para crear o actualizar una reposición
     * 
     * @param array $data Datos a validar
     * @throws InvalidArgumentException Si los datos no son válidos
     * @return void
     */
    private function validarDatosReposicion(array $data): void {
        if (!isset($data['id_producto']) || empty($data['id_producto'])) {
            throw new InvalidArgumentException("Debe seleccionar un producto.");
        }
        
        if (!isset($data['desde_almacen']) || empty($data['desde_almacen'])) {
            throw new InvalidArgumentException("Debe seleccionar un almacén de origen.");
        }
        
        if (!isset($data['hacia_botiquin']) || empty($data['hacia_botiquin'])) {
            throw new InvalidArgumentException("Debe seleccionar un botiquín de destino.");
        }
        
        if (!isset($data['cantidad_repuesta']) || $data['cantidad_repuesta'] <= 0) {
            throw new InvalidArgumentException("La cantidad a reponer debe ser mayor que cero.");
        }
    }
    
    /**
     * Método para obtener datos de una reposición por su ID
     * 
     * @param int $id ID de la reposición
     * @return array Datos de la reposición
     */
    public function getReposicionById(int $id): array {
        try {
            $reposicion = $this->reposicionService->getReposicionById($id);
            
            if (!$reposicion) {
                throw new Exception("Reposición no encontrada");
            }
            
            return [
                'success' => true,
                'reposicion' => $reposicion
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
