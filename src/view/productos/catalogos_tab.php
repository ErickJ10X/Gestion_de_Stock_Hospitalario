<?php
if (!isset($catalogos) || !isset($plantas) || !isset($productos) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3">

        <button id="btnNuevoCatalogo" class="btn btn-sm btn-success">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Catálogo
        </button>

        <div class="search-group">
            <div class="input-group input-group-sm">
                <select id="registrosPorPaginaCatalogos" class="form-select form-select-sm">
                    <option value="5">5 registros</option>
                    <option value="10" selected>10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
            <div class="input-group input-group-sm">
                <input type="text" id="buscarCatalogo" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="list-table table table-striped table-hover" id="catalogosDataTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Código</th>
                    <th>Planta</th>
                    <th>Hospital</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($catalogos)): ?>
                    <tr>
                        <td colspan="7" class="list-table__empty">No hay catálogos registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($catalogos as $catalogo):
                        // Buscar producto asociado
                        $producto = null;
                        foreach ($productos as $p) {
                            if ($p->getIdProducto() == $catalogo->getIdProducto()) {
                                $producto = $p;
                                break;
                            }
                        }

                        // Buscar planta asociada
                        $planta = null;
                        $hospital = null;
                        foreach ($plantas as $p) {
                            if ($p->getIdPlanta() == $catalogo->getIdPlanta()) {
                                $planta = $p;
                                // Si hay hospital asociado a la planta, obtener su nombre
                                if ($planta->getHospital()) {
                                    $hospital = $planta->getHospital()->getNombre();
                                }
                                break;
                            }
                        }

                        if (!$producto || !$planta) continue;
                        ?>
                        <tr class="list-table__body-row <?= $catalogo->isActivo() ? '' : 'text-muted' ?>">
                            <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($catalogo->getIdCatalogo()) ?></td>
                            <td class="list-table__body-cell" data-label="Producto"><?= htmlspecialchars($producto->getNombre()) ?></td>
                            <td class="list-table__body-cell" data-label="Código"><?= htmlspecialchars($producto->getCodigo()) ?></td>
                            <td class="list-table__body-cell" data-label="Planta"><?= htmlspecialchars($planta->getNombre()) ?></td>
                            <td class="list-table__body-cell" data-label="Hospital"><?= $hospital ? htmlspecialchars($hospital) : 'N/A' ?></td>
                            <td class="list-table__body-cell" data-label="Estado">
                                <?php if ($catalogo->isActivo()): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="list-table__body-cell" data-label="Acciones">
                                <div class="list-table__actions">
                                    <button class="list-table__button list-table__button--edit"
                                            onclick="seleccionarCatalogo(<?= $catalogo->getIdCatalogo() ?>)"
                                            title="Editar catálogo">
                                        <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                    </button>
                                    <button class="list-table__button list-table__button--delete"
                                            onclick="confirmarEliminarCatalogo(<?= $catalogo->getIdCatalogo() ?>, '<?= htmlspecialchars(addslashes($producto->getNombre())) ?>', '<?= htmlspecialchars(addslashes($planta->getNombre())) ?>')"
                                            title="Eliminar del catálogo">
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
                        Mostrando <span id="inicio-registros-catalogos">1</span> a <span id="fin-registros-catalogos">10</span>
                        de <span id="total-registros-catalogos"><?= count($catalogos) ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de catálogos">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-catalogos">
                            <!-- La paginación se generará dinámicamente con JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar datos de catálogos para paginación
    window.datosCatalogos = <?= json_encode(array_map(function ($c) use ($productos, $plantas) {
        // Buscar producto asociado
        $producto = null;
        foreach ($productos as $p) {
            if ($p->getIdProducto() == $c->getIdProducto()) {
                $producto = $p;
                break;
            }
        }

        // Buscar planta asociada
        $planta = null;
        $hospital = null;
        foreach ($plantas as $p) {
            if ($p->getIdPlanta() == $c->getIdPlanta()) {
                $planta = $p;
                // Si hay hospital asociado a la planta, obtener su nombre
                if ($planta->getHospital()) {
                    $hospital = $planta->getHospital()->getNombre();
                }
                break;
            }
        }

        return [
            'id' => $c->getIdCatalogo(),
            'producto' => $producto ? $producto->getNombre() : 'N/A',
            'codigo' => $producto ? $producto->getCodigo() : 'N/A',
            'planta' => $planta ? $planta->getNombre() : 'N/A',
            'hospital' => $hospital ?: 'N/A',
            'activo' => $c->isActivo(),
            'id_producto' => $c->getIdProducto(),
            'id_planta' => $c->getIdPlanta()
        ];
    }, $catalogos)) ?>;
</script>