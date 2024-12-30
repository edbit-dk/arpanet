        <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
        if (typeof jQuery === 'undefined') {
            document.write('<script src="<?php base_url() ?>/js/jquery-3.7.1.min.js"><\/script>');
        }
        </script>
        <script src="<?php base_url() ?>/js/app.min.js?v=<?php echo($_SESSION['hash']) ?>"></script>
    </body>
</html>