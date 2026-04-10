<?php
/**
 * Register Page - FAQ System
 * Modern dark-themed design
 */

$pageTitle = 'Register';
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($username) || empty($email) || empty($password)) {
            $error = 'All fields are required.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            $result = registerUser($username, $email, $password);
            
            if ($result['success']) {
                redirect('login.php?registered=1');
            } else {
                $error = $result['error'];
            }
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card p-4 p-lg-5 rounded-4 border-0 shadow-lg">
            <div class="text-center mb-4">
                <div class="auth-icon mb-3">
                    <i class="bi bi-person-plus"></i>
                </div>
                <h2 class="h3 mb-2">Create Account</h2>
                <p class="text-muted">Join our programming community</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= h($error) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="register.php">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-at text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="username" name="username" 
                               placeholder="Choose a username" minlength="3" maxlength="50" 
                               pattern="[a-zA-Z0-9_]+" title="Letters, numbers, underscores only" required>
                    </div>
                    <small class="text-muted">3-50 characters (letters, numbers, underscores)</small>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-envelope text-muted"></i>
                        </span>
                        <input type="email" class="form-control border-start-0" id="email" name="email" 
                               placeholder="you@example.com" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                                                        <i class="bi bi-lock text-muted"></i>
                        </span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" 
                               placeholder="Create a password" minlength="6" required>
                    </div>
                    <small class="text-muted">At least 6 characters</small>
                </div>
                
                <div class="mb-4">
                    <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-lock-fill text-muted"></i>
                        </span>
                        <input type="password" class="form-control border-start-0" id="confirm_password" 
                               name="confirm_password" placeholder="Confirm your password" required>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted mb-0">Already have an account? 
                    <a href="login.php" class="text-primary fw-semibold">Sign in</a>
                </p>
            </div>
            
            <div class="terms-note mt-4 p-3 rounded-3">
                <p class="small text-muted mb-0">
                    <i class="bi bi-shield-check me-1"></i>
                    By creating an account, you agree to our terms and privacy policy.
                </p>
            </div>
        </div>
    </div>
</div>

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

.terms-note {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}
</style>

<?php require_once 'includes/footer.php'; ?>
