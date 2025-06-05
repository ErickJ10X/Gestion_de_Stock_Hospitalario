<?php

namespace controller;

require_once __DIR__ . '/../model/service/EtiquetaService.php';
require_once __DIR__ . '/../model/entity/Etiqueta.php';

use model\service\EtiquetaService;
use model\entity\Etiqueta;
use InvalidArgumentException;
use Exception;

/**
 * Controlador para la gestión de etiquetas
 */
class EtiquetasController {
    private EtiquetaService $etiquetaService;

    public function __construct() {
        $this->etiquetaService = new EtiquetaService();
    }

    /**
     * Obtiene todas las etiquetas
     * @return array Array con todas las etiquetas o mensaje de error
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
     * Obtiene una etiqueta por su ID
     * @param int $id ID de la etiqueta
     * @return array Etiqueta encontrada o mensaje de error
     */
    public function show(int $id): array {
        try {
            $etiqueta = $this->etiquetaService->getEtiquetaById($id);
            
            if (!$etiqueta) {
                return [
                    'error' => true,
                    'mensaje' => 'Etiqueta no encontrada'
                ];
            }
            
            return [
                'error' => false,
                'etiqueta' => $etiqueta
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener la etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crea una nueva etiqueta
     * @param int $idProducto ID del producto
     * @param int $idReposicion ID de la reposición
     * @param string $tipo Tipo de etiqueta (Informativa o RFID)
     * @param string $prioridad Prioridad de la etiqueta (Normal o Urgente)
     * @param bool $impresa Estado de impresión de la etiqueta
     * @return array Resultado de la operación
     */
    public function store(int $idProducto, int $idReposicion, string $tipo, string $prioridad, bool $impresa = false): array {
        try {
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
                'etiqueta' => $etiqueta,
                'mensaje' => 'Etiqueta generada correctamente'
            ];
        } catch (InvalidArgumentException $e) {
            return [
                'error' => true,
                'mensaje' => 'Error en los datos de la etiqueta: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al crear la etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualiza una etiqueta existente
     * @param int $id ID de la etiqueta
     * @param array $data Datos a actualizar
     * @return array Resultado de la operación
     */
    public function update(int $id, array $data): array {
        try {
            $etiqueta = $this->etiquetaService->updateEtiqueta($id, $data);
            
            return [
                'error' => false,
                'etiqueta' => $etiqueta,
                'mensaje' => 'Etiqueta actualizada correctamente'
            ];
        } catch (InvalidArgumentException $e) {
            return [
                'error' => true,
                'mensaje' => 'Error en los datos de la etiqueta: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al actualizar la etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Elimina una etiqueta
     * @param int $id ID de la etiqueta
     * @return array Resultado de la operación
     */
    public function destroy(int $id): array {
        try {
            $result = $this->etiquetaService->deleteEtiqueta($id);
            
            if ($result) {
                return [
                    'error' => false,
                    'mensaje' => 'Etiqueta eliminada correctamente'
                ];
            } else {
                return [
                    'error' => true,
                    'mensaje' => 'No se pudo eliminar la etiqueta'
                ];
            }
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al eliminar la etiqueta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Marca una etiqueta como impresa
     * @param int $id ID de la etiqueta
     * @return array Resultado de la operación
     */
    public function marcarComoImpresa(int $id): array {
        try {
            $result = $this->etiquetaService->marcarComoImpresa($id);
            
            if ($result) {
                return [
                    'error' => false,
                    'mensaje' => 'Etiqueta marcada como impresa correctamente'
                ];
            } else {
                return [
                    'error' => true,
                    'mensaje' => 'No se pudo marcar la etiqueta como impresa'
                ];
            }
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al marcar la etiqueta como impresa: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las etiquetas asociadas a una reposición
     * @param int $idReposicion ID de la reposición
     * @return array Etiquetas encontradas o mensaje de error
     */
    public function getEtiquetasByReposicion(int $idReposicion): array {
        try {
            $etiquetas = $this->etiquetaService->getEtiquetasByReposicion($idReposicion);
            
            return [
                'error' => false,
                'etiquetas' => $etiquetas
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener las etiquetas de la reposición: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las etiquetas que no han sido impresas
     * @return array Etiquetas no impresas o mensaje de error
     */
    public function getEtiquetasNoImpresas(): array {
        try {
            $etiquetas = $this->etiquetaService->getEtiquetasNoImpresas();
            
            return [
                'error' => false,
                'etiquetas' => $etiquetas
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al obtener las etiquetas no impresas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Genera etiquetas a partir de una reposición
     * @param int $idReposicion ID de la reposición
     * @param string $tipo Tipo de etiqueta (Informativa o RFID)
     * @return array Resultado de la operación
     */
    public function generarEtiquetasDesdeReposicion(int $idReposicion, string $tipo = 'Informativa'): array {
        try {
            $etiquetas = $this->etiquetaService->generarEtiquetasDesdeReposicion($idReposicion, $tipo);
            
            return [
                'error' => false,
                'etiquetas' => $etiquetas,
                'mensaje' => 'Etiquetas generadas correctamente'
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al generar etiquetas desde la reposición: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Genera varias etiquetas para un producto y una reposición
     * @param int $idProducto ID del producto
     * @param int $idReposicion ID de la reposición
     * @param string $tipo Tipo de etiqueta
     * @param string $prioridad Prioridad de la etiqueta
     * @param int $cantidad Cantidad de etiquetas a generar
     * @return array Resultado de la operación
     */
    public function generarMultiplesEtiquetas(int $idProducto, int $idReposicion, string $tipo, string $prioridad, int $cantidad): array {
        try {
            if ($cantidad <= 0) {
                throw new InvalidArgumentException('La cantidad de etiquetas debe ser mayor que cero');
            }
            
            $etiquetas = [];
            $errores = [];
            
            for ($i = 0; $i < $cantidad; $i++) {
                try {
                    $resultado = $this->store($idProducto, $idReposicion, $tipo, $prioridad);
                    if (!$resultado['error']) {
                        $etiquetas[] = $resultado['etiqueta'];
                    } else {
                        $errores[] = 'Error en etiqueta #' . ($i + 1) . ': ' . $resultado['mensaje'];
                    }
                } catch (Exception $e) {
                    $errores[] = 'Error en etiqueta #' . ($i + 1) . ': ' . $e->getMessage();
                }
            }
            
            if (empty($errores)) {
                return [
                    'error' => false,
                    'etiquetas' => $etiquetas,
                    'mensaje' => 'Se han generado ' . count($etiquetas) . ' etiquetas correctamente'
                ];
            } else {
                return [
                    'error' => true,
                    'etiquetas' => $etiquetas,
                    'errores' => $errores,
                    'mensaje' => 'Se han generado ' . count($etiquetas) . ' etiquetas, pero con ' . count($errores) . ' errores'
                ];
            }
            
        } catch (InvalidArgumentException $e) {
            return [
                'error' => true,
                'mensaje' => 'Error en los parámetros: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al generar las etiquetas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Procesa los datos del formulario para generar etiquetas
     * @param array $formData Datos del formulario
     * @return array Resultado de la operación
     */
    public function procesarFormularioGenerar(array $formData): array {
        try {
            // Validar datos del formulario
            if (!isset($formData['id_producto']) || empty($formData['id_producto'])) {
                throw new InvalidArgumentException('Debe seleccionar un producto');
            }
            
            if (!isset($formData['id_reposicion']) || empty($formData['id_reposicion'])) {
                throw new InvalidArgumentException('Debe seleccionar una reposición');
            }
            
            if (!isset($formData['tipo']) || !in_array($formData['tipo'], ['Informativa', 'RFID'])) {
                throw new InvalidArgumentException('El tipo de etiqueta debe ser Informativa o RFID');
            }
            
            if (!isset($formData['prioridad']) || !in_array($formData['prioridad'], ['Normal', 'Urgente'])) {
                throw new InvalidArgumentException('La prioridad debe ser Normal o Urgente');
            }
            
            $idProducto = intval($formData['id_producto']);
            $idReposicion = intval($formData['id_reposicion']);
            $tipo = $formData['tipo'];
            $prioridad = $formData['prioridad'];
            
            // Cantidad de etiquetas a generar (opcional, por defecto 1)
            $cantidad = isset($formData['cantidad']) && intval($formData['cantidad']) > 0 
                      ? intval($formData['cantidad']) 
                      : 1;
            
            if ($cantidad === 1) {
                return $this->store($idProducto, $idReposicion, $tipo, $prioridad);
            } else {
                return $this->generarMultiplesEtiquetas($idProducto, $idReposicion, $tipo, $prioridad, $cantidad);
            }
            
        } catch (InvalidArgumentException $e) {
            return [
                'error' => true,
                'mensaje' => $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al procesar el formulario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Procesa los datos del formulario para marcar etiquetas como impresas
     * @param array $formData Datos del formulario
     * @return array Resultado de la operación
     */
    public function procesarFormularioMarcarImpresas(array $formData): array {
        try {
            if (!isset($formData['id_etiqueta'])) {
                throw new InvalidArgumentException('Debe proporcionar el ID de la etiqueta');
            }
            
            $idEtiqueta = intval($formData['id_etiqueta']);
            return $this->marcarComoImpresa($idEtiqueta);
            
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => 'Error al marcar la etiqueta como impresa: ' . $e->getMessage()
            ];
        }
    }
}
