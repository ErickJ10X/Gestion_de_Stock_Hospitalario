/**
 * Archivo JavaScript para el módulo de reposiciones
 * Contiene todas las funcionalidades JS de las diferentes pestañas:
 * - Órdenes Pendientes
 * - Generar Reposición
 * - Histórico
 */

document.addEventListener('DOMContentLoaded', function() {
    // Variables compartidas
    const reposicionOverlay = document.querySelector('.reposicion-overlay');

    // ==================================================
    // FUNCIONALIDAD: ÓRDENES PENDIENTES
    // ==================================================
    const initOrdenesPendientes = () => {
        const formReposicion = document.getElementById('form-reposicion');
        const formTitle = document.getElementById('form-title');
        const formReposicionForm = document.getElementById('reposicion-form');
        const btnNuevaReposicion = document.getElementById('nueva-reposicion');
        const confirmDelete = document.getElementById('confirm-delete');
        let currentReposicionId = null;
        
        // Si los elementos no existen, no inicializar esta funcionalidad
        if (!formReposicion || !btnNuevaReposicion) return;
        
        // Filtros
        const filtroUrgencia = document.getElementById('filtro-urgencia');
        const filtroAlmacen = document.getElementById('filtro-almacen');
        const filtroBotiquin = document.getElementById('filtro-botiquin');
        const filtroProducto = document.getElementById('filtro-producto');
        const btnAplicarFiltros = document.getElementById('aplicar-filtros');
        const btnLimpiarFiltros = document.getElementById('limpiar-filtros');
        
        // Aplicar filtros
        btnAplicarFiltros.addEventListener('click', function() {
            const filas = document.querySelectorAll('#tabla-reposiciones tbody tr:not(.no-results)');
            let hayResultados = false;
            
            filas.forEach(function(fila) {
                const urgente = fila.getAttribute('data-urgente');
                const almacen = fila.getAttribute('data-almacen');
                const botiquin = fila.getAttribute('data-botiquin');
                const producto = fila.getAttribute('data-producto');
                
                let mostrarFila = true;
                
                if (filtroUrgencia.value && filtroUrgencia.value !== urgente) {
                    mostrarFila = false;
                }
                
                if (filtroAlmacen.value && filtroAlmacen.value !== almacen) {
                    mostrarFila = false;
                }
                
                if (filtroBotiquin.value && filtroBotiquin.value !== botiquin) {
                    mostrarFila = false;
                }
                
                if (filtroProducto.value && filtroProducto.value !== producto) {
                    mostrarFila = false;
                }
                
                fila.style.display = mostrarFila ? '' : 'none';
                if (mostrarFila) {
                    hayResultados = true;
                }
            });
            
            // Mostrar mensaje si no hay resultados
            const mensajeNoResultados = document.querySelector('#tabla-reposiciones .no-results');
            if (!hayResultados) {
                if (!mensajeNoResultados) {
                    const tbody = document.querySelector('#tabla-reposiciones tbody');
                    const tr = document.createElement('tr');
                    tr.className = 'no-results';
                    tr.innerHTML = '<td colspan="8" class="text-center">No se encontraron resultados con los filtros aplicados</td>';
                    tbody.appendChild(tr);
                } else {
                    mensajeNoResultados.style.display = '';
                }
            } else if (mensajeNoResultados) {
                mensajeNoResultados.style.display = 'none';
            }
        });
        
        // Limpiar filtros
        btnLimpiarFiltros.addEventListener('click', function() {
            filtroUrgencia.value = '';
            filtroAlmacen.value = '';
            filtroBotiquin.value = '';
            filtroProducto.value = '';
            
            document.querySelectorAll('#tabla-reposiciones tbody tr').forEach(function(fila) {
                fila.style.display = '';
            });
            
            const mensajeNoResultados = document.querySelector('#tabla-reposiciones .no-results');
            if (mensajeNoResultados) {
                mensajeNoResultados.style.display = 'none';
            }
        });
        
        // Mostrar formulario para nueva reposición
        btnNuevaReposicion.addEventListener('click', function() {
            formTitle.textContent = 'Nueva Orden de Reposición';
            formReposicionForm.reset();
            document.getElementById('id_reposicion').value = '';
            
            // Establecer fecha actual por defecto
            const now = new Date();
            const nowFormatted = now.getFullYear() + '-' + 
                                String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                                String(now.getDate()).padStart(2, '0') + 'T' + 
                                String(now.getHours()).padStart(2, '0') + ':' + 
                                String(now.getMinutes()).padStart(2, '0');
            document.getElementById('fecha').value = nowFormatted;
            
            formReposicion.style.display = 'block';
            reposicionOverlay.style.display = 'block';
        });
        
        // Cerrar formulario
        document.querySelectorAll('.card-form__close, .cancel-form').forEach(function(el) {
            el.addEventListener('click', function() {
                formReposicion.style.display = 'none';
                reposicionOverlay.style.display = 'none';
            });
        });
        
        // Editar reposición
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const reposicionId = this.getAttribute('data-id');
                currentReposicionId = reposicionId;
                
                // Aquí normalmente harías una solicitud AJAX para obtener los detalles de la reposición
                // Por ahora, simulamos llenando con datos de la tabla
                
                const fila = document.querySelector(`tr[data-id="${reposicionId}"]`);
                
                document.getElementById('id_reposicion').value = reposicionId;
                document.getElementById('id_producto').value = fila.getAttribute('data-producto');
                document.getElementById('desde_almacen').value = fila.getAttribute('data-almacen');
                document.getElementById('hasta_botiquin').value = fila.getAttribute('data-botiquin');
                
                const celdas = fila.querySelectorAll('td');
                document.getElementById('cantidad_repuesta').value = celdas[4].textContent;
                
                // Formatear fecha para input datetime-local
                const fechaStr = celdas[5].textContent;
                const fecha = new Date(fechaStr);
                const fechaFormatted = fecha.getFullYear() + '-' + 
                                    String(fecha.getMonth() + 1).padStart(2, '0') + '-' + 
                                    String(fecha.getDate()).padStart(2, '0') + 'T' + 
                                    String(fecha.getHours()).padStart(2, '0') + ':' + 
                                    String(fecha.getMinutes()).padStart(2, '0');
                document.getElementById('fecha').value = fechaFormatted;
                
                document.getElementById('urgente').checked = fila.getAttribute('data-urgente') === '1';
                
                formTitle.textContent = 'Editar Orden de Reposición';
                formReposicion.style.display = 'block';
                reposicionOverlay.style.display = 'block';
            });
        });
        
        // Eliminar reposición
        document.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', function() {
                currentReposicionId = this.getAttribute('data-id');
                confirmDelete.style.display = 'block';
                reposicionOverlay.style.display = 'block';
            });
        });
        
        // Cerrar modal de confirmación
        document.querySelectorAll('.modal-confirm__close').forEach(function(el) {
            el.addEventListener('click', function() {
                confirmDelete.style.display = 'none';
                reposicionOverlay.style.display = 'none';
            });
        });
        
        // Confirmar eliminación
        document.getElementById('btn-confirmar-eliminar').addEventListener('click', function() {
            if (currentReposicionId) {
                // Aquí normalmente harías una solicitud AJAX para eliminar la reposición
                // Por ahora, simulamos eliminando la fila de la tabla
                
                document.querySelector(`tr[data-id="${currentReposicionId}"]`).remove();
                
                confirmDelete.style.display = 'none';
                reposicionOverlay.style.display = 'none';
                
                // Mostrar mensaje de éxito
                showMessage('Reposición eliminada correctamente', 'success');
            }
        });
        
        // Marcar como completada
        document.querySelectorAll('.btn-complete').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const reposicionId = this.getAttribute('data-id');
                
                // Aquí normalmente harías una solicitud AJAX para marcar como completada
                // Por ahora, simulamos eliminando la fila de la tabla
                
                document.querySelector(`tr[data-id="${reposicionId}"]`).remove();
                
                // Mostrar mensaje de éxito
                showMessage('Reposición marcada como completada', 'success');
            });
        });
        
        // Enviar formulario
        formReposicionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Aquí normalmente harías una solicitud AJAX para guardar los datos
            // Por ahora, simulamos cerrando el formulario
            
            formReposicion.style.display = 'none';
            reposicionOverlay.style.display = 'none';
            
            // Mostrar mensaje de éxito
            if (document.getElementById('id_reposicion').value) {
                showMessage('Reposición actualizada correctamente', 'success');
            } else {
                showMessage('Reposición creada correctamente', 'success');
            }
        });
    };

    // ==================================================
    // FUNCIONALIDAD: GENERAR REPOSICIÓN
    // ==================================================
    const initGenerarReposicion = () => {
        const form = document.getElementById('nueva-reposicion-form');
        const idProductoSelect = document.getElementById('id_producto');
        const fechaInput = document.getElementById('fecha');
        const confirmacionModal = document.getElementById('confirmacion-modal');
        
        // Si los elementos no existen, no inicializar esta funcionalidad
        if (!form || !idProductoSelect) return;
        
        // Establecer fecha actual por defecto
        const now = new Date();
        const nowFormatted = now.getFullYear() + '-' + 
                            String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                            String(now.getDate()).padStart(2, '0') + 'T' + 
                            String(now.getHours()).padStart(2, '0') + ':' + 
                            String(now.getMinutes()).padStart(2, '0');
        fechaInput.value = nowFormatted;
        
        // Actualizar información del producto al cambiar la selección
        idProductoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const productDetails = document.querySelector('.product-details');
            const noProductSelected = document.querySelector('.no-product-selected');
            const unidadMedidaSpan = document.getElementById('unidad-medida');
            
            if (this.value) {
                // Obtener datos del producto seleccionado
                const productoId = this.value;
                
                // Nota: La parte PHP se maneja en el archivo PHP original y no se puede
                // incluir directamente aquí. En su lugar, podríamos hacer una petición
                // AJAX para obtener la información del producto, pero para este ejemplo
                // usaremos los datos disponibles en el HTML (dataset de la opción seleccionada)
                const unidadMedida = selectedOption.dataset.unidad;
                
                // Datos simulados para el ejemplo
                const productoNombre = selectedOption.textContent.split(' - ')[1] || '';
                const productoCodigo = selectedOption.textContent.split(' - ')[0] || '';
                
                if (document.getElementById('producto-codigo')) {
                    document.getElementById('producto-codigo').textContent = productoCodigo;
                    document.getElementById('producto-nombre').textContent = productoNombre;
                    document.getElementById('producto-unidad').textContent = unidadMedida || 'unidades';
                    
                    // Actualizar la unidad de medida en el input de cantidad
                    if (unidadMedidaSpan) {
                        unidadMedidaSpan.textContent = unidadMedida || 'unidades';
                    }
                    
                    // Mostrar los detalles
                    if (productDetails && noProductSelected) {
                        productDetails.style.display = 'block';
                        noProductSelected.style.display = 'none';
                    }
                }
            } else {
                // Si no hay producto seleccionado, mostrar mensaje
                if (productDetails && noProductSelected) {
                    productDetails.style.display = 'none';
                    noProductSelected.style.display = 'block';
                }
                if (unidadMedidaSpan) {
                    unidadMedidaSpan.textContent = 'unidades';
                }
            }
        });
        
        // Mostrar modal de confirmación al enviar el formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar formulario
            if (!form.checkValidity()) {
                return;
            }
            
            // Obtener valores seleccionados para mostrar en la confirmación
            const desdeAlmacenSelect = document.getElementById('desde_almacen');
            const hastaBotiquinSelect = document.getElementById('hasta_botiquin');
            const cantidadInput = document.getElementById('cantidad_repuesta');
            const urgenteCheckbox = document.getElementById('urgente');
            const unidadMedidaSpan = document.getElementById('unidad-medida');
            
            const productoText = idProductoSelect.options[idProductoSelect.selectedIndex].text;
            const almacenText = desdeAlmacenSelect.options[desdeAlmacenSelect.selectedIndex].text;
            const botiquinText = hastaBotiquinSelect.options[hastaBotiquinSelect.selectedIndex].text;
            const cantidad = cantidadInput.value + ' ' + unidadMedidaSpan.textContent;
            const fecha = fechaInput.value ? new Date(fechaInput.value).toLocaleString() : 'Fecha actual';
            const urgencia = urgenteCheckbox.checked ? 'Urgente' : 'Normal';
            
            // Actualizar el modal de confirmación con los valores
            document.getElementById('confirm-producto').textContent = productoText;
            document.getElementById('confirm-almacen').textContent = almacenText;
            document.getElementById('confirm-botiquin').textContent = botiquinText;
            document.getElementById('confirm-cantidad').textContent = cantidad;
            document.getElementById('confirm-fecha').textContent = fecha;
            document.getElementById('confirm-urgencia').textContent = urgencia;
            
            // Mostrar modal de confirmación
            confirmacionModal.style.display = 'block';
            reposicionOverlay.style.display = 'block';
        });
        
        // Cerrar modal de confirmación
        document.querySelectorAll('.confirmacion-modal-close').forEach(function(btn) {
            btn.addEventListener('click', function() {
                confirmacionModal.style.display = 'none';
                reposicionOverlay.style.display = 'none';
            });
        });
        
        // Confirmar y enviar formulario
        document.getElementById('confirmar-reposicion').addEventListener('click', function() {
            // Aquí realizaríamos la petición AJAX para crear la reposición
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                confirmacionModal.style.display = 'none';
                reposicionOverlay.style.display = 'none';
                
                if (!data.error) {
                    // Mostrar mensaje de éxito
                    showMessage('Reposición creada correctamente', 'success');
                    
                    // Limpiar formulario
                    form.reset();
                    fechaInput.value = nowFormatted;
                    
                    // Actualizar información del producto
                    const productDetails = document.querySelector('.product-details');
                    const noProductSelected = document.querySelector('.no-product-selected');
                    const unidadMedidaSpan = document.getElementById('unidad-medida');
                    if (productDetails && noProductSelected) {
                        productDetails.style.display = 'none';
                        noProductSelected.style.display = 'block';
                    }
                    if (unidadMedidaSpan) {
                        unidadMedidaSpan.textContent = 'unidades';
                    }
                    
                    // Cambiar a la pestaña de órdenes pendientes después de crear
                    document.querySelector('[data-tab="tab-ordenes-pendientes"]').click();
                } else {
                    // Mostrar mensaje de error
                    showMessage(data.mensaje || 'Error al crear la reposición', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                confirmacionModal.style.display = 'none';
                reposicionOverlay.style.display = 'none';
                
                // Mostrar mensaje de error
                showMessage('Error en el servidor. Intente más tarde.', 'error');
            });
        });

        // Limpiar formulario
        document.getElementById('limpiar-form-btn').addEventListener('click', function() {
            setTimeout(() => {
                // Restablecer fecha predeterminada después del reset
                fechaInput.value = nowFormatted;
                
                // Ocultar detalles del producto
                const productDetails = document.querySelector('.product-details');
                const noProductSelected = document.querySelector('.no-product-selected');
                const unidadMedidaSpan = document.getElementById('unidad-medida');
                if (productDetails && noProductSelected) {
                    productDetails.style.display = 'none';
                    noProductSelected.style.display = 'block';
                }
                if (unidadMedidaSpan) {
                    unidadMedidaSpan.textContent = 'unidades';
                }
            }, 0);
        });
    };

    // ==================================================
    // FUNCIONALIDAD: HISTÓRICO
    // ==================================================
    const initHistorico = () => {
        const filtroAnio = document.getElementById('filtro-anio');
        const filtroMes = document.getElementById('filtro-mes');
        const filtroProducto = document.getElementById('filtro-producto-historico');
        const filtroAlmacen = document.getElementById('filtro-almacen-historico');
        const filtroBotiquin = document.getElementById('filtro-botiquin-historico');
        const btnAplicarFiltros = document.getElementById('aplicar-filtros-historico');
        const btnLimpiarFiltros = document.getElementById('limpiar-filtros-historico');
        const btnExportar = document.getElementById('btn-exportar-historico');
        const modalDetalles = document.getElementById('modal-detalles-reposicion');
        
        // Si los elementos no existen, no inicializar esta funcionalidad
        if (!filtroAnio || !btnAplicarFiltros) return;
        
        // Establecer el año actual como valor predeterminado
        filtroAnio.value = new Date().getFullYear();
        
        // Aplicar filtros
        btnAplicarFiltros.addEventListener('click', function() {
            const anio = filtroAnio.value;
            const mes = filtroMes.value;
            const producto = filtroProducto.value;
            const almacen = filtroAlmacen.value;
            const botiquin = filtroBotiquin.value;
            
            const filas = document.querySelectorAll('#tabla-historico tbody tr:not(.no-results)');
            let hayResultados = false;
            
            filas.forEach(function(fila) {
                if (fila.classList.contains('no-results')) return;
                
                let mostrar = true;
                
                if (anio && fila.getAttribute('data-anio') !== anio) {
                    mostrar = false;
                }
                
                if (mes && fila.getAttribute('data-mes') !== mes) {
                    mostrar = false;
                }
                
                if (producto && fila.getAttribute('data-producto') !== producto) {
                    mostrar = false;
                }
                
                if (almacen && fila.getAttribute('data-almacen') !== almacen) {
                    mostrar = false;
                }
                
                if (botiquin && fila.getAttribute('data-botiquin') !== botiquin) {
                    mostrar = false;
                }
                
                fila.style.display = mostrar ? '' : 'none';
                if (mostrar) {
                    hayResultados = true;
                }
            });
            
            // Mostrar mensaje si no hay resultados
            const tbody = document.querySelector('#tabla-historico tbody');
            const noResults = document.querySelector('#tabla-historico .no-results');
            
            if (!hayResultados) {
                if (!noResults) {
                    const tr = document.createElement('tr');
                    tr.className = 'no-results';
                    tr.innerHTML = '<td colspan="8" class="text-center">No se encontraron registros con los filtros seleccionados</td>';
                    tbody.appendChild(tr);
                } else {
                    noResults.style.display = '';
                }
            } else if (noResults) {
                noResults.style.display = 'none';
            }
        });
        
        // Limpiar filtros
        btnLimpiarFiltros.addEventListener('click', function() {
            filtroAnio.value = '';
            filtroMes.value = '';
            filtroProducto.value = '';
            filtroAlmacen.value = '';
            filtroBotiquin.value = '';
            
            const filas = document.querySelectorAll('#tabla-historico tbody tr');
            filas.forEach(function(fila) {
                if (!fila.classList.contains('no-results')) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
        
        // Ver detalles de una reposición
        document.querySelectorAll('.btn-view').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const reposicionId = this.getAttribute('data-id');
                const fila = document.querySelector(`tr[data-id="${reposicionId}"]`);
                
                if (fila) {
                    // Obtener información del elemento seleccionado
                    const celdas = fila.querySelectorAll('td');
                    const productoId = fila.getAttribute('data-producto');
                    
                    // Nota: La parte PHP se maneja en el archivo PHP original
                    // Aquí solo actualizamos los campos del modal con los datos que ya tenemos
                    
                    // Actualizar información en el modal
                    document.getElementById('detalle-id').textContent = celdas[0].textContent;
                    document.getElementById('detalle-fecha').textContent = celdas[1].textContent;
                    document.getElementById('detalle-producto').textContent = celdas[2].textContent;
                    document.getElementById('detalle-codigo-producto').textContent = 'Código del producto'; // Esto normalmente vendría del backend
                    document.getElementById('detalle-almacen').textContent = celdas[3].textContent;
                    document.getElementById('detalle-botiquin').textContent = celdas[4].textContent;
                    document.getElementById('detalle-cantidad').textContent = celdas[5].textContent;
                    document.getElementById('detalle-urgencia').textContent = celdas[6].textContent.trim();
                    
                    // Aplicar clase según urgencia
                    const urgencia = document.getElementById('detalle-urgencia');
                    if (urgencia.textContent === 'Urgente') {
                        urgencia.className = 'detail-value text-danger';
                    } else {
                        urgencia.className = 'detail-value text-secondary';
                    }
                    
                    // Mostrar modal
                    modalDetalles.style.display = 'block';
                    reposicionOverlay.style.display = 'block';
                }
            });
        });
        
        // Imprimir directamente
        document.querySelectorAll('.btn-print').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const reposicionId = this.getAttribute('data-id');
                imprimirReposicion(reposicionId);
            });
        });
        
        // Imprimir desde el modal de detalles
        if (document.getElementById('btn-imprimir-detalle')) {
            document.getElementById('btn-imprimir-detalle').addEventListener('click', function() {
                const id = document.getElementById('detalle-id').textContent;
                imprimirReposicion(id);
            });
        }
        
        // Función para imprimir reposición
        function imprimirReposicion(id) {
            // Preparar contenido para impresión
            const fila = document.querySelector(`tr[data-id="${id}"]`);
            if (!fila) return;
            
            const celdas = fila.querySelectorAll('td');
            const titulo = document.querySelector('.tab-header h2').textContent;
            const fecha = new Date().toLocaleDateString('es-ES');
            const hora = new Date().toLocaleTimeString('es-ES');
            
            // Crear ventana de impresión
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Reposición #${id}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 20px;
                            font-size: 12pt;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 20px;
                            border-bottom: 1px solid #ccc;
                            padding-bottom: 10px;
                        }
                        .header h1 {
                            margin: 0;
                            font-size: 18pt;
                        }
                        .header p {
                            margin: 5px 0 0;
                            font-size: 10pt;
                        }
                        .detalle {
                            margin-bottom: 30px;
                        }
                        .detalle h2 {
                            margin: 0 0 10px;
                            font-size: 14pt;
                            border-bottom: 1px solid #eee;
                            padding-bottom: 5px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        table, th, td {
                            border: 1px solid #ddd;
                        }
                        th, td {
                            padding: 8px;
                            text-align: left;
                        }
                        .footer {
                            margin-top: 30px;
                            border-top: 1px solid #eee;
                            padding-top: 10px;
                            font-size: 10pt;
                        }
                        .urgente {
                            font-weight: bold;
                            color: #d9534f;
                        }
                        .normal {
                            color: #777;
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>${titulo}</h1>
                        <p>Generado el ${fecha} a las ${hora}</p>
                    </div>
                    
                    <div class="detalle">
                        <h2>Detalle de Reposición #${celdas[0].textContent}</h2>
                        <table>
                            <tr><th>Fecha:</th><td>${celdas[1].textContent}</td></tr>
                            <tr><th>Producto:</th><td>${celdas[2].textContent}</td></tr>
                            <tr><th>Almacén Origen:</th><td>${celdas[3].textContent}</td></tr>
                            <tr><th>Botiquín Destino:</th><td>${celdas[4].textContent}</td></tr>
                            <tr><th>Cantidad:</th><td>${celdas[5].textContent}</td></tr>
                            <tr><th>Urgencia:</th><td class="${celdas[6].textContent.trim() === 'Urgente' ? 'urgente' : 'normal'}">${celdas[6].textContent.trim()}</td></tr>
                        </table>
                    </div>
                    
                    <div class="footer">
                        <p>Este documento es solo para fines informativos.</p>
                    </div>
                    
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 500);
                        };
                    </script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
        
        // Exportar a Excel
        btnExportar.addEventListener('click', function() {
            // Filtrar las filas visibles
            const filas = document.querySelectorAll('#tabla-historico tbody tr:not(.no-results)');
            const filasVisibles = Array.from(filas).filter(f => f.style.display !== 'none');
            
            if (filasVisibles.length === 0) {
                alert('No hay datos para exportar');
                return;
            }
            
            let csvContent = 'ID,Fecha,Producto,Almacén Origen,Botiquín Destino,Cantidad,Urgencia\n';
            
            filasVisibles.forEach(function(fila) {
                const celdas = fila.querySelectorAll('td');
                const id = celdas[0].textContent.trim();
                const fecha = celdas[1].textContent.trim();
                const producto = celdas[2].textContent.trim();
                const almacen = celdas[3].textContent.trim();
                const botiquin = celdas[4].textContent.trim();
                const cantidad = celdas[5].textContent.trim();
                const urgencia = celdas[6].textContent.trim();
                
                csvContent += '"' + id + '","' + fecha + '","' + producto + '","' + almacen + '","' + botiquin + '","' + cantidad + '","' + urgencia + '"\n';
            });
            
            // Crear un enlace de descarga
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            const fechaActual = new Date().toISOString().slice(0, 10);
            
            link.setAttribute("href", url);
            link.setAttribute("download", "historico-reposiciones-" + fechaActual + ".csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
        
        // Cerrar modal de detalles
        document.querySelectorAll('.modal-details__close').forEach(function(btn) {
            btn.addEventListener('click', function() {
                modalDetalles.style.display = 'none';
                reposicionOverlay.style.display = 'none';
            });
        });
    };

    // ==================================================
    // UTILIDADES
    // ==================================================
    
    // Función para mostrar mensajes de alerta
    const showMessage = (message, type) => {
        const alertClass = type === 'success' ? 'list-alert--success' : 'list-alert--error';
        const successAlert = document.createElement('div');
        successAlert.className = `list-alert ${alertClass}`;
        successAlert.innerHTML = `
            <p class="list-alert__message">${message}</p>
            <button type="button" class="list-alert__close">&times;</button>
        `;
        document.querySelector('.list-container').insertBefore(
            successAlert, 
            document.querySelector('.tabs-container')
        );
        
        // Añadir evento para cerrar la alerta
        successAlert.querySelector('.list-alert__close').addEventListener('click', function() {
            successAlert.remove();
        });
    };

    // Inicializar todas las funcionalidades
    initOrdenesPendientes();
    initGenerarReposicion();
    initHistorico();

    // Configurar manejadores para las alertas existentes al cargar la página
    document.querySelectorAll('.list-alert__close').forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
});
