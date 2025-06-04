<?php
session_start();
require_once(__DIR__ . '/../../controller/ReposicionesController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\ReposicionesController;
use controller\ProductoController;
use controller\AlmacenesController;
use controller\BotiquinController;
use util\Session;
use util\AuthGuard;

$reposicionesController = new ReposicionesController();
$productoController = new ProductoController();
$almacenesController = new AlmacenesController();
$botiquinController = new BotiquinController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireGestorHospital();

// Procesar formularios si se han enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['action'] ?? '';
    
    if ($accion === 'crear') {
        $resultado = $reposicionesController->procesarFormularioCrear();
        // Redirigir para evitar reenvío de formulario
        header("Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=ver-estado");
        exit;
    } else if ($accion === 'marcar_entregadas') {
        $resultado = $reposicionesController->procesarFormularioMarcarEntregadas();
        // Redirigir para evitar reenvío de formulario
        header("Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=marcar-entregado");
        exit;
    }
}

// Procesar solicitudes AJAX para obtener reposiciones pendientes
if (isset($_GET['ajax']) && $_GET['ajax'] === 'getPendientes') {
    header('Content-Type: application/json');
    $idBotiquin = isset($_GET['botiquin']) && !empty($_GET['botiquin']) ? (int)$_GET['botiquin'] : null;
    $urgente = isset($_GET['urgente']) ? (int)$_GET['urgente'] === 1 : null;
    
    $resultado = $reposicionesController->getReposicionesPendientes($idBotiquin, $urgente);
    echo json_encode($resultado);
    exit;
}

// Cargar datos para las vistas
$reposiciones = $reposicionesController->index()['reposiciones'] ?? [];
$productos = $productoController->index()['productos'] ?? [];
$almacenes = $almacenesController->index() ?? [];
$botiquines = $botiquinController->index()['botiquines'] ?? [];

// Determinar la pestaña activa
$activeTab = 'tab-ver-estado';
if (isset($_GET['tab'])) {
    switch ($_GET['tab']) {
        case 'generar-reposicion':
            $activeTab = 'tab-generar-reposicion';
            break;
        case 'marcar-entregado':
            $activeTab = 'tab-marcar-entregado';
            break;
        default:
            $activeTab = 'tab-ver-estado';
    }
}

$pageTitle = "Reposiciones";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/reposiciones.css">

<!-- jQuery y DataTables primero para evitar problemas de carga -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap JS para modales -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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
            <button class="tab-btn <?= $activeTab === 'tab-ver-estado' ? 'active' : '' ?>" data-tab="tab-ver-estado">Ver Estado</button>
            <button class="tab-btn <?= $activeTab === 'tab-generar-reposicion' ? 'active' : '' ?>" data-tab="tab-generar-reposicion">Generar Reposición</button>
            <button class="tab-btn <?= $activeTab === 'tab-marcar-entregado' ? 'active' : '' ?>" data-tab="tab-marcar-entregado">Marcar como Entregado</button>
        </div>

        <div class="tab-content">
            <div id="tab-ver-estado" class="tab-pane <?= $activeTab === 'tab-ver-estado' ? 'active' : '' ?>">
                <?php include_once(__DIR__ . '/ver_estado_tab.php'); ?>
            </div>
            <div id="tab-generar-reposicion" class="tab-pane <?= $activeTab === 'tab-generar-reposicion' ? 'active' : '' ?>">
                <?php include_once(__DIR__ . '/generar_reposicion_tab.php'); ?>
            </div>
            <div id="tab-marcar-entregado" class="tab-pane <?= $activeTab === 'tab-marcar-entregado' ? 'active' : '' ?>">
                <?php include_once(__DIR__ . '/marcar_entregado_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="reposicion-overlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Actualizar URL con el parámetro tab
            const tabName = tabId.replace('tab-', '');
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            history.pushState({}, '', url);
            
            // Activar tab
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Cerrar alertas
    document.querySelectorAll('.list-alert__close').forEach(function(closeBtn) {
        closeBtn.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
    
    // Funcionalidades para la pestaña "Generar Reposición"
    const productoSelect = document.getElementById('id_producto');
    const unidadSpan = document.getElementById('unidad-medida');
    const productoInfo = document.getElementById('product-info');
    const productDetails = document.querySelector('.product-details');
    const noProductSelected = document.querySelector('.no-product-selected');
    
    if (productoSelect && unidadSpan) {
        productoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const unidad = selectedOption.dataset.unidad || 'unidades';
            unidadSpan.textContent = unidad;
            
            // Mostrar detalles del producto si está seleccionado
            if (this.value && productoInfo) {
                if (productDetails) productDetails.style.display = 'grid';
                if (noProductSelected) noProductSelected.style.display = 'none';
                
                document.getElementById('producto-codigo').textContent = selectedOption.textContent.split(' - ')[0];
                document.getElementById('producto-nombre').textContent = selectedOption.textContent.split(' - ')[1];
                document.getElementById('producto-unidad').textContent = unidad;
            } else {
                if (productDetails) productDetails.style.display = 'none';
                if (noProductSelected) noProductSelected.style.display = 'block';
            }
        });
    }
    
    // Manejar el formulario de generación de reposición
    const nuevaReposicionForm = document.getElementById('nueva-reposicion-form');
    if (nuevaReposicionForm) {
        nuevaReposicionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar formulario
            const producto = document.getElementById('id_producto').value;
            const desdeAlmacen = document.getElementById('desde_almacen').value;
            const hastaBotiquin = document.getElementById('hacia_botiquin').value;
            const cantidad = document.getElementById('cantidad_repuesta').value;
            
            if (!producto || !desdeAlmacen || !hastaBotiquin || !cantidad || cantidad <= 0) {
                alert('Por favor complete todos los campos obligatorios correctamente.');
                return false;
            }
            
            // Mostrar confirmación
            const confirmarModal = document.getElementById('confirmacion-modal');
            if (confirmarModal) {
                // Llenar datos de confirmación
                document.getElementById('confirm-producto').textContent = 
                    document.getElementById('id_producto').options[document.getElementById('id_producto').selectedIndex].text;
                document.getElementById('confirm-almacen').textContent = 
                    document.getElementById('desde_almacen').options[document.getElementById('desde_almacen').selectedIndex].text;
                document.getElementById('confirm-botiquin').textContent = 
                    document.getElementById('hacia_botiquin').options[document.getElementById('hacia_botiquin').selectedIndex].text;
                document.getElementById('confirm-cantidad').textContent = `${cantidad} ${document.getElementById('unidad-medida').textContent}`;
                document.getElementById('confirm-fecha').textContent = 
                    document.getElementById('fecha').value || 'Fecha actual';
                document.getElementById('confirm-urgencia').textContent = 
                    document.getElementById('urgente').checked ? 'URGENTE' : 'Normal';
                
                // Agregar campo action
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'crear';
                nuevaReposicionForm.appendChild(actionInput);
                
                // Mostrar modal
                confirmarModal.style.display = 'flex';
                
                // Manejar confirmación
                document.getElementById('confirmar-reposicion').addEventListener('click', function() {
                    nuevaReposicionForm.submit();
                    confirmarModal.style.display = 'none';
                });
                
                // Cerrar modal
                document.querySelectorAll('.confirmacion-modal-close').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        confirmarModal.style.display = 'none';
                    });
                });
            } else {
                // Si no hay modal, enviar formulario directamente
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'crear';
                nuevaReposicionForm.appendChild(actionInput);
                nuevaReposicionForm.submit();
            }
        });
    }
    
    // Funcionalidades para la pestaña "Marcar como Entregado"
    const btnBuscarPendientes = document.getElementById('buscar-pendientes');
    const filtroBotiquin = document.getElementById('filtro-botiquin-entrega');
    const filtroUrgencia = document.getElementById('filtro-urgencia-entrega');
    const pendientesTbody = document.getElementById('pendientes-tbody');
    const entregaActions = document.getElementById('entrega-actions');
    const selectTodas = document.getElementById('seleccionar-todas');
    const formEntregas = document.getElementById('form-entregas');
    
    if (btnBuscarPendientes) {
        btnBuscarPendientes.addEventListener('click', function() {
            // Mostrar indicador de carga
            pendientesTbody.innerHTML = '<tr><td colspan="8" class="text-center">Cargando reposiciones pendientes...</td></tr>';
            
            // Construir la URL para la petición
            const botiquinId = filtroBotiquin.value;
            const urgencia = filtroUrgencia.value;
            let url = '?ajax=getPendientes';
            
            if (botiquinId) {
                url += `&botiquin=${botiquinId}`;
            }
            
            if (urgencia !== '') {
                url += `&urgente=${urgencia}`;
            }
            
            // Realizar la petición
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        pendientesTbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${data.mensaje}</td></tr>`;
                    } else if (!data.reposiciones || data.reposiciones.length === 0) {
                        pendientesTbody.innerHTML = '<tr><td colspan="8" class="text-center">No se encontraron reposiciones pendientes con los filtros seleccionados</td></tr>';
                        entregaActions.style.display = 'none';
                    } else {
                        // Generar filas de la tabla
                        pendientesTbody.innerHTML = '';
                        data.reposiciones.forEach(repo => {
                            const row = document.createElement('tr');
                            const urgenciaClass = repo.isUrgente ? 'badge bg-danger' : 'badge bg-primary';
                            const urgenciaText = repo.isUrgente ? 'URGENTE' : 'Normal';
                            
                            // Buscar nombre del producto
                            let nombreProducto = "Producto no encontrado";
                            let nombreAlmacen = "Almacén no encontrado";
                            let nombreBotiquin = "Botiquín no encontrado";
                            
                            // Esta lógica debería implementarse adecuadamente en el servidor
                            // Pero por simplicidad, utilizamos IDs en la visualización
                            
                            row.innerHTML = `
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input checkbox-reposicion" type="checkbox" name="ids_reposicion[]" value="${repo.id}">
                                    </div>
                                </td>
                                <td>${repo.id}</td>
                                <td>Producto ${repo.getIdProducto}</td>
                                <td>Almacén ${repo.getDesdeAlmacen}</td>
                                <td>Botiquín ${repo.getHaciaBotiquin}</td>
                                <td>${repo.getCantidadRepuesta}</td>
                                <td>${formatDate(repo.getFecha)}</td>
                                <td><span class="${urgenciaClass}">${urgenciaText}</span></td>
                            `;
                            
                            pendientesTbody.appendChild(row);
                        });
                        
                        // Mostrar acciones de entrega
                        entregaActions.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    pendientesTbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error al cargar las reposiciones pendientes</td></tr>';
                });
        });
        
        // Manejar selección de todas las reposiciones
        if (selectTodas) {
            selectTodas.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.checkbox-reposicion');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
        
        // Formato de fecha
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
});
</script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
