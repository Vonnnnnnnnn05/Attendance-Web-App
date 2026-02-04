    </div> <!-- End Main Container -->

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container text-center">
            <span>&copy; <?php echo date('Y'); ?> Student Management System. All rights reserved.</span>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/amsp/assets/js/script.js"></script>
    
    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                // Get the base path dynamically
                const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
                const swPath = basePath ? basePath + '/service-worker.js' : './service-worker.js';
                
                navigator.serviceWorker.register(swPath)
                    .then(registration => {
                        console.log('Service Worker registered successfully:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Service Worker registration failed:', error);
                    });
            });
        }
    </script>
</body>
</html>
