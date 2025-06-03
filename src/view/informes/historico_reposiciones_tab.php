<?php
if(!isset($reposiciones) || !isset($productos) || !isset($almacenes)) {
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
    
    // Función para cargar datos de reposiciones
    function cargarDatosReposiciones() {
        const fechaDesde = document.getElementById('filtro-fecha-desde-repo').value;
        const fechaHasta = document.getElementById('filtro-fecha-hasta-repo').value;
        const idProducto = document.getElementById('filtro-producto').value;
        const urgencia = document.getElementById('filtro-urgencia').value;
        
        // URL para la API que obtendría los datos
        let url = '/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/api/informes_api.php?accion=reposiciones';
        url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
        
        if (idProducto) {
            url += `&id_producto=${idProducto}`;
        }
        
        if (urgencia !== '') {
            url += `&urgente=${urgencia}`;
        }
        
        // Mostrar mensaje de carga
        document.getElementById('tabla-reposiciones-body').innerHTML = '<tr><td colspan="7" class="text-center">Cargando datos...</td></tr>';
        
        // En una implementación real, aquí se haría la petición a la API
        // Por ahora, simulamos datos
        const datosSimulados = generarDatosReposicionesSimulados();
        setTimeout(() => {
            actualizarTablaReposiciones(datosSimulados);
            actualizarGraficoReposiciones(datosSimulados);
        }, 500);
    }
    
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
        // Agrupar datos por mes o por día según el rango de fechas
        const datosProcesados = procesarDatosParaGraficoReposiciones(datos);
        
        const ctx = document.getElementById('grafico-reposiciones-periodo').getContext('2d');
        
        if (graficoReposiciones) {
            graficoReposiciones.destroy();
        }
        
        if (datosProcesados.labels.length === 0) {
            document.getElementById('sin-datos-repo').style.display = 'block';
            return;
        } else {
            document.getElementById('sin-datos-repo').style.display = 'none';
        }
        
        graficoReposiciones = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datosProcesados.labels,
                datasets: [
                    {
                        label: 'Cantidad reposiciones',
                        data: datosProcesados.valores,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'Urgentes',
                        data: datosProcesados.urgentes,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
    
    // Función para procesar datos para el gráfico de reposiciones
    function procesarDatosParaGraficoReposiciones(datos) {
        const fechas = {};
        
        datos.forEach(item => {
            const fecha = item.fecha.substring(0, 7); // Formato YYYY-MM para agrupar por mes
            
            if (!fechas[fecha]) {
                fechas[fecha] = { total: 0, urgentes: 0 };
            }
            
            fechas[fecha].total++;
            if (item.urgente) {
                fechas[fecha].urgentes++;
            }
        });
        
        // Convertir a arrays para el gráfico
        const labels = Object.keys(fechas).sort();
        const valores = labels.map(fecha => fechas[fecha].total);
        const urgentes = labels.map(fecha => fechas[fecha].urgentes);
        
        // Formatear las etiquetas de fecha a formato legible
        const labelsFormateados = labels.map(fecha => {
            const [year, month] = fecha.split('-');
            const nombresMes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            return nombresMes[parseInt(month) - 1] + ' ' + year;
        });
        
        return {
            labels: labelsFormateados,
            valores: valores,
            urgentes: urgentes
        };
    }
    
    // Función para formatear fecha en formato DD/MM/YYYY
    function formatearFecha(fecha) {
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES');
    }
    
    // Función para generar datos simulados de reposiciones (solo para demostración)
    function generarDatosReposicionesSimulados() {
        const productos = ['Guantes', 'Mascarillas', 'Vendas', 'Jeringas', 'Gasas'];
        const almacenes = ['Almacén Central', 'Almacén Planta 1', 'Almacén Urgencias'];
        const botiquines = ['Botiquín Planta 1', 'Botiquín Planta 2', 'Botiquín Urgencias', 'Botiquín UCI'];
        const datos = [];
        
        // Generar entre 20 y 40 registros aleatorios
        const numRegistros = Math.floor(Math.random() * 21) + 20;
        
        for (let i = 0; i < numRegistros; i++) {
            const fecha = new Date();
            fecha.setDate(fecha.getDate() - Math.floor(Math.random() * 90)); // Fecha aleatoria en los últimos 90 días
            
            datos.push({
                id: i + 1,
                fecha: fecha.toISOString().split('T')[0],
                producto: productos[Math.floor(Math.random() * productos.length)],
                desde_almacen: almacenes[Math.floor(Math.random() * almacenes.length)],
                hasta_botiquin: botiquines[Math.floor(Math.random() * botiquines.length)],
                cantidad: Math.floor(Math.random() * 100) + 1,
                urgente: Math.random() > 0.7 // 30% de probabilidad de ser urgente
            });
        }
        
        // Ordenar por fecha (descendente)
        datos.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
        
        return datos;
    }
    
    // Manejar el botón de aplicar filtros
    document.getElementById('btn-aplicar-filtro-repo').addEventListener('click', function() {
        cargarDatosReposiciones();
    });
    
    // Manejar el botón de exportar
    document.getElementById('btn-exportar-repo').addEventListener('click', function() {
        // Aquí iría la lógica para exportar a Excel
        alert('Función de exportación a Excel (pendiente de implementar)');
    });
    
    // Cargar datos iniciales
    cargarDatosReposiciones();
});
</script>

<style>
.card-container {
    margin-bottom: 20px;
}
</style>
