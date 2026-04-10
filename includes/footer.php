<?php
/**
 * Footer Include
 * FAQ System
 */

// Get baseUrl if not already defined
if (!isset($baseUrl)) {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') 
        . '://' . $_SERVER['HTTP_HOST'] 
        . dirname($_SERVER['REQUEST_URI']) . '/';
    if (substr($baseUrl, -1) !== '/') {
        $baseUrl .= '/';
    }
}
?>
    </main>

    <!-- AI Agent Chat Button -->
    <button class="agent-toggle-btn" id="agentToggleBtn" title="Chat with AI Assistant">
        <i class="bi bi-chat-dots-fill"></i>
    </button>

    <!-- AI Agent Chat Drawer -->
    <div class="agent-drawer" id="agentDrawer">
        <div class="agent-drawer-header">
            <h6 class="mb-0">
                <i class="bi bi-robot"></i> AI Assistant
            </h6>
            <button class="btn-close-white" id="agentCloseBtn"></button>
        </div>
        <div class="agent-drawer-body" id="agentMessages">
            <div class="agent-message agent-message-bot">
                <div class="agent-avatar">
                    <i class="bi bi-robot"></i>
                </div>
                <div class="agent-bubble">
                    Hi! I'm your programming assistant. Ask me anything!
                </div>
            </div>
        </div>
        <div class="agent-drawer-footer">
            <form id="agentForm" class="d-flex">
                <input type="text" class="form-control" id="agentInput" placeholder="Type your question..." autocomplete="off">
                <button type="submit" class="btn btn-primary ms-2">
                    <i class="bi bi-send"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>FAQ System</h5>
                    <p class="text-muted mb-0">Helping beginners learn programming since 2024</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">&copy; <?= date('Y') ?> FAQ System. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= htmlspecialchars($baseUrl) ?>assets/js/main.js"></script>
    <?php if (file_exists(__DIR__ . '/../assets/js/agent.js')): ?>
    <script src="<?= htmlspecialchars($baseUrl) ?>assets/js/agent.js"></script>
    <?php endif; ?>
</body>
</html>
```

---

Copy this into your `includes/footer.php` file. It includes the AI chat drawer, floating button, footer, and all JavaScript includes!