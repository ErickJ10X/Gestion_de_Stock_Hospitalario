<?php
require_once(__DIR__ . '/../../controller/EtiquetasController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');

use controller\EtiquetasController;
use controller\ProductoController;

$etiquetasController = new EtiquetasController();
$productoController = new ProductoController();

// Manejo de acciones
$mensaje = '';
$tipoAlerta = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && isset($_POST['id_etiqueta'])) {
        $idEtiqueta = $_POST['id_etiqueta'];
        
        if ($_POST['accion'] === 'imprimir') {
            $resultado = $etiquetasController->marcarComoImpresa($idEtiqueta);
            if (!$resultado['error']) {
                $mensaje = $resultado['mensaje'];
                $tipoAlerta = 'success';
            } else {
                $mensaje = $resultado['mensaje'];
                $tipoAlerta = 'danger';
            }
        } elseif ($_POST['accion'] === 'eliminar') {
            $resultado = $etiquetasController->destroy($idEtiqueta);
            if (!$resultado['error']) {
                $mensaje = $resultado['mensaje'];
                $tipoAlerta = 'success';
            } else {
                $mensaje = $resultado['mensaje'];
                $tipoAlerta = 'danger';
            }
        }
    }
}

// Obtener etiquetas
$resultado = $etiquetasController->index();
$etiquetas = !$resultado['error'] && isset($resultado['etiquetas']) ? $resultado['etiquetas'] : [];

// Obtener información de los productos para mostrar en la tabla
$productosInfo = [];
if (!empty($etiquetas)) {
    foreach ($etiquetas as $etiqueta) {
        $idProducto = $etiqueta->getIdProducto();
        if (!isset($productosInfo[$idProducto])) {
            $resultadoProducto = $productoController->show($idProducto);
            if (!$resultadoProducto['error'] && isset($resultadoProducto['producto'])) {
                $productosInfo[$idProducto] = $resultadoProducto['producto'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Etiquetas</title>
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/etiquetas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include_once(__DIR__ . '/../templates/header.php'); ?>
    
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-tags"></i> Lista de Etiquetas</h4>
                <a href="generar_etiquetas.php" class="btn btn-light">
                    <i class="fas fa-plus"></i> Nueva Etiqueta
                </a>
            </div>
            <div class="card-body">
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipoAlerta; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Reposición</th>
                                <th>Tipo</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($etiquetas)): ?>
                                <?php foreach ($etiquetas as $etiqueta): ?>
                                    <tr>
                                        <td><?php echo $etiqueta->getIdEtiqueta(); ?></td>
                                        <td>
                                            <?php if (isset($productosInfo[$etiqueta->getIdProducto()])): ?>
                                                <?php echo $productosInfo[$etiqueta->getIdProducto()]->getNombre(); ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo $productosInfo[$etiqueta->getIdProducto()]->getReferencia(); ?>
                                                </small>
                                            <?php else: ?>
                                                ID: <?php echo $etiqueta->getIdProducto(); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $etiqueta->getIdReposicion(); ?></td>
                                        <td>
                                            <?php if ($etiqueta->getTipo() === 'RFID'): ?>
                                                <span class="badge bg-info">RFID</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Informativa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($etiqueta->getPrioridad() === 'Urgente'): ?>
                                                <span class="badge bg-danger">Urgente</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Normal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($etiqueta->getImpresa()): ?>
                                                <span class="badge bg-success">Impresa</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#verEtiquetaModal" 
                                                        data-id="<?php echo $etiqueta->getIdEtiqueta(); ?>"
                                                        data-tipo="<?php echo $etiqueta->getTipo(); ?>"
                                                        data-prioridad="<?php echo $etiqueta->getPrioridad(); ?>"
                                                        data-producto="<?php echo isset($productosInfo[$etiqueta->getIdProducto()]) ? $productosInfo[$etiqueta->getIdProducto()]->getNombre() : ''; ?>"
                                                        data-referencia="<?php echo isset($productosInfo[$etiqueta->getIdProducto()]) ? $productosInfo[$etiqueta->getIdProducto()]->getReferencia() : ''; ?>"
                                                        data-reposicion="<?php echo $etiqueta->getIdReposicion(); ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if (!$etiqueta->getImpresa()): ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="id_etiqueta" value="<?php echo $etiqueta->getIdEtiqueta(); ?>">
                                                        <input type="hidden" name="accion" value="imprimir">
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta etiqueta?')">
                                                    <input type="hidden" name="id_etiqueta" value="<?php echo $etiqueta->getIdEtiqueta(); ?>">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay etiquetas registradas</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para ver etiqueta -->
    <div class="modal fade" id="verEtiquetaModal" tabindex="-1" aria-labelledby="verEtiquetaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verEtiquetaModalLabel">Vista Previa de Etiqueta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="preview-container">
                        <div id="modal-etiqueta-preview" class="etiqueta-preview normal">
                            <div class="preview-header">
                                <div class="color-indicator blue" id="modal-color-indicator"></div>
                                <span id="modal-tipo-prioridad">Normal Informativa</span>
                            </div>
                            <div class="preview-body">
                                <p><strong>Producto:</strong> <span id="modal-producto">-</span></p>
                                <p><strong>Referencia:</strong> <span id="modal-referencia">-</span></p>
                                <p><strong>Reposición ID:</strong> <span id="modal-reposicion">-</span></p>
                                <p><strong>Fecha:</strong> <span id="modal-fecha"><?php echo date('d/m/Y'); ?></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-imprimir-modal">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const verEtiquetaModal = document.getElementById('verEtiquetaModal');
            
            verEtiquetaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const tipo = button.getAttribute('data-tipo');
                const prioridad = button.getAttribute('data-prioridad');
                const producto = button.getAttribute('data-producto');
                const referencia = button.getAttribute('data-referencia');
                const reposicion = button.getAttribute('data-reposicion');
                
                const modalEtiquetaPreview = document.getElementById('modal-etiqueta-preview');
                const modalColorIndicator = document.getElementById('modal-color-indicator');
                const modalTipoPrioridad = document.getElementById('modal-tipo-prioridad');
                const modalProducto = document.getElementById('modal-producto');
                const modalReferencia = document.getElementById('modal-referencia');
                const modalReposicion = document.getElementById('modal-reposicion');
                
                modalTipoPrioridad.textContent = prioridad + ' ' + tipo;
                modalProducto.textContent = producto || '-';
                modalReferencia.textContent = referencia || '-';
                modalReposicion.textContent = reposicion || '-';
                
                if (prioridad === 'Normal') {
                    modalEtiquetaPreview.className = 'etiqueta-preview normal';
                    modalColorIndicator.className = 'color-indicator blue';
                } else {
                    modalEtiquetaPreview.className = 'etiqueta-preview urgente';
                    modalColorIndicator.className = 'color-indicator red';
                }
                
                // Configurar botón de imprimir
                const btnImprimirModal = document.getElementById('btn-imprimir-modal');
                btnImprimirModal.onclick = function() {
                    window.location.href = 'imprimir_etiqueta.php?id=' + id;
                };
            });
        });
    </script>
</body>
</html>
