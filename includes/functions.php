<?php
/**
 * Common Functions
 * FAQ System
 */

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') 
    . '://' . $_SERVER['HTTP_HOST'] 
    . dirname($_SERVER['REQUEST_URI']) . '/';

if (substr($baseUrl, -1) !== '/') {
    $baseUrl .= '/';
}

/**
 * Redirect to URL
 * @param string $url
 * @return void
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Show success message
 * @param string $message
 * @return void
 */
function setSuccess($message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['success'] = $message;
}

/**
 * Show error message
 * @param string $message
 * @return void
 */
function setError($message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['error'] = $message;
}

/**
 * Get and clear success message
 * @return string|null
 */
function getSuccess() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['success'])) {
        $msg = $_SESSION['success'];
        unset($_SESSION['success']);
        return $msg;
    }
    return null;
}

/**
 * Get and clear error message
 * @return string|null
 */
function getError() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['error'])) {
        $msg = $_SESSION['error'];
        unset($_SESSION['error']);
        return $msg;
    }
    return null;
}

/**
 * Display flash message (success/error)
 * @return void
 */
function displayFlashMessages() {
    $success = getSuccess();
    $error = getError();
    
    if ($success) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo h($success);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
    
    if ($error) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo h($error);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

/**
 * Get all categories
 * @return array
 */
function getCategories() {
    return fetchAll("SELECT * FROM categories ORDER BY category_name");
}

/**
 * Get category by ID
 * @param int $categoryId
 * @return array|null
 */
function getCategoryById($categoryId) {
    return fetchOne("SELECT * FROM categories WHERE category_id = ?", [$categoryId]);
}

/**
 * Get FAQs by category
 * @param int $categoryId
 * @param int $limit
 * @return array
 */
function getFaqsByCategory($categoryId, $limit = 50) {
    return fetchAll(
        "SELECT f.*, u.username as created_by_username 
         FROM faqs f 
         JOIN users u ON f.created_by = u.user_id 
         WHERE f.category_id = ? 
         ORDER BY f.created_at DESC 
         LIMIT ?",
        [$categoryId, $limit]
    );
}

/**
 * Get single FAQ
 * @param int $faqId
 * @return array|null
 */
function getFaqById($faqId) {
    return fetchOne(
        "SELECT f.*, c.category_name, u.username as created_by_username 
         FROM faqs f 
         JOIN categories c ON f.category_id = c.category_id 
         JOIN users u ON f.created_by = u.user_id 
         WHERE f.faq_id = ?",
        [$faqId]
    );
}

/**
 * Search FAQs
 * @param string $query
 * @param int $limit
 * @return array
 */
function searchFaqs($query, $limit = 20) {
    $searchTerm = '%' . $query . '%';
    return fetchAll(
        "SELECT f.faq_id, f.question, f.answer, c.category_name,
                MATCH(f.question, f.answer) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
         FROM faqs f
         JOIN categories c ON f.category_id = c.category_id
         WHERE MATCH(f.question, f.answer) AGAINST(? IN NATURAL LANGUAGE MODE)
         ORDER BY relevance DESC
         LIMIT ?",
        [$query, $searchTerm, $limit]
    );
}

/**
 * Log search query
 * @param string $query
 * @return void
 */
function logSearch($query) {
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    executeQuery(
        "INSERT INTO search_logs (search_query, user_id) VALUES (?, ?)",
        [$query, $userId]
    );
}

/**
 * Format date for display
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Truncate text
 * @param string $text
 * @param int $length
 * @return string
 */
function truncate($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}
