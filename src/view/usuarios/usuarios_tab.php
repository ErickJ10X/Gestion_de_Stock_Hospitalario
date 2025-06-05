<?php

use model\enum\RolEnum;

if (!isset($usuarios)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Paginación manual
$usuariosPorPagina = 10;
$totalUsuarios = count($usuarios);
$totalPaginas = max(1, ceil($totalUsuarios / $usuariosPorPagina));
$paginaActual = isset($_GET['pagina']) ? max(1, min($totalPaginas, intval($_GET['pagina']))) : 1;
$inicio = ($paginaActual - 1) * $usuariosPorPagina;
$usuariosPagina = array_slice($usuarios, $inicio, $usuariosPorPagina);

$usuariosActivos = array_reduce($usuarios, function ($carry, $u) {
    return $carry + ($u->isActivo() ? 1 : 0);
}, 0);
$porcentajeActivos = $totalUsuarios > 0 ? round(($usuariosActivos / $totalUsuarios) * 100) : 0;
?>

<div class="usuarios-container">
    <!-- Tarjeta principal de la tabla -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <button id="btnNuevoUsuario" class="btn btn-sm btn-success me-2" onclick="showCreateUserForm()">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Usuario
            </button>
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
                    foreach ($usuariosPagina as $usuario) {
                        $badgeClass = $usuario->isActivo() ? 'bg-success' : 'bg-danger';
                        $badgeText = $usuario->isActivo() ? 'Activo' : 'Inactivo';
                        $rolBadgeClass = 'primary';

                        // Asignar colores según rol para mejor identificación visual
                        switch ($usuario->getRol()) {
                            case 'ADMINISTRADOR':
                                $rolBadgeClass = 'danger';
                                break;
                            case 'SUPERVISOR':
                                $rolBadgeClass = 'warning';
                                break;
                            case 'USUARIO':
                                $rolBadgeClass = 'info';
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
                                    <button type="button" class="list-table__button list-table__button--edit btn btn-sm"
                                            data-user-id="<?= $usuario->getIdUsuario() ?>"
                                            title="Editar usuario">
                                        <i class="fas fa-edit list-table__button-icon"></i> Editar
                                    </button>
                                    <button type="button"
                                            class="list-table__button list-table__button--locations btn btn-sm"
                                            data-user-id="<?= $usuario->getIdUsuario() ?>"
                                            title="Gestionar ubicaciones">
                                        <i class="fas fa-map-marker-alt list-table__button-icon"></i> Ubicaciones
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (count($usuariosPagina) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle me-2"></i>
                                No se encontraron usuarios registrados
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación manual -->
            <?php if ($totalPaginas > 1): ?>
                <div class="card-footer bg-white">
                    <nav aria-label="Paginación de usuarios" class="usuarios-pagination">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item<?= $paginaActual == 1 ? ' disabled' : '' ?>">
                                <a class="page-link" href="?pagina=1" tabindex="-1" aria-label="Primero">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item<?= $paginaActual == 1 ? ' disabled' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $paginaActual - 1 ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php
                            // Mostrar máximo 5 páginas alrededor de la actual
                            $start = max(1, $paginaActual - 2);
                            $end = min($totalPaginas, $paginaActual + 2);
                            if ($start > 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item<?= $i == $paginaActual ? ' active' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor;
                            if ($end < $totalPaginas) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            ?>
                            <li class="page-item<?= $paginaActual == $totalPaginas ? ' disabled' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $paginaActual + 1 ?>" aria-label="Siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <li class="page-item<?= $paginaActual == $totalPaginas ? ' disabled' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $totalPaginas ?>" aria-label="Último">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Función para mostrar el formulario de creación de usuario
    function showCreateUserForm() {
        const tabButton = document.querySelector('[data-tab="tab-crear-editar"]');
        if (tabButton) {
            tabButton.click();

            // Asegurarse de que se muestre el formulario de creación
            setTimeout(() => {
                const createToggleButton = document.querySelector('[data-target="crearForm"]');
                if (createToggleButton) {
                    createToggleButton.click();
                }
            }, 100);
        }
    }
</script>
