<?php
if(!isset($botiquines) || empty($botiquines)) {
    echo '<div class="alert alert-warning">No se han encontrado botiquines. Por favor, asegúrese de que existen botiquines registrados.</div>';
}
?>

<div class="lecturas-container">
    <div class="row">
        <div class="col-md-12 form-section">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">Registrar nueva lectura de stock</h6>
                </div>
                <div class="card-body">
                    <form id="formRegistrarLectura" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/LecturasStockController.php" method="POST" class="card-form form-lectura">
                        <input type="hidden" name="action" value="store">
                        <input type="hidden" name="registrado_por" value="<?= $_SESSION['user_id'] ?? 0 ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="id_botiquin" class="form-label">Botiquín: <span class="text-danger">*</span></label>
                                    <select id="id_botiquin" name="id_botiquin" class="form-select" required <?= empty($botiquines) ? 'disabled' : '' ?>>
                                        <option value="">Seleccione un botiquín</option>
                                        <?php if(isset($botiquines) && !empty($botiquines)): ?>
                                            <?php foreach ($botiquines as $botiquin): ?>
                                                <option value="<?= $botiquin->getIdBotiquin() ?>">
                                                    <?= htmlspecialchars($botiquin->getNombre()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <?php if(empty($botiquines)): ?>
                                        <small class="text-danger">No hay botiquines disponibles</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="id_producto" class="form-label">Producto: <span class="text-danger">*</span></label>
                                    <select id="id_producto" name="id_producto" class="form-select" required disabled>
                                        <option value="">Seleccione un producto</option>
                                        <!-- Se cargará dinámicamente con JavaScript -->
                                    </select>
                                    <div class="form-text">
                                        <span id="producto-loading" class="text-muted loading-indicator" style="display: none;">
                                            <i class="fas fa-spinner fa-spin spinner-icon"></i> Cargando productos...
                                        </span>
                                        <span id="producto-error" class="text-danger" style="display: none;">
                                            <i class="fas fa-exclamation-circle"></i> Error al cargar productos
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cantidad_disponible" class="form-label">Cantidad disponible: <span class="text-danger">*</span></label>
                                    <input type="number" id="cantidad_disponible" name="cantidad_disponible" class="form-control" min="0" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> Introduce la cantidad actual del producto en el botiquín
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fecha_lectura" class="form-label">Fecha de lectura: <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="fecha_lectura" name="fecha_lectura" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4 text-center">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Registrar lectura
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-broom"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
