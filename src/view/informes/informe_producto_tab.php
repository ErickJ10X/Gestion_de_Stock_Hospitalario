<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">Filtros de Informe por Producto</h5>
    </div>
    <div class="card-body">
        <form id="form-filtro-producto" class="row g-3">
            <div class="col-md-5">
                <label for="filtro-producto-informe" class="form-label">Producto</label>
                <select id="filtro-producto-informe" name="filtro-producto-informe" class="form-select" required>
                    <option value="">Seleccione un producto...</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto->getIdProducto() ?>">
                            <?= htmlspecialchars($producto->getNombre()) ?> - <?= htmlspecialchars($producto->getCodigo()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtro-fecha-desde-informe" class="form-label">Fecha desde</label>
                <input type="date" class="form-control" id="filtro-fecha-desde-informe" name="filtro-fecha-desde-informe" 
                       value="<?= date('Y-m-01') ?>">
            </div>
            <div class="col-md-3">
                <label for="filtro-fecha-hasta-informe" class="form-label">Fecha hasta</label>
                <input type="date" class="form-control" id="filtro-fecha-hasta-informe" name="filtro-fecha-hasta-informe" 
                       value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" onclick="cargarDatosInformeProducto()">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Resumen de Producto: <span id="producto-nombre">-</span></h5>
                <button class="btn btn-sm btn-outline-success" onclick="exportarInformeProductoCSV()">
                    <i class="bi bi-file-earmark-excel"></i> Exportar a CSV
                </button>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Total Salidas</h6>
                                <h3 class="card-title" id="producto-total-salidas">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Total Entradas</h6>
                                <h3 class="card-title" id="producto-total-entradas">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Stock Actual</h6>
                                <h3 class="card-title" id="producto-stock-actual">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <canvas id="grafico-producto" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Movimientos del Producto</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Almacén</th>
                        <th>Planta</th>
                        <th class="text-right">Entrada</th>
                        <th class="text-right">Salida</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody id="tabla-informe-producto-body">
                    <tr>
                        <td colspan="6" class="text-center">Seleccione un producto y aplique los filtros</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function exportarInformeProductoCSV() {
    const idProducto = document.getElementById('filtro-producto-informe').value;
    const fechaDesde = document.getElementById('filtro-fecha-desde-informe').value;
    const fechaHasta = document.getElementById('filtro-fecha-hasta-informe').value;
    
    if (!idProducto) {
        alert('Debe seleccionar un producto para exportar el informe');
        return;
    }
    
    let url = '?export=informe-producto';
    url += `&id_producto=${idProducto}`;
    url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
    
    window.location.href = url;
}
</script>
