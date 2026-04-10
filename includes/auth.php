<?php
/**
 * Authentication Include
 * FAQ System
 * 
 * Handles session management, login validation, CSRF protection, and role-based access
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate CSRF token
 * @return string
 */
function csrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * @param string $token
 * @return bool
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user info
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return fetchOne(
        "SELECT user_id, username, email, role FROM users WHERE user_id = ?",
        [$_SESSION['user_id']]
    );
}

/**
 * Check if current user is admin
 * @return bool
 */
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

/**
 * Require login - redirect to login if not authenticated
 * @return void
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Require admin role - redirect to home if not admin
 * @return void
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../index.php');
        exit;
    }
}

/**
 * Login user
 * @param int $userId
 * @param string $username
 * @param string $role
 * @return void
 */
function loginUser($userId, $username, $role) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['login_time'] = time();
    
    session_regenerate_id(true);
}

/**
 * Logout user
 * @return void
 */
function logoutUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION = [];
    session_destroy();
    
    // Clear session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
}

/**
 * Validate login credentials
 * @param string $email
 * @param string $password
 * @return array ['success' => bool, 'user' => array|null, 'error' => string]
 */
function validateLogin($email, $password) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'user' => null, 'error' => 'Invalid email format'];
    }
    
    try {
        $user = fetchOne(
            "SELECT user_id, username, email, password_hash, role FROM users WHERE email = ?",
            [$email]
        );
        
        if (!$user) {
            return ['success' => false, 'user' => null, 'error' => 'Invalid email or password'];
        }
        
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'user' => null, 'error' => 'Invalid email or password'];
        }
        
        if (password_get_info($user['password_hash'])['algo'] === 0) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            executeQuery("UPDATE users SET password_hash = ? WHERE user_id = ?", [$newHash, $user['user_id']]);
        }
        
        return ['success' => true, 'user' => $user, 'error' => null];
    } catch (Exception $e) {
        error_log("Login validation error: " . $e->getMessage());
        return ['success' => false, 'user' => null, 'error' => 'Login failed. Please try again.'];
    }
}

/**
 * Register new user
 * @param string $username
 * @param string $email
 * @param string $password
 * @return array ['success' => bool, 'user_id' => int|null, 'error' => string]
 */
function registerUser($username, $email, $password) {
    $username = trim($username);
    if (strlen($username) < 3 || strlen($username) > 50) {
        return ['success' => false, 'user_id' => null, 'error' => 'Username must be between 3 and 50 characters'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return ['success' => false, 'user_id' => null, 'error' => 'Username can only contain letters, numbers, and underscores'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'user_id' => null, 'error' => 'Invalid email format'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'user_id' => null, 'error' => 'Password must be at least 6 characters'];
    }
    
    // Check for existing users
    try {
        $existing = fetchOne("SELECT user_id FROM users WHERE username = ?", [$username]);
        if ($existing) {
            return ['success' => false, 'user_id' => null, 'error' => 'Username already taken'];
        }
        
        $existing = fetchOne("SELECT user_id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            return ['success' => false, 'user_id' => null, 'error' => 'Email already registered'];
        }
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        executeQuery("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')", [$username, $email, $passwordHash]);
        return ['success' => true, 'user_id' => getLastInsertId(), 'error' => null];
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'user_id' => null, 'error' => 'Registration failed. Please try again.'];
    }
}

/**
 * Sanitize output - prevent XSS
 * @param string $str
 * @return string
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
