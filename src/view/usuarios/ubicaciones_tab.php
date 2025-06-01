<?php
if (!isset($usuarios) || !isset($usuarioController) || !isset($usuarioUbicacionController)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Cargar datos de usuario-ubicaciones 
try {
    $ubicacionesResult = $usuarioUbicacionController->getAllUsuarioUbicaciones();
    $usuario_ubicaciones = $ubicacionesResult['success'] ? $ubicacionesResult['data'] : [];
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error al cargar los datos: " . $e->getMessage() . "</div>";
    $usuario_ubicaciones = [];
}
?>

<div class="ubicaciones-container">
    <div class="list-header__actions mb-4">
        <button class="list-button list-button--success usuario-card-open" data-target="asignar-ubicacion-modal">
            <i class="fas fa-plus"></i> Asignar Ubicación a Usuario
        </button>
    </div>

    <div class="card shadow">
        <div class="section-title bg-primary text-white">
            Ubicaciones de Usuarios
        </div>
        <div class="table-responsive">
            <table class="list-table" id="ubicacionesUsuariosDataTable">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Tipo Ubicación</th>
                        <th>ID Ubicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Preparar datos para la tabla
                    if (!empty($usuario_ubicaciones)) {
                        foreach ($usuario_ubicaciones as $relacion) {
                            $idUsuario = $relacion->getIdUsuario();
                            $tipoUbicacion = $relacion->getTipoUbicacion();
                            $idUbicacion = $relacion->getIdUbicacion();
                            
                            // Obtener datos del usuario
                            $usuarioData = $usuarioController->getUserById($idUsuario);
                            $usuario = $usuarioData['success'] ? $usuarioData['data'] : null;
                            
                            // Si el usuario no existe, continuar con el siguiente
                            if (!$usuario) continue;
                            ?>
                            <tr class="list-table__body-row">
                                <td class="list-table__body-cell"><?= htmlspecialchars($usuario->getNombre()) ?></td>
                                <td class="list-table__body-cell"><?= htmlspecialchars($usuario->getEmail()) ?></td>
                                <td class="list-table__body-cell">
                                    <span class="badge bg-<?= getTipoBadgeClass($tipoUbicacion) ?>">
                                        <?= htmlspecialchars(getTipoUbicacionLabel($tipoUbicacion)) ?>
                                    </span>
                                </td>
                                <td class="list-table__body-cell"><?= $idUbicacion ?></td>
                                <td class="list-table__body-cell">
                                    <div class="list-table__actions">
                                        <button class="list-table__button list-table__button--edit usuario-card-open" 
                                            data-target="editar-ubicacion-modal" 
                                            data-id-usuario="<?= $idUsuario ?>"
                                            data-tipo-ubicacion="<?= htmlspecialchars($tipoUbicacion) ?>"
                                            data-id-ubicacion="<?= $idUbicacion ?>"
                                            title="Editar">
                                            <i class="fas fa-edit list-table__button-icon"></i> Editar
                                        </button>
                                        <button type="button" class="list-table__button list-table__button--delete usuario-card-open" 
                                                data-target="eliminar-ubicacion-modal"
                                                data-id-usuario="<?= $idUsuario ?>" 
                                                data-tipo-ubicacion="<?= htmlspecialchars($tipoUbicacion) ?>" 
                                                data-id-ubicacion="<?= $idUbicacion ?>"
                                                data-nombre-usuario="<?= htmlspecialchars($usuario->getNombre()) ?>"
                                                title="Eliminar">
                                            <i class="fas fa-trash list-table__button-icon"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    
                    if (empty($usuario_ubicaciones)) {
                        echo '<tr class="list-table__empty"><td colspan="5">No hay asignaciones de ubicaciones a usuarios</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para asignar ubicación a usuario -->
<div id="asignar-ubicacion-modal" class="usuario-card">
    <div class="usuario-card__header usuario-card__header--create">
        <h3 class="usuario-card__title">Asignar Ubicación a Usuario</h3>
        <button type="button" class="usuario-card__close">&times;</button>
    </div>
    <div class="usuario-card__body">
        <form id="form-asignar-ubicacion" method="post" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/ubicacion-actions.php" class="usuario-form">
            <input type="hidden" name="action" value="asignar">
            
            <div class="usuario-form__group">
                <label for="usuario_id" class="usuario-form__label">Usuario:</label>
                <select id="usuario_id" name="usuario_id" class="usuario-form__select" required>
                    <option value="">Seleccione un usuario</option>
                    <?php foreach ($usuarios as $user): ?>
                    <option value="<?= $user->getIdUsuario() ?>"><?= htmlspecialchars($user->getNombre()) ?> (<?= htmlspecialchars($user->getEmail()) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="usuario-form__group">
                <label for="tipo_ubicacion" class="usuario-form__label">Tipo de ubicación:</label>
                <select id="tipo_ubicacion" name="tipo_ubicacion" class="usuario-form__select" required>
                    <option value="hospital">Hospital</option>
                    <option value="planta">Planta</option>
                    <option value="botiquin">Botiquín</option>
                </select>
            </div>
            
            <div class="usuario-form__group">
                <label for="ubicacion_id" class="usuario-form__label">ID de Ubicación:</label>
                <input type="number" id="ubicacion_id" name="ubicacion_id" class="usuario-form__input" required min="1">
            </div>
            
            <div class="usuario-card__footer">
                <button type="button" class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">
                    Cancelar
                </button>
                <button type="submit" class="usuario-form__button usuario-form__button--primary">
                    Asignar Ubicación
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para editar asignación -->
<div id="editar-ubicacion-modal" class="usuario-card">
    <div class="usuario-card__header usuario-card__header--edit">
        <h3 class="usuario-card__title">Editar Asignación de Ubicación</h3>
        <button type="button" class="usuario-card__close">&times;</button>
    </div>
    <div class="usuario-card__body">
        <form id="form-editar-ubicacion" method="post" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/ubicacion-actions.php" class="usuario-form">
            <input type="hidden" name="action" value="editar">
            <input type="hidden" id="edit_usuario_id" name="usuario_id" value="">
            <input type="hidden" id="edit_tipo_ubicacion_original" name="tipo_ubicacion_original" value="">
            <input type="hidden" id="edit_ubicacion_id_original" name="ubicacion_id_original" value="">
            
            <div class="usuario-form__group">
                <label for="edit_tipo_ubicacion" class="usuario-form__label">Tipo de ubicación:</label>
                <select id="edit_tipo_ubicacion" name="tipo_ubicacion" class="usuario-form__select" required>
                    <option value="hospital">Hospital</option>
                    <option value="planta">Planta</option>
                    <option value="botiquin">Botiquín</option>
                </select>
            </div>
            
            <div class="usuario-form__group">
                <label for="edit_ubicacion_id" class="usuario-form__label">ID de Ubicación:</label>
                <input type="number" id="edit_ubicacion_id" name="ubicacion_id" class="usuario-form__input" required min="1">
            </div>
            
            <div class="usuario-card__footer">
                <button type="button" class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">
                    Cancelar
                </button>
                <button type="submit" class="usuario-form__button usuario-form__button--primary">
                    Actualizar Asignación
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de confirmación para eliminar asignación -->
<div id="eliminar-ubicacion-modal" class="usuario-card">
    <div class="usuario-card__header usuario-card__header--delete">
        <h3 class="usuario-card__title">Eliminar Asignación</h3>
        <button type="button" class="usuario-card__close">&times;</button>
    </div>
    <div class="usuario-card__body">
        <h4>¿Estás seguro de que deseas eliminar esta asignación de ubicación?</h4>
        <p>Usuario: <strong id="delete-usuario-nombre"></strong></p>
        <p>Tipo: <strong id="delete-tipo-ubicacion-texto"></strong></p>
        <p>ID de Ubicación: <strong id="delete-ubicacion-id"></strong></p>
        <p class="text-danger">Esta acción no se puede deshacer.</p>
        
        <form id="form-eliminar-ubicacion" method="post" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/ubicacion-actions.php">
            <input type="hidden" name="action" value="eliminar">
            <input type="hidden" id="delete_usuario_id" name="usuario_id" value="">
            <input type="hidden" id="delete_tipo_ubicacion" name="tipo_ubicacion" value="">
            <input type="hidden" id="delete_ubicacion_id" name="ubicacion_id" value="">
            
            <div class="usuario-card__footer">
                <button type="button" class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">
                    Cancelar
                </button>
                <button type="submit" class="usuario-form__button usuario-form__button--danger">
                    Eliminar Asignación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable si está disponible
    if (typeof $.fn.DataTable !== 'undefined') {
        try {
            $('#ubicacionesUsuariosDataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                },
                responsive: true,
                order: [[0, 'asc']]
            });
        } catch (error) {
            console.error('Error al inicializar DataTable:', error);
        }
    } else {
        console.warn('DataTable no está disponible para ubicaciones');
    }
});
</script>

<?php
// Funciones auxiliares
function getTipoBadgeClass($tipo) {
    switch ($tipo) {
        case 'hospital':
            return 'primary';
        case 'planta':
            return 'success';
        case 'botiquin':
            return 'warning';
        default:
            return 'secondary';
    }
}

function getTipoUbicacionLabel($tipo) {
    switch ($tipo) {
        case 'hospital':
            return 'Hospital';
        case 'planta':
            return 'Planta';
        case 'botiquin':
            return 'Botiquín';
        default:
            return ucfirst($tipo);
    }
}
?>
