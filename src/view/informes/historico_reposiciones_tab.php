<?php
if(!isset($reposiciones) || !isset($productos) || !isset($almacenes) || !isset($informesController)) {
    die('No se puede acceder directamente a este archivo.');
}
?>

<div class="card-container">
    <!-- Filtros de histórico de reposiciones -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Histórico de Reposiciones</h5>
        </div>
        <div class="card-body">
            <form id="filtro-reposiciones-form" class="row g-3">
                <div class="col-md-3">
                    <label for="filtro-fecha-desde-repo" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="filtro-fecha-desde-repo" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label for="filtro-fecha-hasta-repo" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="filtro-fecha-hasta-repo" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-3">
                    <label for="filtro-producto" class="form-label">Producto</label>
                    <select class="form-select" id="filtro-producto">
                        <option value="">Todos los productos</option>
                        <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto->getIdProducto() ?>"><?= htmlspecialchars($producto->getNombre()) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro-urgencia" class="form-label">Urgencia</label>
                    <select class="form-select" id="filtro-urgencia">
                        <option value="">Todas</option>
                        <option value="1">Urgentes</option>
                        <option value="0">Normales</option>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <button type="button" id="btn-aplicar-filtro-repo" class="btn btn-primary">
                        <i class="fas fa-search"></i> Aplicar Filtros
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Limpiar
                    </button>
                    <button type="button" id="btn-exportar-repo" class="btn btn-success float-end">
                        <i class="fas fa-file-export"></i> Exportar a Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Gráfico de reposiciones por mes -->
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line"></i> Reposiciones por Período</h5>
        </div>
        <div class="card-body">
            <canvas id="grafico-reposiciones-periodo" height="250"></canvas>
            <div class="text-center mt-3" id="sin-datos-repo" style="display: none;">
                <p class="text-muted">No hay datos disponibles para el período seleccionado</p>
            </div>
        </div>
    </div>

    <!-- Tabla de histórico de reposiciones -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-table"></i> Histórico de Reposiciones</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tabla-reposiciones">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Desde Almacén</th>
                            <th>Hasta Botiquín</th>
                            <th>Cantidad</th>
                            <th>Urgencia</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-reposiciones-body">
                        <tr>
                            <td colspan="7" class="text-center">Cargando datos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let graficoReposiciones = null;
    
    // Inicializar DataTable
    const tablaReposiciones = $('#tabla-reposiciones').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        responsive: true,
        order: [[1, 'desc']],
        pageLength: 10
    });
    
    // Función para actualizar la tabla con los datos
    function actualizarTablaReposiciones(datos) {
        tablaReposiciones.clear();
        
        if (datos.length === 0) {
            tablaReposiciones.row.add(['No hay datos disponibles', '', '', '', '', '', '']).draw();
        } else {
            datos.forEach(item => {
                tablaReposiciones.row.add([
                    item.id,
                    formatearFecha(item.fecha),
                    item.producto,
                    item.desde_almacen,
                    item.hasta_botiquin,
                    item.cantidad,
                    `<span class="badge bg-${item.urgente ? 'danger' : 'primary'}">${item.urgente ? 'Urgente' : 'Normal'}</span>`
                ]).draw(false);
            });
        }
        
        tablaReposiciones.columns.adjust().draw();
    }
    
    // Función para actualizar el gráfico de reposiciones
    function actualizarGraficoReposiciones(datos) {
        const ctx = document.getElementById('grafico-reposiciones-periodo').getContext('2d');
        
        if (graficoReposiciones) {
            graficoReposiciones.destroy();
        }
        
        if (!datos || datos.periodos.length === 0) {
            document.getElementById('sin-datos-repo').style.display = 'block';
            return;
        } else {
            document.getElementById('sin-datos-repo').style.display = 'none';
        }
        
        graficoReposiciones = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datos.periodos,
                datasets: [
                    {
                        label: 'Total reposiciones',
                        data: datos.totales,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'Urgentes',
                        data: datos.urgentes,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'Normales',
                        data: datos.normales,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        }
                    }
                }
            }
        });
    }
    
    // Manejar el botón de aplicar filtros
    document.getElementById('btn-aplicar-filtro-repo').addEventListener('click', function() {
        cargarDatosHistoricoReposiciones();
    });
    
    // Manejar el botón de exportar
    document.getElementById('btn-exportar-repo').addEventListener('click', function() {
        const fechaDesde = document.getElementById('filtro-fecha-desde-repo').value;
        const fechaHasta = document.getElementById('filtro-fecha-hasta-repo').value;
        const idProducto = document.getElementById('filtro-producto').value;
        const urgencia = document.getElementById('filtro-urgencia').value;
        
        let url = '?export=reposiciones';
        url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
        
        if (idProducto) {
            url += `&id_producto=${idProducto}`;
        }
        
        if (urgencia !== '') {
            url += `&urgente=${urgencia}`;
        }
        
        window.location.href = url;
    });
    
    // Cargar datos iniciales
    cargarDatosHistoricoReposiciones();
});
</script>

<style>
.card-container {
    margin-bottom: 20px;
}
</style>
