</main>
<footer>
    <div>
        <p>&copy; <?php echo date('Y'); ?> Pegasus Medical - Gestión de Stock Hospitalario.</p>
        <?php if (isset($_SESSION['id'])): ?>
            <small>Conectado como: <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong> 
            (<?php echo htmlspecialchars($_SESSION['rol']); ?>)</small>
        <?php endif; ?>
    </div>
</footer>
</body>
</html>
