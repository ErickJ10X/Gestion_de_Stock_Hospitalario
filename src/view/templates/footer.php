</main>
<footer class="footer">
    <div class="container footer__container">
        <p class="footer__copyright">&copy; <?php echo date('Y'); ?> Pegasus Medical - Gestión de Stock Hospitalario.</p>
        <?php if (isset($_SESSION['id'])): ?>
            <small class="footer__user-info">Conectado como: <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong> 
            (<?php echo htmlspecialchars($_SESSION['rol']); ?>)</small>
        <?php endif; ?>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            mainNav.classList.toggle('nav--active');
        });
    }
    
    const closeButtons = document.querySelectorAll('.alert__close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.parentElement;
            alert.style.display = 'none';
        });
    });
});
</script>
</body>
</html>
