<?php
if (!isset($hospitales) || !isset($plantas) || !isset($botiquines) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

$totalBotiquines = count($botiquines);
$botiquinesActivos = $totalBotiquines; // Todos los botiquines se consideran activos
$porcentajeActivos = $totalBotiquines > 0 ? round(($botiquinesActivos / $totalBotiquines) * 100) : 0;

// Ya no usamos la paginación manual del servidor, ahora será con JS
?>


<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <button id="btnNuevoBotiquin" class="btn btn-sm btn-success" onclick="irACrearBotiquin()">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Botiquín
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
                <input type="text" id="buscarBotiquin" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive w-100">
            <table class="table table-striped table-hover" id="botiquinesDataTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Planta</th>
                    <th>Hospital</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($botiquines as $botiquin):
                    $plantaBotiquin = null;
                    foreach ($plantas as $p) {
                        if ($p->getIdPlanta() == $botiquin->getIdPlanta()) {
                            $plantaBotiquin = $p;
                            break;
                        }
                    }

                    $hospitalBotiquin = null;
                    if ($plantaBotiquin) {
                        foreach ($hospitales as $h) {
                            if ($h->getIdHospital() == $plantaBotiquin->getIdHospital()) {
                                $hospitalBotiquin = $h;
                                break;
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td><?= $botiquin->getIdBotiquin() ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-first-aid me-2 text-success"></i>
                                <span class="ms-2"><?= htmlspecialchars($botiquin->getNombre()) ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <i class="fas fa-clinic-medical me-1"></i>
                                <?= $plantaBotiquin ? htmlspecialchars($plantaBotiquin->getNombre()) : 'N/A' ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                <i class="fas fa-hospital me-1"></i>
                                <?= $hospitalBotiquin ? htmlspecialchars($hospitalBotiquin->getNombre()) : 'N/A' ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquin/?id=<?= $botiquin->getIdBotiquin() ?>" 
                                   class="btn btn-sm btn-info"
                                   title="Ver botiquín">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <button type="button"
                                       class="btn btn-sm btn-primary"
                                       onclick="editarBotiquin(<?= $botiquin->getIdBotiquin() ?>)" 
                                       title="Editar botiquín">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($botiquines) === 0): ?>
                    <tr class="no-results-row">
                        <td colspan="5" class="text-center py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron botiquines registrados
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación con JS -->
        <div class="card-footer bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="paginacion-info">
                        Mostrando <span id="inicio-registros">1</span> a <span id="fin-registros">10</span> de <span
                                id="total-registros"><?= $totalBotiquines ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de botiquines">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-botiquines">
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
    // Inicializar datos de botiquines para paginación
    window.datosBotiquines = <?= json_encode(array_map(function ($b) use ($plantas, $hospitales) {
        $plantaBotiquin = null;
        foreach ($plantas as $p) {
            if ($p->getIdPlanta() == $b->getIdPlanta()) {
                $plantaBotiquin = $p;
                break;
            }
        }

        $hospitalBotiquin = null;
        if ($plantaBotiquin) {
            foreach ($hospitales as $h) {
                if ($h->getIdHospital() == $plantaBotiquin->getIdHospital()) {
                    $hospitalBotiquin = $h;
                    break;
                }
            }
        }
        
        return [
            'id' => $b->getIdBotiquin(),
            'nombre' => $b->getNombre(),
            'planta_id' => $b->getIdPlanta(),
            'planta_nombre' => $plantaBotiquin ? $plantaBotiquin->getNombre() : 'N/A',
            'hospital_nombre' => $hospitalBotiquin ? $hospitalBotiquin->getNombre() : 'N/A'
        ];
    }, $botiquines)) ?>;

    // Total de registros para paginación
    window.totalRegistros = <?= $totalBotiquines ?>;
    
    // Función para ir a crear botiquín
    function irACrearBotiquin() {
        // Cambiar a la pestaña de agregar/editar
        const tabBtn = document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]');
        if (tabBtn) {
            // Hacer clic en la pestaña para cambiar a ella
            tabBtn.click();
            
            // Pequeña espera para asegurar que la pestaña se haya cargado
            setTimeout(() => {
                // Activar el formulario "Crear Botiquín" (primero por defecto) 
                const crearBtn = document.querySelector('.form-toggle-btn[data-target="crearForm"]');
                if (crearBtn && !crearBtn.classList.contains('active')) {
                    crearBtn.click();
                }
            }, 100);
        }
    }
    
    // Función para editar botiquín
    function editarBotiquin(idBotiquin) {
        // Cambiar a la pestaña de agregar/editar
        const tabBtn = document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]');
        if (tabBtn) {
            // Hacer clic en la pestaña para cambiar a ella
            tabBtn.click();
            
            // Pequeña espera para asegurar que la pestaña se haya cargado
            setTimeout(() => {
                // Activar el formulario "Editar Botiquín"
                const editarBtn = document.querySelector('.form-toggle-btn[data-target="editarForm"]');
                if (editarBtn) {
                    editarBtn.click();
                    
                    // Otra pequeña espera para asegurar que el formulario se active
                    setTimeout(() => {
                        // Seleccionar el botiquín en el dropdown
                        const selectBotiquin = document.getElementById('select_botiquin');
                        if (selectBotiquin) {
                            selectBotiquin.value = idBotiquin;
                            
                            // Disparar el evento change para cargar los datos del botiquín
                            const changeEvent = new Event('change');
                            selectBotiquin.dispatchEvent(changeEvent);
                        }
                    }, 100);
                }
            }, 100);
        }
    }
</script>