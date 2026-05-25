    <!-- JavaScript -->
    <?php if (basename($_SERVER['PHP_SELF']) === 'results.php'): ?>
    <script src="/assets/js/share.js"></script>
    <?php endif; ?>

    <!-- PWA Service Worker -->
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/pwa/sw.js')
            .then(registration => {
                console.log('Bayani World: Service Worker registered');
                // Force update on page load
                registration.update();
            })
            .catch(err => console.log('SW registration failed:', err));
    }
    </script>
</body>
</html>
