<?php
/**
 * Header Include
 * FAQ System
 * 
 * Session management and common header for all pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database config
require_once __DIR__ . '/../config/db.php';

// Include functions (provides $baseUrl and helper functions)
require_once __DIR__ . '/../includes/functions.php';

// Get current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Get user info if logged in
$loggedInUser = null;
if (isset($_SESSION['user_id'])) {
    $user = fetchOne(
        "SELECT user_id, username, email, role FROM users WHERE user_id = ?",
        [$_SESSION['user_id']]
    );
    $loggedInUser = $user;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FAQ System for Programming Beginners">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>FAQ System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= htmlspecialchars($baseUrl) ?>assets/css/style.css" rel="stylesheet">
    
    <?php if (isset($extraCSS)): ?>
    <link href="<?= htmlspecialchars($baseUrl) ?>assets/css/<?= htmlspecialchars($extraCSS) ?>.css" rel="stylesheet">
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= htmlspecialchars($baseUrl) ?>index.php">
                <i class="bi bi-question-circle-fill"></i> FAQ System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>index.php">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'search' ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>search.php">
                            <i class="bi bi-search"></i> Search
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'category' ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>category.php">
                            <i class="bi bi-folder"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'submit_question' ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>submit_question.php">
                            <i class="bi bi-send"></i> Ask Question
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if ($loggedInUser): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($loggedInUser['username']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($loggedInUser['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl) ?>admin/index.php">
                                        <i class="bi bi-speedometer2"></i> Admin Dashboard
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl) ?>logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'login' ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'register' ? 'active' : '' ?>" href="<?= htmlspecialchars($baseUrl) ?>register.php">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
```