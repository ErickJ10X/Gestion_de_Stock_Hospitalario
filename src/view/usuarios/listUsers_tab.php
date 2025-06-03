<?php

use model\enum\RolEnum;

if (!isset($usuarios)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="usuarios-container">
    <div class="card shadow">
        <div class="section-title bg-primary text-white">
            Lista de Usuarios
        </div>
        <div class="table-responsive">
            <table class="list-table" id="usuariosDataTable">
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
                        $badgeClass = $usuario->getActivo() ? 'bg-success' : 'bg-danger';
                        $badgeText = $usuario->getActivo() ? 'Activo' : 'Inactivo';
                        $rolBadgeClass = 'primary'; // Color predeterminado para roles
                        ?>
                        <tr class="list-table__body-row">
                            <td class="list-table__body-cell"><?= $usuario->getIdUsuario() ?></td>
                            <td class="list-table__body-cell"><?= htmlspecialchars($usuario->getNombre()) ?></td>
                            <td class="list-table__body-cell"><?= htmlspecialchars($usuario->getEmail()) ?></td>
                            <td class="list-table__body-cell">
                                <span class="badge bg-<?= $rolBadgeClass ?>"><?= htmlspecialchars($usuario->getRol()) ?></span>
                            </td>
                            <td class="list-table__body-cell">
                                <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                            </td>
                            <td class="list-table__body-cell">
                                <div class="list-table__actions">
                                    <button class="list-table__button list-table__button--edit" title="Editar">
                                        <i class="fas fa-edit list-table__button-icon"></i> Editar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando script en usuarios_tab.php...');

    // Inicializar DataTable si está disponible
    if (typeof $.fn.DataTable !== 'undefined') {
        try {
            $('#usuariosDataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                },
                responsive: true,
                order: [[0, 'asc']]
            });
            console.log('DataTable inicializado correctamente');
        } catch (error) {
            console.error('Error al inicializar DataTable:', error);
        }
    } else {
        console.warn('DataTable no está disponible');
    }
});
</script>
