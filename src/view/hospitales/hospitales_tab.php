<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3">

        <button id="btnNuevoHospital" class="btn btn-sm btn-success">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Hospital
        </button>

        <div class="search-group">
            <div class="input-group input-group-sm">
                <select id="registrosPorPaginaHospitales" class="form-select form-select-sm">
                    <option value="5">5 registros</option>
                    <option value="10" selected>10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
            <div class="input-group input-group-sm">
                <input type="text" id="buscarHospital" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="list-table table table-striped table-hover" id="hospitalesDataTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Plantas</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($hospitales)): ?>
                    <tr>
                        <td colspan="4" class="list-table__empty">No hay hospitales registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($hospitales as $hospital): ?>
                        <tr class="list-table__body-row">
                            <td class="list-table__body-cell"
                                data-label="ID"><?= htmlspecialchars($hospital->getIdHospital()) ?></td>
                            <td class="list-table__body-cell"
                                data-label="Nombre"><?= htmlspecialchars($hospital->getNombre()) ?></td>
                            <td class="list-table__body-cell" data-label="Plantas">
                                <?php
                                $plantasHospital = $plantaController->getByHospital($hospital->getIdHospital())['plantas'] ?? [];
                                $cantidadPlantas = count($plantasHospital);
                                ?>
                                <span class="badge bg-info"><?= $cantidadPlantas ?> plantas</span>
                            </td>
                            <td class="list-table__body-cell" data-label="Acciones">
                                <div class="list-table__actions">
                                    <button class="list-table__button list-table__button--edit btn-edit-hospital"
                                            data-id="<?= $hospital->getIdHospital() ?>">
                                        <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                    </button>
                                    <button class="list-table__button list-table__button--delete btn-delete-hospital"
                                            data-id="<?= $hospital->getIdHospital() ?>">
                                        <i class="bi bi-trash list-table__button-icon"></i> Eliminar
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
                        Mostrando <span id="inicio-registros-hospitales">1</span> a <span id="fin-registros-hospitales">10</span>
                        de <span id="total-registros-hospitales"><?= count($hospitales) ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de hospitales">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-hospitales">
                            <!-- La paginación se generará dinámicamente con JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar datos de hospitales para paginación
    window.datosHospitales = <?= json_encode(array_map(function ($h) use ($plantaController) {
        $plantasHospital = $plantaController->getByHospital($h->getIdHospital())['plantas'] ?? [];
        return [
            'id' => $h->getIdHospital(),
            'nombre' => $h->getNombre(),
            'plantas' => count($plantasHospital)
        ];
    }, $hospitales)) ?>;
</script>
