<?php
if (!isset($pactos) || !isset($session) || !isset($productos)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Función para obtener el nombre del producto por ID
function getProductoNombreById($idProducto, $productos) {
    foreach ($productos as $producto) {
        if ($producto->getIdProducto() == $idProducto) {
            return $producto->getNombre();
        }
    }
    return "Producto no encontrado";
}

// Función para obtener el nombre del tipo de ubicación
function getTipoUbicacionNombre($tipo) {
    if ($tipo == 'almacen') {
        return "Almacén";
    } elseif ($tipo == 'botiquin') {
        return "Botiquín";
    }
    return $tipo;
}

// Función para obtener el nombre del destino (implementar según lógica de negocio)
function getDestinoNombre($idDestino, $tipo) {
    // Aquí deberías buscar el nombre del destino según su tipo y ID
    // Por ahora, devolvemos un valor genérico
    return "Destino #" . $idDestino;
}
?>

<div class="table-responsive">
    <table class="list-table" id="tablaPactos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Tipo Ubicación</th>
                <th>Destino</th>
                <th>Cantidad Pactada</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pactos)): ?>
                <tr>
                    <td colspan="5" class="list-table__empty">No hay pactos registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($pactos as $pacto): ?>
                    <tr class="list-table__body-row">
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($pacto->getIdPacto()) ?></td>
                        <td class="list-table__body-cell" data-label="Producto">
                            <?= htmlspecialchars(getProductoNombreById($pacto->getIdProducto(), $productos)) ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Tipo Ubicación">
                            <?= htmlspecialchars(getTipoUbicacionNombre($pacto->getTipoUbicacion())) ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Destino">
                            <?= htmlspecialchars(getDestinoNombre($pacto->getIdDestino(), $pacto->getTipoUbicacion())) ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Cantidad Pactada">
                            <?= htmlspecialchars($pacto->getCantidadPactada()) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


