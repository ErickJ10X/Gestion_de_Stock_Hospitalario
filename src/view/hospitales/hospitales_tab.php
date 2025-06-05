<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="table-responsive">
    <table class="list-table">
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
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($hospital->getIdHospital()) ?></td>
                        <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($hospital->getNombre()) ?></td>
                        <td class="list-table__body-cell" data-label="Plantas">
                            <?php
                            $plantasHospital = $plantaController->getByHospital($hospital->getIdHospital())['plantas'] ?? [];
                            $cantidadPlantas = count($plantasHospital);
                            ?>
                            <span class="badge bg-info"><?= $cantidadPlantas ?> plantas</span>
                        </td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit btn-edit-hospital" data-id="<?= $hospital->getIdHospital() ?>">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete btn-delete-hospital" data-id="<?= $hospital->getIdHospital() ?>">
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
