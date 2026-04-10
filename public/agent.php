<?php
/**
 * AI Agent Endpoint - FAQ System
 * Handles chat requests from the AI agent drawer
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/groq.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = getCurrentUser();
$username = $user ? $user['username'] : 'Guest';
$userId = $user ? $user['user_id'] : null;

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');

if (empty($message)) {
    echo json_encode(['success' => false, 'reply' => 'Please enter a message.']);
    exit;
}

$sessionToken = $_SESSION['agent_session_token'] ?? null;
if (!$sessionToken) {
    $sessionToken = bin2hex(random_bytes(32));
    $_SESSION['agent_session_token'] = $sessionToken;
}

$sessionData = fetchOne(
    "SELECT * FROM agent_sessions WHERE session_token = ?",
    [$sessionToken]
);

if ($sessionData && !empty($sessionData['messages'])) {
    $messages = json_decode($sessionData['messages'], true);
    if (!is_array($messages)) {
        $messages = [];
    }
} else {
    $messages = [];
}

$context = [
    'username' => $username,
    'viewed_faqs' => $_SESSION['viewed_faqs'] ?? [],
    'search_terms' => $_SESSION['search_terms'] ?? [],
];

$currentFaqId = $_SESSION['current_faq_id'] ?? null;
if ($currentFaqId) {
    $currentFaq = getFaqById($currentFaqId);
    if ($currentFaq) {
        $context['current_faq'] = $currentFaq;
    }
}

$systemPrompt = buildAgentSystemPrompt($userId, $context);

$messages[] = ['role' => 'user', 'content' => $message];

$apiMessages = array_merge(
    [['role' => 'system', 'content' => $systemPrompt]],
    array_slice($messages, -10)
);

$response = callGroqAPI($apiMessages);

if ($response['success']) {
    $reply = $response['reply'];
    $messages[] = ['role' => 'assistant', 'content' => $reply];
    
    if ($sessionData) {
        executeQuery(
            "UPDATE agent_sessions SET messages = ?, updated_at = NOW() WHERE session_token = ?",
            [json_encode($messages), $sessionToken]
        );
    } else {
        executeQuery(
            "INSERT INTO agent_sessions (user_id, session_token, messages) VALUES (?, ?, ?)",
            [$userId, $sessionToken, json_encode($messages)]
        );
    }
    
    echo json_encode([
        'success' => true,
        'reply' => $reply
    ]);
} else {
    echo json_encode([
        'success' => false,
        'reply' => 'Sorry, I\'m having trouble responding right now. ' . $response['error']
    ]);
}