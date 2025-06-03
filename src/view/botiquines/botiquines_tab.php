<?php
if (!isset($hospitales) || !isset($plantas) || !isset($botiquines) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="table-responsive">
    <table class="list-table">
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
        <?php if (empty($botiquines)): ?>
            <tr>
                <td colspan="5" class="list-table__empty">No hay botiquines registrados</td>
            </tr>
        <?php else: ?>
            <?php foreach ($botiquines as $botiquin):
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
                <tr class="list-table__body-row">
                    <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($botiquin->getIdBotiquines()) ?></td>
                    <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($botiquin->getNombre()) ?></td>
                    <td class="list-table__body-cell" data-label="Planta">
                        <?= $plantaBotiquin ? htmlspecialchars($plantaBotiquin->getNombre()) : 'N/A' ?>
                    </td>
                    <td class="list-table__body-cell" data-label="Hospital">
                        <?= $hospitalBotiquin ? htmlspecialchars($hospitalBotiquin->getNombre()) : 'N/A' ?>
                    </td>
                    <td class="list-table__body-cell" data-label="Acciones">
                        <div class="list-table__actions">
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquin/?id=<?= $botiquin->getIdBotiquines() ?>" class="list-table__button list-table__button--info">
                                <i class="bi bi-eye list-table__button-icon"></i> Ver
                            </a>
                            <button class="list-table__button list-table__button--edit" 
                                    onclick="seleccionarBotiquin(<?= $botiquin->getIdBotiquines() ?>)">
                                <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function seleccionarBotiquin(id) {
        // Cambiar a la pestaña de edición
        document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]').click();
        
        // Esperar un momento para que la pestaña se muestre
        setTimeout(() => {
            // Seleccionar el botiquín en el dropdown
            const selectBotiquin = document.getElementById('select_botiquin');
            selectBotiquin.value = id;
            
            // Disparar el evento change para cargar los datos
            const event = new Event('change');
            selectBotiquin.dispatchEvent(event);
            
            // Hacer scroll al formulario de edición
            document.getElementById('editar_botiquin_form_container').scrollIntoView({
                behavior: 'smooth'
            });
        }, 100);
    }
</script>
