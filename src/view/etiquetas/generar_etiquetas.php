<?php
require_once(__DIR__ . '/../../controller/EtiquetasController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');
require_once(__DIR__ . '/../../controller/ReposicionesController.php');

use controller\EtiquetasController;
use controller\ProductoController;
use controller\ReposicionesController;

$etiquetasController = new EtiquetasController();
$productoController = new ProductoController();
$reposicionController = new ReposicionesController();

// Obtener productos y reposiciones
$resultadoProductos = $productoController->index();
$resultadoReposiciones = $reposicionController->index();

// Procesar formulario
$mensaje = '';
$tipoAlerta = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['id_producto']) && 
        isset($_POST['id_reposicion']) && 
        isset($_POST['tipo']) && 
        isset($_POST['prioridad'])
    ) {
        $idProducto = $_POST['id_producto'];
        $idReposicion = $_POST['id_reposicion'];
        $tipo = $_POST['tipo'];
        $prioridad = $_POST['prioridad'];
        
        $resultado = $etiquetasController->store($idProducto, $idReposicion, $tipo, $prioridad, false);
        
        if (!$resultado['error']) {
            $mensaje = $resultado['mensaje'];
            $tipoAlerta = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipoAlerta = 'danger';
        }
    } else {
        $mensaje = 'Todos los campos son obligatorios';
        $tipoAlerta = 'warning';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generación de Etiquetas</title>
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/etiquetas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include_once(__DIR__ . '/../templates/header.php'); ?>
    
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-tags"></i> Generación de Etiquetas</h4>
            </div>
            <div class="card-body">
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipoAlerta; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="id_producto" class="form-label">Producto</label>
                            <select class="form-select" id="id_producto" name="id_producto" required>
                                <option value="">Seleccione un producto</option>
                                <?php if (!$resultadoProductos['error'] && isset($resultadoProductos['productos'])): ?>
                                    <?php foreach ($resultadoProductos['productos'] as $producto): ?>
                                        <option value="<?php echo $producto->getIdProducto(); ?>">
                                            <?php echo $producto->getNombre(); ?> - <?php echo $producto->getDescripcion(); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="id_reposicion" class="form-label">Reposición</label>
                            <select class="form-select" id="id_reposicion" name="id_reposicion" required>
                                <option value="">Seleccione una reposición</option>
                                <?php if (!$resultadoReposiciones['error'] && isset($resultadoReposiciones['reposiciones'])): ?>
                                    <?php foreach ($resultadoReposiciones['reposiciones'] as $reposicion): ?>
                                        <option value="<?php echo $reposicion->getIdReposicion(); ?>">
                                            ID: <?php echo $reposicion->getIdReposicion(); ?> - Fecha: <?php echo $reposicion->getFechaCreacion(); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label">Tipo de Etiqueta</label>
                            <div class="etiquetas-container">
                                <div class="etiqueta-option">
                                    <input type="radio" id="normal_informativa" name="tipo_prioridad" value="Informativa,Normal" required>
                                    <label for="normal_informativa" class="etiqueta normal">
                                        <div class="etiqueta-content">
                                            <div class="color-indicator blue"></div>
                                            <span>Normal Informativa</span>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="etiqueta-option">
                                    <input type="radio" id="normal_rfid" name="tipo_prioridad" value="RFID,Normal">
                                    <label for="normal_rfid" class="etiqueta normal">
                                        <div class="etiqueta-content">
                                            <div class="color-indicator blue"></div>
                                            <span>Normal RFID</span>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="etiqueta-option">
                                    <input type="radio" id="urgente_informativa" name="tipo_prioridad" value="Informativa,Urgente">
                                    <label for="urgente_informativa" class="etiqueta urgente">
                                        <div class="etiqueta-content">
                                            <div class="color-indicator red"></div>
                                            <span>Urgente Informativa</span>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="etiqueta-option">
                                    <input type="radio" id="urgente_rfid" name="tipo_prioridad" value="RFID,Urgente">
                                    <label for="urgente_rfid" class="etiqueta urgente">
                                        <div class="etiqueta-content">
                                            <div class="color-indicator red"></div>
                                            <span>Urgente RFID</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos ocultos que se actualizarán con JavaScript -->
                    <input type="hidden" id="tipo" name="tipo" value="">
                    <input type="hidden" id="prioridad" name="prioridad" value="">
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-print"></i> Generar Etiqueta
                            </button>
                            <a href="listar_etiquetas.php" class="btn btn-secondary">
                                <i class="fas fa-list"></i> Ver Etiquetas
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Vista previa de etiqueta -->
        <div class="card shadow mt-4">
            <div class="card-header bg-info text-white">
                <h5><i class="fas fa-eye"></i> Vista previa</h5>
            </div>
            <div class="card-body">
                <div class="preview-container">
                    <div id="etiqueta-preview" class="etiqueta-preview normal">
                        <div class="preview-header">
                            <div class="color-indicator blue"></div>
                            <span id="preview-tipo-prioridad">Normal Informativa</span>
                        </div>
                        <div class="preview-body">
                            <p><strong>Producto:</strong> <span id="preview-producto">Seleccione un producto</span></p>
                            <p><strong>Referencia:</strong> <span id="preview-referencia">-</span></p>
                            <p><strong>Reposición ID:</strong> <span id="preview-reposicion">-</span></p>
                            <p><strong>Fecha:</strong> <span id="preview-fecha"><?php echo date('d/m/Y'); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar la selección de tipo de etiqueta
            const radioButtons = document.querySelectorAll('input[name="tipo_prioridad"]');
            const tipoInput = document.getElementById('tipo');
            const prioridadInput = document.getElementById('prioridad');
            const etiquetaPreview = document.getElementById('etiqueta-preview');
            const previewTipoPrioridad = document.getElementById('preview-tipo-prioridad');
            const colorIndicatorPreview = etiquetaPreview.querySelector('.color-indicator');
            
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    const [tipo, prioridad] = this.value.split(',');
                    tipoInput.value = tipo;
                    prioridadInput.value = prioridad;
                    
                    // Actualizar vista previa
                    previewTipoPrioridad.textContent = prioridad + ' ' + tipo;
                    
                    if (prioridad === 'Normal') {
                        etiquetaPreview.className = 'etiqueta-preview normal';
                        colorIndicatorPreview.className = 'color-indicator blue';
                    } else {
                        etiquetaPreview.className = 'etiqueta-preview urgente';
                        colorIndicatorPreview.className = 'color-indicator red';
                    }
                });
            });
            
            // Actualizar la vista previa con el producto seleccionado
            const productoSelect = document.getElementById('id_producto');
            const previewProducto = document.getElementById('preview-producto');
            const previewReferencia = document.getElementById('preview-referencia');
            
            productoSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const productoInfo = selectedOption.textContent.split(' - ');
                    previewProducto.textContent = productoInfo[0];
                    previewReferencia.textContent = productoInfo[1] || '-';
                } else {
                    previewProducto.textContent = 'Seleccione un producto';
                    previewReferencia.textContent = '-';
                }
            });
            
            // Actualizar la vista previa con la reposición seleccionada
            const reposicionSelect = document.getElementById('id_reposicion');
            const previewReposicion = document.getElementById('preview-reposicion');
            
            reposicionSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const reposicionId = selectedOption.value;
                    previewReposicion.textContent = reposicionId;
                } else {
                    previewReposicion.textContent = '-';
                }
            });
        });
    </script>
</body>
</html>
