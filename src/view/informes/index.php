<?php
session_start();
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/ReposicionesController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');
require_once(__DIR__ . '/../../controller/InformesController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\AlmacenesController;
use controller\PlantaController;
use controller\ReposicionesController;
use controller\ProductoController;
use controller\InformesController;
use util\Session;
use util\AuthGuard;

$almacenesController = new AlmacenesController();
$plantaController = new PlantaController();
$reposicionesController = new ReposicionesController();
$productoController = new ProductoController();
$informesController = new InformesController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireGestorHospital();

// Obtener datos necesarios para los informes
$almacenes = $almacenesController->index() ?? [];
$plantas = $plantaController->index()['plantas'] ?? [];
$reposiciones = $reposicionesController->index()['reposiciones'] ?? [];
$productos = $productoController->index()['productos'] ?? [];

// Inicializar datos de informes (se cargarán por AJAX)
$datosActividad = [];
$datosReposiciones = [];

$pageTitle = "Informes";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/informes.css">

<!-- jQuery y DataTables primero para evitar problemas de carga -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap JS para modales -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

<div class="list-container">

    <?php if ($session->hasMessage('success')): ?>
        <div class="list-alert list-alert--success">
            <p class="list-alert__message"><?= $session->getMessage('success') ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
        <?php $session->clearMessage('success'); ?>
    <?php endif; ?>

    <?php if ($session->hasMessage('error')): ?>
        <div class="list-alert list-alert--error">
            <p class="list-alert__message"><?= $session->getMessage('error') ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
        <?php $session->clearMessage('error'); ?>
    <?php endif; ?>

    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="tab-actividad">Actividad Almacén y Planta</button>
            <button class="tab-btn" data-tab="tab-historico-reposiciones">Histórico Reposiciones</button>
            <button class="tab-btn" data-tab="tab-informe-producto">Informe por Producto</button>
            <button class="tab-btn" data-tab="tab-exportar">Exportar a CSV/Excel</button>
        </div>

        <div class="tab-content">
            <div id="tab-actividad" class="tab-pane active">
                <?php include_once(__DIR__ . '/actividad_tab.php'); ?>
            </div>

            <div id="tab-historico-reposiciones" class="tab-pane">
                <?php include_once(__DIR__ . '/historico_reposiciones_tab.php'); ?>
            </div>
            
            <div id="tab-informe-producto" class="tab-pane">
                <?php include_once(__DIR__ . '/informe_producto_tab.php'); ?>
            </div>
            
            <div id="tab-exportar" class="tab-pane">
                <?php include_once(__DIR__ . '/exportar_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="overlay"></div>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener pestaña activa desde la URL si existe
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    
    // Activar pestaña según parámetro de URL o usar la predeterminada
    if (tabParam) {
        const tabId = 'tab-' + tabParam;
        const tabBtn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
        if (tabBtn) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            
            tabBtn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }
    }
    
    // Cerrar alertas
    document.querySelectorAll('.list-alert__close').forEach(function(closeBtn) {
        closeBtn.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
    
    // Manejar cambio de pestañas
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Actualizar URL con el parámetro tab
            const tabName = tabId.replace('tab-', '');
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            history.pushState({}, '', url);
        });
    });
});

// Función para cargar datos de actividad
function cargarDatosActividad() {
    const fechaDesde = document.getElementById('filtro-fecha-desde').value;
    const fechaHasta = document.getElementById('filtro-fecha-hasta').value;
    const idAlmacen = document.getElementById('filtro-almacen').value;
    const idPlanta = document.getElementById('filtro-planta').value;
    
    // Construir URL para la solicitud
    let url = '?tab=actividad';
    url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
    
    if (idAlmacen) {
        url += `&id_almacen=${idAlmacen}`;
    }
    
    if (idPlanta) {
        url += `&id_planta=${idPlanta}`;
    }
    
    // Mostrar mensaje de carga
    document.getElementById('tabla-actividad-body').innerHTML = '<tr><td colspan="6" class="text-center">Cargando datos...</td></tr>';
    
    // Realizar la petición
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('tabla-actividad-body').innerHTML = `<tr><td colspan="6" class="text-center text-danger">${data.error}</td></tr>`;
                return;
            }
            
            actualizarTablaActividad(data.movimientos);
            actualizarGraficosActividad(data.resumen_almacenes, data.resumen_plantas);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('tabla-actividad-body').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar los datos</td></tr>';
        });
}

// Función para cargar datos del histórico de reposiciones
function cargarDatosHistoricoReposiciones() {
    const fechaDesde = document.getElementById('filtro-fecha-desde-repo').value;
    const fechaHasta = document.getElementById('filtro-fecha-hasta-repo').value;
    const idProducto = document.getElementById('filtro-producto').value;
    const urgencia = document.getElementById('filtro-urgencia').value;
    
    // Construir URL para la solicitud
    let url = '?tab=historico';
    url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
    
    if (idProducto) {
        url += `&id_producto=${idProducto}`;
    }
    
    if (urgencia !== '') {
        url += `&urgente=${urgencia}`;
    }
    
    // Mostrar mensaje de carga
    document.getElementById('tabla-reposiciones-body').innerHTML = '<tr><td colspan="7" class="text-center">Cargando datos...</td></tr>';
    
    // Realizar la petición
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('tabla-reposiciones-body').innerHTML = `<tr><td colspan="7" class="text-center text-danger">${data.error}</td></tr>`;
                return;
            }
            
            actualizarTablaReposiciones(data.reposiciones);
            actualizarGraficoReposiciones(data.estadisticas_periodo);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('tabla-reposiciones-body').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error al cargar los datos</td></tr>';
        });
}

// Función para cargar datos del informe por producto
function cargarDatosInformeProducto() {
    const idProducto = document.getElementById('filtro-producto-informe').value;
    const fechaDesde = document.getElementById('filtro-fecha-desde-informe').value;
    const fechaHasta = document.getElementById('filtro-fecha-hasta-informe').value;
    
    if (!idProducto) {
        alert('Debe seleccionar un producto para generar el informe');
        return;
    }
    
    // Construir URL para la solicitud
    let url = '?tab=informe-producto';
    url += `&id_producto=${idProducto}`;
    url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
    
    // Mostrar mensaje de carga
    document.getElementById('tabla-informe-producto-body').innerHTML = '<tr><td colspan="6" class="text-center">Cargando datos...</td></tr>';
    
    // Realizar la petición
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('tabla-informe-producto-body').innerHTML = `<tr><td colspan="6" class="text-center text-danger">${data.error}</td></tr>`;
                return;
            }
            
            actualizarTablaInformeProducto(data.movimientos);
            actualizarGraficoInformeProducto(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('tabla-informe-producto-body').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar los datos</td></tr>';
        });
}

// Función para formatear fecha en formato DD/MM/YYYY
function formatearFecha(fecha) {
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES');
}

// Actualizar tabla de informe por producto
function actualizarTablaInformeProducto(datos) {
    const tbody = document.getElementById('tabla-informe-producto-body');
    tbody.innerHTML = '';
    
    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay datos disponibles</td></tr>';
        return;
    }
    
    datos.forEach(item => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${formatearFecha(item.fecha)}</td>
            <td>${item.almacen}</td>
            <td>${item.planta || 'N/A'}</td>
            <td class="text-right">${item.cantidad_entrada || 0}</td>
            <td class="text-right">${item.cantidad_salida || 0}</td>
            <td>${item.tipo}</td>
        `;
        tbody.appendChild(fila);
    });
}

// Actualizar gráfico de informe por producto
function actualizarGraficoInformeProducto(data) {
    const nombreProducto = data.nombre_producto;
    const estadisticas = data.estadisticas_mensuales;
    
    document.getElementById('producto-nombre').textContent = nombreProducto;
    document.getElementById('producto-total-salidas').textContent = data.total_salidas;
    document.getElementById('producto-total-entradas').textContent = data.total_entradas;
    document.getElementById('producto-stock-actual').textContent = data.stock_actual;
    
    // Configurar y mostrar gráfico
    const ctx = document.getElementById('grafico-producto').getContext('2d');
    
    if (window.graficoProducto) {
        window.graficoProducto.destroy();
    }
    
    window.graficoProducto = new Chart(ctx, {
        type: 'line',
        data: {
            labels: estadisticas.periodos,
            datasets: [
                {
                    label: 'Entradas',
                    data: estadisticas.entradas,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.2
                },
                {
                    label: 'Salidas',
                    data: estadisticas.salidas,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    tension: 0.2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: `Movimientos de ${nombreProducto} por período`
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
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
</script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
