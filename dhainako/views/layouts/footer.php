    </div>
    
    <!-- Custom Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmModalBody">
                    <!-- Message will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmModalBtn">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    $config = require __DIR__ . '/../../config/app.php';
    $baseUrl = rtrim($config['base_url'], '/');
    // Fallback to relative path if base_url is not set correctly
    if (empty($baseUrl) || $baseUrl === 'http://' || $baseUrl === 'https://') {
        $baseUrl = '/';
    }
    ?>
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/script.js"></script>
    <?php
    if (!empty($additionalScripts)) {
        $scripts = is_array($additionalScripts) ? $additionalScripts : [$additionalScripts];
        foreach ($scripts as $scriptSrc) {
            if (!empty($scriptSrc)) {
                echo '<script src="' . htmlspecialchars($scriptSrc) . '"></script>';
            }
        }
    }

    if (!empty($additionalInlineScripts)) {
        $inlineScripts = is_array($additionalInlineScripts) ? $additionalInlineScripts : [$additionalInlineScripts];
        foreach ($inlineScripts as $inlineScript) {
            if (!empty($inlineScript)) {
                echo '<script>' . $inlineScript . '</script>';
            }
        }
    }
    ?>
</body>
</html>

