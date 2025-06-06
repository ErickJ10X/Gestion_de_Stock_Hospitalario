<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($plantas) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3">

            <button id="btnNuevaPlanta" class="btn btn-sm btn-success">
                <i class="fas fa-plus-circle me-1"></i> Nueva Planta
            </button>
            
            <div class="search-group">
                <div class="input-group input-group-sm">
                    <select id="registrosPorPaginaPlantas" class="form-select form-select-sm">
                        <option value="5">5 registros</option>
                        <option value="10" selected>10 registros</option>
                        <option value="25">25 registros</option>
                        <option value="50">50 registros</option>
                    </select>
                </div>
                <div class="input-group input-group-sm">
                    <input type="text" id="buscarPlanta" class="form-control" placeholder="Buscar...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="list-table table table-striped table-hover" id="plantasDataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Hospital</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($plantas)): ?>
                        <tr>
                            <td colspan="4" class="list-table__empty">No hay plantas registradas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($plantas as $planta):
                            $hospitalPlanta = null;
                            foreach ($hospitales as $h) {
                                if ($h->getIdHospital() == $planta->getIdHospital()) {
                                    $hospitalPlanta = $h;
                                    break;
                                }
                            }
                        ?>
                            <tr class="list-table__body-row">
                                <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($planta->getIdPlanta()) ?></td>
                                <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($planta->getNombre()) ?></td>
                                <td class="list-table__body-cell" data-label="Hospital">
                                    <?= $hospitalPlanta ? htmlspecialchars($hospitalPlanta->getNombre()) : 'N/A' ?>
                                </td>
                                <td class="list-table__body-cell" data-label="Acciones">
                                    <div class="list-table__actions">
                                        <button class="list-table__button list-table__button--edit btn-edit-planta" data-id="<?= $planta->getIdPlanta() ?>">
                                            <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                        </button>
                                        <button class="list-table__button list-table__button--delete btn-delete-planta" data-id="<?= $planta->getIdPlanta() ?>">
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
                        Mostrando <span id="inicio-registros-plantas">1</span> a <span id="fin-registros-plantas">10</span> de <span id="total-registros-plantas"><?= count($plantas) ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de plantas">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-plantas">
                            <!-- La paginación se generará dinámicamente con JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar datos de plantas para paginación
    window.datosPlantas = <?= json_encode(array_map(function($p) use ($hospitales) {
        $hospitalNombre = "Desconocido";
        foreach ($hospitales as $h) {
            if ($h->getIdHospital() == $p->getIdHospital()) {
                $hospitalNombre = $h->getNombre();
                break;
            }
        }
        return [
            'id' => $p->getIdPlanta(),
            'nombre' => $p->getNombre(),
            'idHospital' => $p->getIdHospital(),
            'nombreHospital' => $hospitalNombre
        ];
    }, $plantas)) ?>;
</script>
