<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($plantas) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="table-responsive">
    <table class="list-table">
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

