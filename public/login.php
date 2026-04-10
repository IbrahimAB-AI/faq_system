<?php
/**
 * Login Page - FAQ System
 * Modern dark-themed design
 */

$pageTitle = 'Login';
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    $redirect = $_GET['redirect'] ?? 'index.php';
    redirect($redirect);
}

$error = null;
$success = null;

if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $success = 'Registration successful! Please login.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $result = validateLogin($email, $password);
        
        if ($result['success']) {
            loginUser($result['user']['user_id'], $result['user']['username'], $result['user']['role']);
            $redirect = $_POST['redirect'] ?? 'index.php';
            redirect($redirect);
        } else {
            $error = $result['error'];
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card p-4 p-lg-5 rounded-4 border-0 shadow-lg">
            <div class="text-center mb-4">
                <div class="auth-icon mb-3">
                    <i class="bi bi-box-arrow-in-right"></i>
                </div>
                <h2 class="h3 mb-2">Welcome Back</h2>
                <p class="text-muted">Sign in to access your account</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= h($error) ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-check-circle me-2"></i>
                <?= h($success) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="redirect" value="<?= h($_GET['redirect'] ?? '') ?>">
                
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-envelope text-muted"></i>
                        </span>
                        <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="you@example.com" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-lock text-muted"></i>
                        </span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Enter your password" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted mb-2">Don't have an account? 
                    <a href="register.php" class="text-primary fw-semibold">Create one</a>
                </p>
            </div>
            
            <!-- Demo Credentials -->
            <div class="demo-box mt-4 p-3 rounded-3">
                <p class="small text-muted mb-2">
                    <i class="bi bi-info-circle me-1"></i>Demo Accounts
                </p>
                <div class="row g-2 small">
                    <div class="col-6">
                        <strong>Admin:</strong><br>
                        <code>admin@faqsystem.com</code>
                    </div>
                    <div class="col-6">
                        <strong>User:</strong><br>
                        <code>john@example.com</code>
                    </div>
                </div>
                <p class="mb-0 mt-2 text-muted">Password: <code>password123</code></p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

<style>
.auth-page {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    padding: 2rem 0;
}

.auth-container { width: 100%; max-width: 440px; }

.auth-card { background: white; }

.auth-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.toggle-password { border-color: #dee2e6; }

.demo-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}

code {
    background: #e9ecef;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    font-size: 0.85em;
}
</style>

<?php require_once 'includes/footer.php'; ?>
```
