<?php
session_start();
require_once(__DIR__ . '/../../controller/ProductosController.php');
require_once(__DIR__ . '/../../util/Session.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\ProductosController;
use util\Session;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->requireAuth();

$productosController = new ProductosController();
$productos = $productosController->getAllProductos();

$session = new Session();

$pageTitle = "Gestión de Productos";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h2>Gestión de Productos</h2>
        </div>
        <div class="col text-end">
            <button id="btn-add-producto" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Producto
            </button>
        </div>
    </div>

    <?php if ($session->hasMessage('success')): ?>
        <div class="alert alert--success">
            <?php echo $session->getMessage('success'); ?>
            <button type="button" class="alert__close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($session->hasMessage('error')): ?>
        <div class="alert alert--danger">
            <?php echo $session->getMessage('error'); ?>
            <button type="button" class="alert__close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Listado de Productos</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Unidad de Medida</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay productos registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto->getId()); ?></td>
                                    <td><?php echo htmlspecialchars($producto->getCodigo()); ?></td>
                                    <td><?php echo htmlspecialchars($producto->getNombre()); ?></td>
                                    <td><?php echo htmlspecialchars($producto->getDescripcion()); ?></td>
                                    <td><?php echo htmlspecialchars($producto->getUnidadMedida()); ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btn-edit-producto" data-id="<?php echo $producto->getId(); ?>">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button class="btn btn-danger btn-sm btn-delete-producto" data-id="<?php echo $producto->getId(); ?>">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para el fondo oscurecido -->
<div class="producto-overlay"></div>

<!-- Tarjeta flotante para crear un nuevo producto -->
<div id="producto-card-create" class="producto-card">
    <div class="producto-card__header producto-card__header--create">
        <h3 class="producto-card__title">Nuevo Producto</h3>
        <button type="button" class="producto-card__close">&times;</button>
    </div>
    <div class="producto-card__body">
        <form id="create-producto-form" method="POST" action="procesar_producto.php">
            <input type="hidden" name="action" value="create">
            <div class="producto-form__group">
                <label for="codigo-create" class="producto-form__label">Código</label>
                <input type="text" class="producto-form__input" id="codigo-create" name="codigo" required>
            </div>
            <div class="producto-form__group">
                <label for="nombre-create" class="producto-form__label">Nombre</label>
                <input type="text" class="producto-form__input" id="nombre-create" name="nombre" required>
            </div>
            <div class="producto-form__group">
                <label for="descripcion-create" class="producto-form__label">Descripción</label>
                <textarea class="producto-form__input" id="descripcion-create" name="descripcion" rows="3" required></textarea>
            </div>
            <div class="producto-form__group">
                <label for="unidad_medida-create" class="producto-form__label">Unidad de Medida</label>
                <input type="text" class="producto-form__input" id="unidad_medida-create" name="unidad_medida" required>
            </div>
            <div class="producto-card__footer">
                <button type="button" class="producto-form__button producto-form__button--secondary producto-form__button--cancel">Cancelar</button>
                <button type="submit" class="producto-form__button producto-form__button--primary">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

<!-- Tarjetas flotantes para editar y eliminar productos -->
<?php if (!empty($productos)): ?>
    <?php foreach ($productos as $producto): ?>
        <div id="producto-card-edit-<?php echo $producto->getId(); ?>" class="producto-card">
            <div class="producto-card__header producto-card__header--edit">
                <h3 class="producto-card__title">Editar Producto</h3>
                <button type="button" class="producto-card__close">&times;</button>
            </div>
            <div class="producto-card__body">
                <form id="edit-producto-form-<?php echo $producto->getId(); ?>" method="POST" action="procesar_producto.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $producto->getId(); ?>">
                    <div class="producto-form__group">
                        <label for="codigo-edit-<?php echo $producto->getId(); ?>" class="producto-form__label">Código</label>
                        <input type="text" class="producto-form__input" id="codigo-edit-<?php echo $producto->getId(); ?>" name="codigo" value="<?php echo htmlspecialchars($producto->getCodigo()); ?>" required>
                    </div>
                    <div class="producto-form__group">
                        <label for="nombre-edit-<?php echo $producto->getId(); ?>" class="producto-form__label">Nombre</label>
                        <input type="text" class="producto-form__input" id="nombre-edit-<?php echo $producto->getId(); ?>" name="nombre" value="<?php echo htmlspecialchars($producto->getNombre()); ?>" required>
                    </div>
                    <div class="producto-form__group">
                        <label for="descripcion-edit-<?php echo $producto->getId(); ?>" class="producto-form__label">Descripción</label>
                        <textarea class="producto-form__input" id="descripcion-edit-<?php echo $producto->getId(); ?>" name="descripcion" rows="3" required><?php echo htmlspecialchars($producto->getDescripcion()); ?></textarea>
                    </div>
                    <div class="producto-form__group">
                        <label for="unidad_medida-edit-<?php echo $producto->getId(); ?>" class="producto-form__label">Unidad de Medida</label>
                        <input type="text" class="producto-form__input" id="unidad_medida-edit-<?php echo $producto->getId(); ?>" name="unidad_medida" value="<?php echo htmlspecialchars($producto->getUnidadMedida()); ?>" required>
                    </div>
                    <div class="producto-card__footer">
                        <button type="button" class="producto-form__button producto-form__button--secondary producto-form__button--cancel">Cancelar</button>
                        <button type="submit" class="producto-form__button producto-form__button--primary">Actualizar Producto</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="producto-card-delete-<?php echo $producto->getId(); ?>" class="producto-card">
            <div class="producto-card__header producto-card__header--delete">
                <h3 class="producto-card__title">Eliminar Producto</h3>
                <button type="button" class="producto-card__close">&times;</button>
            </div>
            <div class="producto-card__body">
                <h4>¿Estás seguro de que deseas eliminar el producto "<?php echo htmlspecialchars($producto->getNombre()); ?>"?</h4>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <form id="delete-producto-form-<?php echo $producto->getId(); ?>" method="POST" action="procesar_producto.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $producto->getId(); ?>">
                    <div class="producto-card__footer">
                        <button type="button" class="producto-form__button producto-form__button--secondary producto-form__button--cancel">Cancelar</button>
                        <button type="submit" class="producto-form__button producto-form__button--danger">Confirmar Eliminación</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include(__DIR__ . '/../templates/footer.php'); ?>

<!-- Scripts después del footer -->
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/producto-cards.js"></script>
