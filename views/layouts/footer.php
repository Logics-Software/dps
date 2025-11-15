    <?php
    $config = require __DIR__ . '/../../config/app.php';
    $baseUrl = rtrim($config['base_url'], '/');
    if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
        $baseUrl = '/';
    }
    ?>
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/bootstrap.bundle.min.js"></script>
    <?php
    if (!empty($additionalScripts)) {
        $scripts = is_array($additionalScripts) ? $additionalScripts : [$additionalScripts];
        foreach ($scripts as $scriptSrc) {
            if (!empty($scriptSrc)) {
                echo '<script src="' . htmlspecialchars($scriptSrc) . '"></script>';
            }
        }
    }
    ?>
    <script>
    // Initialize toast messages
    document.addEventListener('DOMContentLoaded', function() {
        const toastElements = document.querySelectorAll('.toast');
        toastElements.forEach(function(toastEl) {
            // Set delay based on type: 5 seconds for success, 7 seconds for error
            const isError = toastEl.id.includes('error') || toastEl.id.includes('danger');
            const delay = isError ? 7000 : 5000;
            
            // Set custom delay
            toastEl.setAttribute('data-bs-delay', delay);
            
            // Initialize and show toast
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: delay
            });
            toast.show();
        });
    });

    // Confirm delete function
    function confirmDelete(message, url) {
        if (confirm(message)) {
            window.location.href = url;
        }
        return false;
    }
    </script>
    <?php
    if (!empty($additionalInlineScripts)) {
        $scripts = is_array($additionalInlineScripts) ? $additionalInlineScripts : [$additionalInlineScripts];
        foreach ($scripts as $script) {
            if (!empty($script)) {
                echo $script;
            }
        }
    }
    ?>
</body>
</html>

