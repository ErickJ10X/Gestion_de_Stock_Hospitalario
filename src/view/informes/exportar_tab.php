<?php
if (!isset($almacenes) || !isset($plantas) || !isset($productos)) {
    die('No se han cargado los datos necesarios para la exportación.');
}
?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">Exportar Datos a CSV/Excel</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">
            Seleccione el tipo de informe que desea exportar y configure los filtros necesarios.
            Los archivos se generarán en formato CSV compatible con Excel.
        </p>
        
        <div class="accordion" id="exportOptionsAccordion">
            <!-- Opción 1: Exportar Actividad -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingActividad">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseActividad" aria-expanded="true" aria-controls="collapseActividad">
                        Exportar Actividad de Almacenes y Plantas
                    </button>
                </h2>
                <div id="collapseActividad" class="accordion-collapse collapse show" aria-labelledby="headingActividad" data-bs-parent="#exportOptionsAccordion">
                    <div class="accordion-body">
                        <form id="form-export-actividad" class="row g-3">
                            <div class="col-md-3">
                                <label for="export-fecha-desde-act" class="form-label">Fecha desde</label>
                                <input type="date" class="form-control" id="export-fecha-desde-act" name="fecha_desde" 
                                    value="<?= date('Y-m-01') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="export-fecha-hasta-act" class="form-label">Fecha hasta</label>
                                <input type="date" class="form-control" id="export-fecha-hasta-act" name="fecha_hasta" 
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="export-almacen" class="form-label">Almacén</label>
                                <select id="export-almacen" name="id_almacen" class="form-select">
                                    <option value="">Todos los almacenes</option>
                                    <?php foreach ($almacenes as $almacen): ?>
                                        <option value="<?= $almacen->getIdAlmacen() ?>">
                                            <?= htmlspecialchars($almacen->getTipo()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="export-planta" class="form-label">Planta</label>
                                <select id="export-planta" name="id_planta" class="form-select">
                                    <option value="">Todas las plantas</option>
                                    <?php foreach ($plantas as $planta): ?>
                                        <option value="<?= $planta->getIdPlanta() ?>">
                                            <?= htmlspecialchars($planta->getNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-success" onclick="generarExportacionActividad()">
                                    <i class="bi bi-file-earmark-excel"></i> Generar CSV de Actividad
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Opción 2: Exportar Histórico de Reposiciones -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingReposiciones">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReposiciones" aria-expanded="false" aria-controls="collapseReposiciones">
                        Exportar Histórico de Reposiciones
                    </button>
                </h2>
                <div id="collapseReposiciones" class="accordion-collapse collapse" aria-labelledby="headingReposiciones" data-bs-parent="#exportOptionsAccordion">
                    <div class="accordion-body">
                        <form id="form-export-reposiciones" class="row g-3">
                            <div class="col-md-3">
                                <label for="export-fecha-desde-repo" class="form-label">Fecha desde</label>
                                <input type="date" class="form-control" id="export-fecha-desde-repo" name="fecha_desde" 
                                    value="<?= date('Y-m-01') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="export-fecha-hasta-repo" class="form-label">Fecha hasta</label>
                                <input type="date" class="form-control" id="export-fecha-hasta-repo" name="fecha_hasta" 
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="export-producto-repo" class="form-label">Producto</label>
                                <select id="export-producto-repo" name="id_producto" class="form-select">
                                    <option value="">Todos los productos</option>
                                    <?php foreach ($productos as $producto): ?>
                                        <option value="<?= $producto->getIdProducto() ?>">
                                            <?= htmlspecialchars($producto->getNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="export-urgencia" class="form-label">Urgencia</label>
                                <select id="export-urgencia" name="urgente" class="form-select">
                                    <option value="">Todas</option>
                                    <option value="1">Solo urgentes</option>
                                    <option value="0">No urgentes</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-success" onclick="generarExportacionReposiciones()">
                                    <i class="bi bi-file-earmark-excel"></i> Generar CSV de Reposiciones
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Opción 3: Exportar Informe por Producto -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingProducto">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProducto" aria-expanded="false" aria-controls="collapseProducto">
                        Exportar Informe por Producto
                    </button>
                </h2>
                <div id="collapseProducto" class="accordion-collapse collapse" aria-labelledby="headingProducto" data-bs-parent="#exportOptionsAccordion">
                    <div class="accordion-body">
                        <form id="form-export-producto" class="row g-3">
                            <div class="col-md-6">
                                <label for="export-producto" class="form-label">Producto</label>
                                <select id="export-producto" name="id_producto" class="form-select" required>
                                    <option value="">Seleccione un producto...</option>
                                    <?php foreach ($productos as $producto): ?>
                                        <option value="<?= $producto->getIdProducto() ?>">
                                            <?= htmlspecialchars($producto->getNombre()) ?> - <?= htmlspecialchars($producto->getCodigo()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="export-fecha-desde-prod" class="form-label">Fecha desde</label>
                                <input type="date" class="form-control" id="export-fecha-desde-prod" name="fecha_desde" 
                                    value="<?= date('Y-m-01') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="export-fecha-hasta-prod" class="form-label">Fecha hasta</label>
                                <input type="date" class="form-control" id="export-fecha-hasta-prod" name="fecha_hasta" 
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-success" onclick="generarExportacionProducto()">
                                    <i class="bi bi-file-earmark-excel"></i> Generar CSV de Producto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Opción 4: Exportación completa para análisis -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingCompleto">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompleto" aria-expanded="false" aria-controls="collapseCompleto">
                        Exportar Dataset Completo para Análisis
                    </button>
                </h2>
                <div id="collapseCompleto" class="accordion-collapse collapse" aria-labelledby="headingCompleto" data-bs-parent="#exportOptionsAccordion">
                    <div class="accordion-body">
                        <form id="form-export-completo" class="row g-3">
                            <div class="col-md-4">
                                <label for="export-fecha-desde-completo" class="form-label">Fecha desde</label>
                                <input type="date" class="form-control" id="export-fecha-desde-completo" name="fecha_desde" 
                                    value="<?= date('Y-01-01') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="export-fecha-hasta-completo" class="form-label">Fecha hasta</label>
                                <input type="date" class="form-control" id="export-fecha-hasta-completo" name="fecha_hasta" 
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="export-formato" class="form-label">Formato de salida</label>
                                <select id="export-formato" name="formato" class="form-select">
                                    <option value="csv">CSV (Compatible con Excel)</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="export-incluir-metadata" name="incluir_metadata" value="1" checked>
                                    <label class="form-check-label" for="export-incluir-metadata">
                                        Incluir metadatos (nombres de almacenes, plantas, productos)
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-success" onclick="generarExportacionCompleta()">
                                    <i class="bi bi-file-earmark-excel"></i> Generar Dataset Completo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Archivos Exportados Recientemente</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre del archivo</th>
                        <th>Tipo</th>
                        <th>Fecha de creación</th>
                        <th>Tamaño</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="archivos-exportados">
                    <tr>
                        <td colspan="5" class="text-center">No hay archivos exportados recientemente</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Función para generar exportación de actividad
function generarExportacionActividad() {
    const fechaDesde = document.getElementById('export-fecha-desde-act').value;
    const fechaHasta = document.getElementById('export-fecha-hasta-act').value;
    const idAlmacen = document.getElementById('export-almacen').value;
    const idPlanta = document.getElementById('export-planta').value;
    
    // Construir URL para la solicitud
    let url = '?export=actividad';
    url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
    
    if (idAlmacen) {
        url += `&id_almacen=${idAlmacen}`;
    }
    
    if (idPlanta) {
        url += `&id_planta=${idPlanta}`;
    }
    
    // Redirigir para descargar
    window.location.href = url;
}

// Función para generar exportación de reposiciones
function generarExportacionReposiciones() {
    const fechaDesde = document.getElementById('export-fecha-desde-repo').value;
    const fechaHasta = document.getElementById('export-fecha-hasta-repo').value;
    const idProducto = document.getElementById('export-producto-repo').value;
    const urgencia = document.getElementById('export-urgencia').value;
    
    // Construir URL para la solicitud
    let url = '?export=reposiciones';
    url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
    
    if (idProducto) {
        url += `&id_producto=${idProducto}`;
    }
    
    if (urgencia !== '') {
        url += `&urgente=${urgencia}`;
    }
    
    // Redirigir para descargar
    window.location.href = url;
}

// Función para generar exportación por producto
function generarExportacionProducto() {
    const idProducto = document.getElementById('export-producto').value;
    const fechaDesde = document.getElementById('export-fecha-desde-prod').value;
    const fechaHasta = document.getElementById('export-fecha-hasta-prod').value;
    
    if (!idProducto) {
        alert('Debe seleccionar un producto para generar el informe');
        return;
    }
    
    // Construir URL para la solicitud
    let url = '?export=informe-producto';
    url += `&id_producto=${idProducto}`;
    url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
    
    // Redirigir para descargar
    window.location.href = url;
}

// Función para generar exportación completa
function generarExportacionCompleta() {
    const fechaDesde = document.getElementById('export-fecha-desde-completo').value;
    const fechaHasta = document.getElementById('export-fecha-hasta-completo').value;
    const formato = document.getElementById('export-formato').value;
    const incluirMetadata = document.getElementById('export-incluir-metadata').checked ? '1' : '0';
    
    // Construir URL para la solicitud
    let url = '?export=dataset-completo';
    url += `&fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
    url += `&formato=${formato}`;
    url += `&incluir_metadata=${incluirMetadata}`;
    
    // Redirigir para descargar
    window.location.href = url;
}

// Cargar archivos exportados recientemente cuando se activa la pestaña
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (this.getAttribute('data-tab') === 'tab-exportar') {
                cargarArchivosExportados();
            }
        });
    });
});

// Función para cargar archivos exportados recientemente
function cargarArchivosExportados() {
    fetch('?action=listar-archivos-exportados')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('archivos-exportados');
            
            if (!data.success || data.archivos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay archivos exportados recientemente</td></tr>';
                return;
            }
            
            tbody.innerHTML = '';
            data.archivos.forEach(archivo => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${archivo.nombre}</td>
                    <td>${archivo.tipo}</td>
                    <td>${archivo.fecha_creacion}</td>
                    <td>${archivo.tamano}</td>
                    <td>
                        <a href="${archivo.url}" class="btn btn-sm btn-outline-primary" target="_blank" download>
                            <i class="bi bi-download"></i> Descargar
                        </a>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarArchivoExportado('${archivo.nombre}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(fila);
            });
        })
        .catch(error => {
            console.error('Error al cargar archivos exportados:', error);
            document.getElementById('archivos-exportados').innerHTML = 
                '<tr><td colspan="5" class="text-center text-danger">Error al cargar la lista de archivos</td></tr>';
        });
}

// Función para eliminar un archivo exportado
function eliminarArchivoExportado(nombreArchivo) {
    if (confirm('¿Está seguro de que desea eliminar este archivo?')) {
        fetch(`?action=eliminar-archivo-exportado&nombre=${encodeURIComponent(nombreArchivo)}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Archivo eliminado correctamente');
                cargarArchivosExportados();
            } else {
                alert('Error al eliminar el archivo: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al eliminar archivo:', error);
            alert('Error al procesar la solicitud');
        });
    }
}
</script>
