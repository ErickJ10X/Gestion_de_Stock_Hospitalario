<?php

use model\enum\RolEnum;

if (!isset($usuarios)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

$totalUsuarios = count($usuarios);
$usuariosActivos = array_reduce($usuarios, function ($carry, $u) {
    return $carry + ($u->isActivo() ? 1 : 0);
}, 0);
$porcentajeActivos = $totalUsuarios > 0 ? round(($usuariosActivos / $totalUsuarios) * 100) : 0;

// Ya no usamos la paginación manual del servidor, ahora será con JS
?>


<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3">

        <button id="btnNuevoUsuario" class="btn btn-sm btn-success">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Usuario
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
                <input type="text" id="buscarUsuario" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

    </div>
    <div class="card-body p-0">
        <div class="table-responsive w-100">
            <table class="list-table table table-striped table-hover w-100" id="usuariosDataTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($usuarios as $usuario) {
                    $badgeClass = $usuario->isActivo() ? 'bg-success' : 'bg-danger';
                    $badgeText = $usuario->isActivo() ? 'Activo' : 'Inactivo';
                    $rolBadgeClass = 'primary';

                    // Asignar colores según rol para mejor identificación visual
                    switch ($usuario->getRol()) {
                        case RolEnum::ADMINISTRADOR:
                            $rolBadgeClass = 'danger';
                            break;
                        case RolEnum::GESTOR_GENERAL:
                            $rolBadgeClass = 'primary';
                            break;
                        case RolEnum::GESTOR_HOSPITAL:
                            $rolBadgeClass = 'warning';
                            break;
                        case RolEnum::GESTOR_PLANTA:
                            $rolBadgeClass = 'info';
                            break;
                        case RolEnum::USUARIO_BOTIQUIN:
                            $rolBadgeClass = 'secondary';
                            break;
                    }
                    ?>
                    <tr>
                        <td><?= $usuario->getIdUsuario() ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="ms-2"><?= htmlspecialchars($usuario->getNombre()) ?></span>
                            </div>
                        </td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($usuario->getEmail()) ?>"
                               class="text-decoration-none">
                                <i class="far fa-envelope me-1"></i>
                                <?= htmlspecialchars($usuario->getEmail()) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-<?= $rolBadgeClass ?>"><?= htmlspecialchars($usuario->getRol()) ?></span>
                        </td>
                        <td>
                                <span class="badge <?= $badgeClass ?> status-badge">
                                    <i class="fas fa-<?= $usuario->isActivo() ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                    <?= $badgeText ?>
                                </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="#"
                                   class="list-table__button list-table__button--edit btn btn-sm"
                                   title="Editar usuario">
                                    <i class="fas fa-edit list-table__button-icon"></i> Editar
                                </a>

                                <button type="button"
                                        class="list-table__button list-table__button--locations btn btn-sm"
                                        title="Gestionar ubicaciones">
                                    <i class="fas fa-map-marker-alt list-table__button-icon"></i> Ubicaciones
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if (count($usuarios) === 0): ?>
                    <tr class="no-results-row">
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron usuarios registrados
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
                                id="total-registros"><?= $totalUsuarios ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de usuarios">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-usuarios">
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
    // Inicializar datos de usuarios para paginación
    window.datosUsuarios = <?= json_encode(array_map(function ($u) {
        return [
            'id' => $u->getIdUsuario(),
            'nombre' => $u->getNombre(),
            'email' => $u->getEmail(),
            'rol' => $u->getRol(),
            'activo' => $u->isActivo()
        ];
    }, $usuarios)) ?>;

    // Total de registros para paginación
    window.totalRegistros = <?= $totalUsuarios ?>;
</script>

<!-- Incluir el JS centralizado de usuarios -->
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/usuarios.js"></script>
