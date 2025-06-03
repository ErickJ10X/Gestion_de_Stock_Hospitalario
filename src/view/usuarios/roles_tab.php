<?php

use src\enum\RolEnum;

if (!isset($usuarios) || !method_exists('src\enum\RolEnum', 'getValues')) {
    die("Error: No se han proporcionado las variables o clases requeridas.");
}
?>

<div class="list-header__actions mb-4">
    <h3 class="section-title">Distribución de Usuarios por Rol</h3>
</div>

<div class="roles-grid">
    <?php
    $roleUsers = [];
    foreach (RolEnum::getValues() as $rol) {
        $roleUsers[$rol] = [];
    }
    foreach ($usuarios as $usuario) {
        $rol = $usuario->getRol();
        if (isset($roleUsers[$rol])) {
            $roleUsers[$rol][] = $usuario;
        }
    }
    foreach ($roleUsers as $rol => $users):
        $badgeColor = getBadgeColorForRole($rol);
        ?>
        <div class="role-card">
            <div class="role-card-header bg-<?= $badgeColor ?>">
                <?= htmlspecialchars($rol) ?>
                <span class="role-count"><?= count($users) ?></span>
            </div>
            <div class="role-card-body">
                <?php if (empty($users)): ?>
                    <div class="role-empty">No hay usuarios con este rol</div>
                <?php else: ?>
                    <ul class="role-users-list">
                        <?php foreach ($users as $usuario): ?>
                            <li class="role-user-item">
                                <div class="role-user-name">
                                    <i class="fas fa-user"></i>
                                    <?= htmlspecialchars($usuario->getNombre()) ?>
                                </div>
                                <div class="role-user-email"><?= htmlspecialchars($usuario->getEmail()) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
