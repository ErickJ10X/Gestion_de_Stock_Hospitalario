<?php
if (!isset($almacenes) || !isset($session) || !isset($plantas) || !isset($hospitales)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Crear mapeos para mostrar nombres en lugar de IDs
$plantasMap = [];
foreach ($plantas as $planta) {
    $plantasMap[$planta->getIdPlanta()] = $planta->getNombre();
}

$hospitalesMap = [];
foreach ($hospitales as $hospital) {
    $hospitalesMap[$hospital->getIdHospital()] = $hospital->getNombre();
}

$totalAlmacenes = count($almacenes);
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button id="btn-add-almacen" class="btn btn-sm btn-success">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Almacén
        </button>

        <div class="search-group">
            <div class="input-group input-group-sm">
                <select id="registrosPorPagina" class="form-select form-select-sm">
                    <option value="5">5 registros</option>
                    <option value="10" selected>10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                    <option value="100">100 registros</option>
                </select>
            </div>
            <div class="input-group input-group-sm">
                <input type="text" id="buscarAlmacen" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive w-100">
            <table class="list-table table table-striped table-hover w-100" id="almacenesDataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Planta</th>
                        <th>Hospital</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($almacenes)): ?>
                        <tr class="no-results-row">
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-info-circle me-2"></i>
                                No hay almacenes registrados
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($almacenes as $almacen): ?>
                            <tr class="list-table__body-row">
                                <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($almacen->getIdAlmacen()) ?></td>
                                <td class="list-table__body-cell" data-label="Tipo">
                                    <span class="almacen-type <?= $almacen->getTipo() === 'General' ? 'almacen-type--general' : 'almacen-type--planta' ?>">
                                        <?= htmlspecialchars($almacen->getTipo()) ?>
                                    </span>
                                </td>
                                <td class="list-table__body-cell" data-label="Planta">
                                    <?= $almacen->getIdPlanta() ? htmlspecialchars($plantasMap[$almacen->getIdPlanta()] ?? 'Sin nombre') : 'N/A' ?>
                                </td>
                                <td class="list-table__body-cell" data-label="Hospital">
                                    <?= htmlspecialchars($hospitalesMap[$almacen->getIdHospital()] ?? 'Sin nombre') ?>
                                </td>
                                <td class="list-table__body-cell" data-label="Acciones">
                                    <div class="list-table__actions">
                                        <button class="list-table__button list-table__button--edit btn-edit-almacen" 
                                                onclick="editarAlmacen(<?= $almacen->getIdAlmacen() ?>)">
                                            <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                        </button>
                                        <button class="list-table__button list-table__button--delete btn-delete-almacen"
                                                onclick="eliminarAlmacen(<?= $almacen->getIdAlmacen() ?>)">
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
                        Mostrando <span id="inicio-registros">1</span> a <span id="fin-registros">10</span> de <span id="total-registros"><?= $totalAlmacenes ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de almacenes">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-almacenes">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" data-pagina="primera" aria-label="Primera página">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                    <span class="sr-only">Primera</span>
                                </a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link" href="#" data-pagina="anterior" aria-label="Página anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Anterior</span>
                                </a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#" data-pagina="1">1</a>
                            </li>
                            <!-- Los demás números de página se generarán dinámicamente con JS -->
                            <li class="page-item">
                                <a class="page-link" href="#" data-pagina="siguiente" aria-label="Página siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Siguiente</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" data-pagina="ultima" aria-label="Última página">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                    <span class="sr-only">Última</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar datos de almacenes para paginación
    window.datosAlmacenes = <?= json_encode(array_map(function ($a) use ($plantasMap, $hospitalesMap) {
        return [
            'id' => $a->getIdAlmacen(),
            'tipo' => $a->getTipo(),
            'planta_id' => $a->getIdPlanta(),
            'planta_nombre' => $a->getIdPlanta() ? ($plantasMap[$a->getIdPlanta()] ?? 'Sin nombre') : 'N/A',
            'hospital_id' => $a->getIdHospital(),
            'hospital_nombre' => $hospitalesMap[$a->getIdHospital()] ?? 'Sin nombre'
        ];
    }, $almacenes)) ?>;

    // Total de registros para paginación
    window.totalAlmacenes = <?= $totalAlmacenes ?>;
</script>
