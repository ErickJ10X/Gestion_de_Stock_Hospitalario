<?php
if(!isset($almacenes) || !isset($plantas) || !isset($informesController)) {
    die('No se puede acceder directamente a este archivo.');
}
?>

<div class="card-container">
    <!-- Filtros de actividad -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Actividad</h5>
        </div>
        <div class="card-body">
            <form id="filtro-actividad-form" class="row g-3">
                <div class="col-md-3">
                    <label for="filtro-fecha-desde" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="filtro-fecha-desde" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label for="filtro-fecha-hasta" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="filtro-fecha-hasta" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-3">
                    <label for="filtro-almacen" class="form-label">Almacén</label>
                    <select class="form-select" id="filtro-almacen">
                        <option value="">Todos los almacenes</option>
                        <?php foreach ($almacenes as $almacen): ?>
                        <option value="<?= $almacen->getId() ?>"><?= htmlspecialchars($almacen->getTipo()) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro-planta" class="form-label">Planta</label>
                    <select class="form-select" id="filtro-planta">
                        <option value="">Todas las plantas</option>
                        <?php foreach ($plantas as $planta): ?>
                        <option value="<?= $planta->getIdPlanta() ?>"><?= htmlspecialchars($planta->getNombre()) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <button type="button" id="btn-aplicar-filtro-actividad" class="btn btn-primary">
                        <i class="fas fa-search"></i> Aplicar Filtros
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Limpiar
                    </button>
                    <button type="button" id="btn-exportar-actividad" class="btn btn-success float-end">
                        <i class="fas fa-file-export"></i> Exportar a Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Gráficos de actividad -->
    <div class="row mb-4">
        <!-- Gráfico de actividad por almacén -->
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Actividad por Almacén</h5>
                </div>
                <div class="card-body">
                    <canvas id="grafico-actividad-almacen" height="250"></canvas>
                    <div class="text-center mt-3" id="sin-datos-almacen" style="display: none;">
                        <p class="text-muted">No hay datos disponibles para el período seleccionado</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráfico de actividad por planta -->
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Actividad por Planta</h5>
                </div>
                <div class="card-body">
                    <canvas id="grafico-actividad-planta" height="250"></canvas>
                    <div class="text-center mt-3" id="sin-datos-planta" style="display: none;">
                        <p class="text-muted">No hay datos disponibles para el período seleccionado</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de actividad -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-table"></i> Detalle de Actividad</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tabla-actividad">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Almacén</th>
                            <th>Planta</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-actividad-body">
                        <tr>
                            <td colspan="6" class="text-center">Cargando datos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let graficoPorAlmacen = null;
    let graficoPorPlanta = null;
    
    // Inicializar DataTable
    const tablaActividad = $('#tabla-actividad').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10
    });
    
    // Función para actualizar la tabla con los datos
    function actualizarTablaActividad(datos) {
        tablaActividad.clear();
        
        if (datos.length === 0) {
            tablaActividad.row.add(['No hay datos disponibles', '', '', '', '', '']).draw();
        } else {
            datos.forEach(item => {
                tablaActividad.row.add([
                    formatearFecha(item.fecha),
                    item.almacen,
                    item.planta,
                    item.producto,
                    item.cantidad_entrada > 0 ? item.cantidad_entrada : item.cantidad_salida,
                    `<span class="badge bg-${item.tipo === 'Entrada' ? 'success' : 'warning'}">${item.tipo}</span>`
                ]).draw(false);
            });
        }
        
        tablaActividad.columns.adjust().draw();
    }
    
    // Función para actualizar los gráficos
    function actualizarGraficosActividad(datosAlmacen, datosPlanta) {
        actualizarGraficoAlmacen(datosAlmacen);
        actualizarGraficoPlanta(datosPlanta);
    }
    
    // Función para actualizar el gráfico de almacén
    function actualizarGraficoAlmacen(datos) {
        const ctx = document.getElementById('grafico-actividad-almacen').getContext('2d');
        const labels = Object.keys(datos);
        const datosEntrada = labels.map(almacen => datos[almacen].entradas);
        const datosSalida = labels.map(almacen => datos[almacen].salidas);
        
        if (graficoPorAlmacen) {
            graficoPorAlmacen.destroy();
        }
        
        if (labels.length === 0) {
            document.getElementById('sin-datos-almacen').style.display = 'block';
            return;
        } else {
            document.getElementById('sin-datos-almacen').style.display = 'none';
        }
        
        graficoPorAlmacen = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Entradas',
                        data: datosEntrada,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Salidas',
                        data: datosSalida,
                        backgroundColor: 'rgba(255, 159, 64, 0.7)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
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
    
    // Función para actualizar el gráfico de planta
    function actualizarGraficoPlanta(datos) {
        const ctx = document.getElementById('grafico-actividad-planta').getContext('2d');
        const labels = Object.keys(datos);
        const datosEntrada = labels.map(planta => datos[planta].entradas);
        const datosSalida = labels.map(planta => datos[planta].salidas);
        
        if (graficoPorPlanta) {
            graficoPorPlanta.destroy();
        }
        
        if (labels.length === 0) {
            document.getElementById('sin-datos-planta').style.display = 'block';
            return;
        } else {
            document.getElementById('sin-datos-planta').style.display = 'none';
        }
        
        graficoPorPlanta = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Entradas',
                        data: datosEntrada,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Salidas',
                        data: datosSalida,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
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
    document.getElementById('btn-aplicar-filtro-actividad').addEventListener('click', function() {
        cargarDatosActividad();
    });
    
    // Manejar el botón de exportar
    document.getElementById('btn-exportar-actividad').addEventListener('click', function() {
        const fechaDesde = document.getElementById('filtro-fecha-desde').value;
        const fechaHasta = document.getElementById('filtro-fecha-hasta').value;
        const idAlmacen = document.getElementById('filtro-almacen').value;
        const idPlanta = document.getElementById('filtro-planta').value;
        
        let url = '?export=actividad';
        url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
        
        if (idAlmacen) {
            url += `&id_almacen=${idAlmacen}`;
        }
        
        if (idPlanta) {
            url += `&id_planta=${idPlanta}`;
        }
        
        window.location.href = url;
    });
    
    // Cargar datos iniciales
    cargarDatosActividad();
});
</script>

<style>
.card-container {
    margin-bottom: 20px;
}
</style>
