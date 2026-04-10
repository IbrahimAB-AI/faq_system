
<?php
/**
 * Logout Page - FAQ System
 */

require_once 'includes/auth.php';

// Clear session data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['viewed_faqs']);
unset($_SESSION['search_terms']);
unset($_SESSION['agent_session_token']);

// Logout user
logoutUser();

// Redirect to home with success message
setSuccess('You have been logged out successfully.');
redirect('index.php');
