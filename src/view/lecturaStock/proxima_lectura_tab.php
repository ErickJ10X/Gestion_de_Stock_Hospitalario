<?php
if (!isset($botiquines) || !isset($proximasLecturas)) {
    echo '<p>Error: Datos necesarios no disponibles.</p>';
    exit;
}
?>

<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="search-group">
            <div class="input-group input-group-sm">
                <label for="filtro-botiquin-proximas" class="input-group-text">Filtrar por botiquín:</label>
                <select id="filtro-botiquin-proximas" class="form-select form-select-sm">
                    <option value="">Todos los botiquines</option>
                    <?php foreach ($botiquines as $botiquin): ?>
                        <option value="<?= $botiquin->getIdBotiquin() ?>">
                            <?= htmlspecialchars($botiquin->getNombre()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="btn-filtrar-proximas" class="btn btn-outline-secondary">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
                <button type="button" id="btn-reset-filtros-proximas" class="btn btn-outline-danger">
                    <i class="fas fa-times me-1"></i> Limpiar
                </button>
            </div>

            <div class="input-group input-group-sm">
                <select id="registrosPorPaginaProximas" class="form-select form-select-sm">
                    <option value="5">5 registros</option>
                    <option value="10" selected>10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>

            <div class="input-group input-group-sm">
                <input type="text" id="buscarProximaLectura" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabla-proximas-lecturas" class="list-table table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Botiquín</th>
                        <th>Producto</th>
                        <th>Última lectura</th>
                        <th>Cantidad actual</th>
                        <th>Fecha próxima lectura</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="proximas-lecturas-body">
                    <?php if (empty($proximasLecturas)): ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="alert alert-info my-3">
                                    No hay próximas lecturas programadas
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($proximasLecturas as $lectura): 
                            // Calcular el estado basado en la fecha próxima
                            $estado = '';
                            $estadoClass = '';
                            
                            $hoy = new DateTime();
                            $fechaProxima = new DateTime($lectura['fecha_proxima_lectura']);
                            
                            if ($fechaProxima < $hoy) {
                                $estado = 'Atrasada';
                                $estadoClass = 'bg-danger';
                            } else {
                                // Calcular días de diferencia
                                $diffDays = (int)$hoy->diff($fechaProxima)->format('%R%a');
                                
                                if ($diffDays <= 2) {
                                    $estado = 'Urgente';
                                    $estadoClass = 'bg-warning';
                                } else if ($diffDays <= 7) {
                                    $estado = 'Próxima';
                                    $estadoClass = 'bg-info';
                                } else {
                                    $estado = 'Programada';
                                    $estadoClass = 'bg-success';
                                }
                            }
                            
                            // Formatear fechas
                            $fechaUltimaLectura = new DateTime($lectura['ultima_fecha_lectura']);
                            $fechaUltimaFormateada = $fechaUltimaLectura->format('d/m/Y');
                            $fechaProximaFormateada = $fechaProxima->format('d/m/Y');
                        ?>
                            <tr class="list-table__body-row" data-botiquin-id="<?= $lectura['id_botiquin'] ?>">
                                <td class="list-table__body-cell" data-label="Botiquín"><?= htmlspecialchars($lectura['nombre_botiquin']) ?></td>
                                <td class="list-table__body-cell" data-label="Producto"><?= htmlspecialchars($lectura['codigo_producto'] . ' - ' . $lectura['nombre_producto']) ?></td>
                                <td class="list-table__body-cell" data-label="Última lectura"><?= $fechaUltimaFormateada ?></td>
                                <td class="list-table__body-cell" data-label="Cantidad"><?= $lectura['cantidad_disponible'] ?></td>
                                <td class="list-table__body-cell" data-label="Próxima lectura"><?= $fechaProximaFormateada ?></td>
                                <td class="list-table__body-cell" data-label="Estado">
                                    <span class="badge <?= $estadoClass ?> text-white"><?= $estado ?></span>
                                </td>
                                <td class="list-table__body-cell" data-label="Acciones">
                                    <div class="list-table__actions">
                                        <button class="list-table__button list-table__button--edit btn-registrar-lectura"
                                                data-botiquin-id="<?= $lectura['id_botiquin'] ?>"
                                                data-producto-id="<?= $lectura['id_producto'] ?>"
                                                onclick="registrarNuevaLectura(<?= $lectura['id_botiquin'] ?>, <?= $lectura['id_producto'] ?>)">
                                            <i class="bi bi-clipboard-plus list-table__button-icon"></i> Registrar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación con JS -->
        <div class="card-footer bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="paginacion-info">
                        Mostrando <span id="inicio-registros-proximas">1</span> a <span id="fin-registros-proximas"><?= min(10, count($proximasLecturas)) ?></span>
                        de <span id="total-registros-proximas"><?= count($proximasLecturas) ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de próximas lecturas">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-proximas">
                            <!-- La paginación se generará dinámicamente con JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.datosProximasLecturas = <?= json_encode($proximasLecturas) ?>;
</script>
