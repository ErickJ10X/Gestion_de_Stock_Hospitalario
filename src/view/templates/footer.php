</main>
<footer>
    <div>
        <p>&copy; <?php echo date('Y'); ?> Gestor de Usuarios.</p>
        <?php if (isset($_SESSION['usuario'])): ?>
            <small>Conectado como: <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></small>
        <?php endif; ?>
    </div>
</footer>
</body>
</html>