<?php
if (!isset($pactos) || !isset($session) || !isset($productos)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Array con los tipos de ubicación disponibles
$tiposUbicacion = ['almacen' => 'Almacén', 'botiquin' => 'Botiquín'];

// Obtener almacenes y botiquines (implementar según su estructura de datos)
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');

use controller\AlmacenesController;
use controller\BotiquinController;

$almacenesController = new AlmacenesController();
$botiquinController = new BotiquinController();

$almacenes = $almacenesController->index() ?? [];
$botiquines = $botiquinController->index()['botiquines'] ?? [];
?>

<div class="row">
    <!-- Formulario para agregar pacto -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-primary text-white">
                <i class="fas fa-handshake me-2"></i> Crear Pacto
            </div>
            <div class="card-body">
                <form id="formCrearPacto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PactosHandler.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="mb-3">
                        <label for="producto_pacto" class="form-label">Producto <span class="text-danger">*</span></label>
                        <select class="form-select" id="producto_pacto" name="id_producto" required>
                            <option value="">Seleccione un producto</option>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?= $producto->getIdProducto() ?>">
                                    <?= htmlspecialchars($producto->getCodigo()) ?> - <?= htmlspecialchars($producto->getNombre()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo_ubicacion" class="form-label">Tipo de Ubicación <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo_ubicacion" name="tipo_ubicacion" required>
                            <option value="">Seleccione un tipo de ubicación</option>
                            <?php foreach ($tiposUbicacion as $key => $value): ?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="destino_pacto" class="form-label">Destino <span class="text-danger">*</span></label>
                        <select class="form-select" id="destino_pacto" name="id_destino" required disabled>
                            <option value="">Primero seleccione un tipo de ubicación</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cantidad_pactada" class="form-label">Cantidad Pactada <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="cantidad_pactada" name="cantidad_pactada" min="1" required>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-secondary me-2">
                            <i class="fas fa-broom"></i> Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Formulario para editar pacto -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-warning text-dark">
                <i class="fas fa-edit me-2"></i> Editar Pacto
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="select_pacto" class="form-label">Seleccionar Pacto</label>
                    <select class="form-select" id="select_pacto">
                        <option value="">Seleccione un pacto para editar</option>
                        <?php foreach ($pactos as $pacto): 
                            $nombreProducto = "";
                            foreach ($productos as $producto) {
                                if ($producto->getIdProducto() == $pacto->getIdProducto()) {
                                    $nombreProducto = $producto->getNombre();
                                    break;
                                }
                            }
                            $tipoUbicacion = ($pacto->getTipoUbicacion() == 'almacen') ? 'Almacén' : 'Botiquín';
                        ?>
                            <option value="<?= $pacto->getIdPacto() ?>">
                                <?= htmlspecialchars($nombreProducto) ?> - <?= $tipoUbicacion ?> - Cant: <?= $pacto->getCantidadPactada() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="editar_pacto_form_container" style="display: none;">
                    <form id="formEditarPacto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PactosHandler.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" id="editar_pacto_id" name="id">
                        
                        <div class="mb-3">
                            <label for="editar_producto_pacto" class="form-label">Producto <span class="text-danger">*</span></label>
                            <select class="form-select" id="editar_producto_pacto" name="id_producto" required>
                                <option value="">Seleccione un producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= $producto->getIdProducto() ?>">
                                        <?= htmlspecialchars($producto->getCodigo()) ?> - <?= htmlspecialchars($producto->getNombre()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_tipo_ubicacion" class="form-label">Tipo de Ubicación <span class="text-danger">*</span></label>
                            <select class="form-select" id="editar_tipo_ubicacion" name="tipo_ubicacion" required>
                                <option value="">Seleccione un tipo de ubicación</option>
                                <?php foreach ($tiposUbicacion as $key => $value): ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_destino_pacto" class="form-label">Destino <span class="text-danger">*</span></label>
                            <select class="form-select" id="editar_destino_pacto" name="id_destino" required>
                                <option value="">Primero seleccione un tipo de ubicación</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_cantidad_pactada" class="form-label">Cantidad Pactada <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editar_cantidad_pactada" name="cantidad_pactada" min="1" required>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="cancelar_edicion_pacto" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="editar_pacto_placeholder" class="text-center text-muted py-4">
                    <i class="fas fa-handshake fa-3x mb-3"></i>
                    <p>Seleccione un pacto para editarlo</p>
                </div>
            </div>
        </div>
    </div>
</div>


