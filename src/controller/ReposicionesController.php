<?php

namespace controller;

require_once __DIR__ . '/../model/service/ReposicionService.php';
require_once __DIR__ . '/../model/entity/Reposicion.php';
require_once __DIR__ . '/../util/Session.php';
require_once __DIR__ . '/../util/AuthGuard.php';
require_once __DIR__ . '/../util/Redirect.php';

use model\service\ReposicionService;
use model\entity\Reposicion;
use util\Session;
use util\AuthGuard;
use util\Redirect;
use DateTime;
use Exception;
use InvalidArgumentException;

class ReposicionesController
{
    private ReposicionService $reposicionService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct()
    {
        $this->reposicionService = new ReposicionService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Método principal que muestra todas las reposiciones
     * @return array Datos para la vista
     */
    public function index(): array
    {
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
     * @param bool $estado Estado de las reposiciones (true para completadas, false para pendientes)
     * @return array Datos para la vista
     */
    public function getReposicionesPorEstado(bool $estado = false): array
    {
        try {
            $reposiciones = $this->reposicionService->getReposicionesByEstado($estado);
            return [
                'reposiciones' => $reposiciones,
                'success' => true,
                'estadoActual' => $estado
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al filtrar las reposiciones: " . $e->getMessage());
            return [
                'reposiciones' => [],
                'success' => false,
                'error' => $e->getMessage(),
                'estadoActual' => $estado
            ];
        }
    }

    /**
     * Obtiene las reposiciones pendientes
     *
     * @return array Datos para la vista
     */
    public function getReposicionesPendientes(): array
    {
        return $this->getReposicionesPorEstado(false);
    }

    /**
     * Obtiene las reposiciones completadas
     *
     * @return array Datos para la vista
     */
    public function getReposicionesCompletadas(): array
    {
        return $this->getReposicionesPorEstado(true);
    }

    /**
     * Obtiene las reposiciones por botiquín
     *
     * @param int $idBotiquin ID del botiquín
     * @return array Datos para la vista
     */
    public function getReposicionesPorBotiquin(int $idBotiquin): array
    {
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
    public function getReposicionesPorAlmacen(int $idAlmacen): array
    {
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
    public function getReposicionesUrgentes(): array
    {
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
    public function crearReposicion(array $data): array
    {
        try {
            $this->validarDatosReposicion($data);

            // Convertir los datos a los tipos correctos
            $data['id_producto'] = (int)$data['id_producto'];
            $data['desde_almacen'] = (int)$data['desde_almacen'];
            $data['hacia_botiquin'] = (int)$data['hacia_botiquin'];
            $data['cantidad_repuesta'] = (int)$data['cantidad_repuesta'];

            // Formatear la fecha si viene como string
            if (isset($data['fecha']) && is_string($data['fecha']) && !empty($data['fecha'])) {
                $data['fecha'] = new DateTime($data['fecha']);
            } else {
                $data['fecha'] = new DateTime();
            }

            // Convertir checkbox a booleano
            $data['urgente'] = isset($data['urgente']) && ($data['urgente'] == '1' || $data['urgente'] === true);

            // Por defecto, las reposiciones se crean con estado pendiente (false)
            $data['estado'] = isset($data['estado']) ? (bool)$data['estado'] : false;

            // Asegurarse de que los campos existen
            $data['notas'] = $data['notas'] ?? '';

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
    public function actualizarReposicion(int $id, array $data): array
    {
        try {
            // Formatear la fecha si viene como string
            if (isset($data['fecha']) && is_string($data['fecha']) && !empty($data['fecha'])) {
                $data['fecha'] = new DateTime($data['fecha']);
            }

            // Convertir checkbox a booleano
            if (isset($data['urgente'])) {
                $data['urgente'] = ($data['urgente'] == '1' || $data['urgente'] === true);
            }

            // Convertir estado a booleano
            if (isset($data['estado'])) {
                $data['estado'] = ($data['estado'] == '1' || $data['estado'] === true);
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
     * Cambia el estado de una reposición
     *
     * @param int $id ID de la reposición
     * @param bool $nuevoEstado Nuevo estado
     * @return array Resultado de la operación
     */
    public function cambiarEstadoReposicion(int $id, bool $nuevoEstado): array
    {
        try {
            $reposicion = $this->reposicionService->cambiarEstadoReposicion($id, $nuevoEstado);

            $estadoTexto = $nuevoEstado ? "completada" : "pendiente";
            $this->session->setMessage('success', "La reposición se ha marcado como {$estadoTexto} correctamente.");

            return [
                'success' => true,
                'reposicion' => $reposicion,
                'message' => "Reposición marcada como {$estadoTexto} correctamente"
            ];
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al cambiar el estado de la reposición: " . $e->getMessage());
            return [
                'success' => false,
                'error' => "Error al cambiar el estado de la reposición: " . $e->getMessage()
            ];
        }
    }

    /**
     * Marca una reposición como completada
     *
     * @param int $id ID de la reposición
     * @return array Resultado de la operación
     */
    public function marcarComoCompletada(int $id): array
    {
        return $this->cambiarEstadoReposicion($id, true);
    }

    /**
     * Marca una reposición como pendiente
     *
     * @param int $id ID de la reposición
     * @return array Resultado de la operación
     */
    public function marcarComoPendiente(int $id): array
    {
        return $this->cambiarEstadoReposicion($id, false);
    }

    /**
     * Marca una o varias reposiciones como entregadas (completadas)
     *
     * @param array $ids IDs de las reposiciones
     * @param array $data Datos adicionales (fecha de entrega, notas, etc.)
     * @return array Resultado de la operación
     */
    public function marcarComoEntregadas(array $ids, array $data = []): array
    {
        try {
            if (empty($ids)) {
                throw new InvalidArgumentException("Debe seleccionar al menos una reposición para marcarla como entregada.");
            }

            // Para depuración - ver qué IDs estamos procesando
            error_log("Intentando marcar como entregadas las reposiciones con IDs: " . implode(', ', $ids));

            $fechaEntrega = isset($data['fecha_entrega']) && !empty($data['fecha_entrega'])
                ? new DateTime($data['fecha_entrega'])
                : new DateTime();

            $resultados = [];
            $fallos = [];

            foreach ($ids as $id) {
                try {
                    $id = (int)$id; // Asegurar que el ID sea un entero

                    // Obtener la reposición por ID
                    $reposicion = $this->reposicionService->getReposicionById($id);

                    if (!$reposicion) {
                        throw new Exception("Reposición con ID $id no encontrada");
                    }

                    // Preparar las notas actualizadas
                    $notasActualizadas = $reposicion->getNotas() ?: '';
                    if (!empty($data['notas_entrega'])) {
                        $notasActualizadas .= ($notasActualizadas ? "\n" : "") .
                            "Entregado el " . $fechaEntrega->format('d/m/Y H:i') . ": " . $data['notas_entrega'];
                    } else {
                        $notasActualizadas .= ($notasActualizadas ? "\n" : "") .
                            "Entregado el " . $fechaEntrega->format('d/m/Y H:i');
                    }

                    // Actualizar la reposición directamente a través del servicio
                    $reposicionActualizada = $this->reposicionService->actualizarReposicion($id, [
                        'estado' => true, // Marcar explícitamente como completada
                        'notas' => $notasActualizadas
                    ]);

                    error_log("Reposición $id actualizada correctamente. Estado: " . ($reposicionActualizada->getEstado() ? "Entregado" : "Pendiente"));
                    $resultados[] = $id;
                } catch (Exception $e) {
                    error_log("Error al actualizar reposición $id: " . $e->getMessage());
                    $fallos[] = [
                        'id' => $id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            if (empty($fallos)) {
                $mensaje = count($resultados) . " reposiciones han sido marcadas como entregadas.";
                $this->session->setMessage('success', $mensaje);
                return [
                    'success' => true,
                    'message' => $mensaje,
                    'actualizadas' => $resultados
                ];
            } else {
                if (!empty($resultados)) {
                    $mensaje = "Algunas reposiciones no pudieron ser marcadas como entregadas.";
                    $this->session->setMessage('warning', $mensaje);
                    return [
                        'success' => false,
                        'message' => $mensaje,
                        'resultados' => $resultados,
                        'fallos' => $fallos
                    ];
                } else {
                    $mensaje = "No se pudo marcar ninguna reposición como entregada.";
                    $this->session->setMessage('error', $mensaje);
                    return [
                        'success' => false,
                        'message' => $mensaje,
                        'fallos' => $fallos
                    ];
                }
            }
        } catch (InvalidArgumentException $e) {
            $this->session->setMessage('error', $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            $mensaje = "Error al marcar las reposiciones como entregadas: " . $e->getMessage();
            $this->session->setMessage('error', $mensaje);
            return [
                'success' => false,
                'error' => $mensaje
            ];
        }
    }

    /**
     * Procesa los datos POST para crear una nueva reposición
     *
     * @return array Resultado de la operación
     */
    public function procesarFormularioCrear(): array
    {
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
    public function procesarFormularioMarcarEntregadas(): array
    {
        try {
            // Verificar que sea una solicitud POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            // Verificar que se hayan enviado IDs
            if (!isset($_POST['ids_reposicion']) || !is_array($_POST['ids_reposicion']) || empty($_POST['ids_reposicion'])) {
                throw new InvalidArgumentException("Debe seleccionar al menos una reposición para marcarla como entregada");
            }

            $ids = array_map('intval', $_POST['ids_reposicion']);

            // Registro de depuración para verificar que se están procesando los IDs correctamente
            error_log("Procesando formulario para marcar como entregadas las reposiciones: " . implode(", ", $ids));

            $data = [
                'fecha_entrega' => isset($_POST['fecha_entrega']) && !empty($_POST['fecha_entrega']) ? $_POST['fecha_entrega'] : null,
                'notas_entrega' => isset($_POST['notas_entrega']) ? $_POST['notas_entrega'] : null,
            ];

            return $this->marcarComoEntregadas($ids, $data);
        } catch (Exception $e) {
            error_log("Error al procesar formulario de marcar como entregadas: " . $e->getMessage());
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
     * @return void
     * @throws InvalidArgumentException Si los datos no son válidos
     */
    private function validarDatosReposicion(array $data): void
    {
        if (!isset($data['id_producto']) || empty($data['id_producto'])) {
            throw new InvalidArgumentException("Debe seleccionar un producto.");
        }

        if (!isset($data['desde_almacen']) || empty($data['desde_almacen'])) {
            throw new InvalidArgumentException("Debe seleccionar un almacén de origen.");
        }

        if (!isset($data['hacia_botiquin']) || empty($data['hacia_botiquin'])) {
            throw new InvalidArgumentException("Debe seleccionar un botiquín de destino.");
        }

        if (!isset($data['cantidad_repuesta']) || (int)$data['cantidad_repuesta'] <= 0) {
            throw new InvalidArgumentException("La cantidad a reponer debe ser mayor que cero.");
        }
    }

    /**
     * Método para obtener datos de una reposición por su ID
     *
     * @param int $id ID de la reposición
     * @return array Datos de la reposición
     */
    public function getReposicionById(int $id): array
    {
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

    /**
     * Procesa una solicitud desde la vista API
     */
    public function processApiRequest(): void
    {
        header('Content-Type: application/json');

        try {
            $this->authGuard->requireGestorHospital();

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $action = $_GET['action'] ?? '';

                switch ($action) {
                    case 'getById':
                        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                        echo json_encode($this->getReposicionById($id));
                        break;
                    case 'getPendientes':
                        echo json_encode($this->getReposicionesPendientes());
                        break;
                    case 'getCompletadas':
                        echo json_encode($this->getReposicionesCompletadas());
                        break;
                    default:
                        echo json_encode([
                            'error' => true,
                            'message' => 'Acción no reconocida'
                        ]);
                        break;
                }
            } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                switch ($action) {
                    case 'crear':
                        $resultado = $this->procesarFormularioCrear();
                        echo json_encode($resultado);
                        break;
                    case 'marcar_entregadas':
                        $resultado = $this->procesarFormularioMarcarEntregadas();
                        echo json_encode($resultado);
                        break;
                    case 'marcar_entregada':
                        if (isset($_POST['id_reposicion'])) {
                            $id = intval($_POST['id_reposicion']);
                            $resultado = $this->marcarComoCompletada($id);
                            echo json_encode($resultado);
                        } else {
                            echo json_encode([
                                'success' => false,
                                'error' => 'ID de reposición no especificado'
                            ]);
                        }
                        break;
                    case 'marcar_pendiente':
                        if (isset($_POST['id_reposicion'])) {
                            $id = intval($_POST['id_reposicion']);
                            $resultado = $this->marcarComoPendiente($id);
                            echo json_encode($resultado);
                        } else {
                            echo json_encode([
                                'success' => false,
                                'error' => 'ID de reposición no especificado'
                            ]);
                        }
                        break;
                    default:
                        echo json_encode([
                            'success' => false,
                            'error' => 'Acción no reconocida'
                        ]);
                        break;
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Procesa la solicitud actual
     */
    public function processRequest(): void
    {
        try {
            $this->authGuard->requireGestorHospital();

            // Determinar si es una solicitud API
            $isApiRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

            if ($isApiRequest) {
                $this->processApiRequest();
                return;
            }

            // Procesar solicitudes normales POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                switch ($action) {
                    case 'crear':
                        $resultado = $this->procesarFormularioCrear();
                        // Redirigir para evitar reenvío de formulario
                        Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=ver-estado');
                        break;
                    case 'marcar_entregadas':
                        $resultado = $this->procesarFormularioMarcarEntregadas();
                        // Redirigir para evitar reenvío de formulario
                        Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=marcar-entregado');
                        break;
                    case 'marcar_entregada':
                        if (isset($_POST['id_reposicion'])) {
                            $id = intval($_POST['id_reposicion']);
                            $resultado = $this->marcarComoCompletada($id);
                            // Redirigir para evitar reenvío de formulario
                            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=ver-estado');
                        } else {
                            $this->session->setMessage('error', 'ID de reposición no especificado');
                            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=ver-estado');
                        }
                        break;
                    case 'marcar_pendiente':
                        if (isset($_POST['id_reposicion'])) {
                            $id = intval($_POST['id_reposicion']);
                            $resultado = $this->marcarComoPendiente($id);
                            // Redirigir para evitar reenvío de formulario
                            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=ver-estado');
                        } else {
                            $this->session->setMessage('error', 'ID de reposición no especificado');
                            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=ver-estado');
                        }
                        break;
                    default:
                        $this->session->setMessage('error', 'Acción no válida');
                        Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/');
                        break;
                }
            }
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/');
        }
    }

    /**
     * Redirecciona a una URL específica
     *
     * @param string $url URL a la que redireccionar
     */
    private function redirect(string $url): void
    {
        if (headers_sent()) {
            echo "<script>window.location.href='$url';</script>";
        } else {
            header("Location: $url");
        }
        exit;
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new ReposicionesController();
    $controller->processRequest();
}