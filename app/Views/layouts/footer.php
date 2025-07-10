<?php if (isset($_SESSION['user_id'])): ?>
            </main>
        </div>
    <?php endif; ?>

    <footer class="mt-8 p-4 text-center text-gray-600">
        <p>&copy; <?php echo date('Y'); ?> Facturation. Tous droits réservés.</p>
    </footer>
    <script src="<?php echo BASE_URL; ?>/public/js/main.js"></script>
</body>
</html>