    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> ShareMyRide - Administration - Tous droits réservés</p>
        </div>
    </footer>
    </div> <!-- Fermeture de main-content -->

    <!-- Script pour le toggle de la barre latérale en mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                });
            }
        });
    </script>
</body>
</html> 