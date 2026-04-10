<?php
/**
 * Groq API Configuration
 * FAQ System - AI Agent
 * 
 * IMPORTANT: This file contains your API key.
 * Never expose this to the frontend or commit to version control.
 */

// Groq API Configuration
define('GROQ_API_KEY', 'YOUR_GROQ_API_KEY_HERE');
define('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');

// Model configuration
define('GROQ_MODEL', 'llama-3.1-8b-instant');

/**
 * Call Groq API
 * @param array $messages
 * @return array
 */
function callGroqAPI($messages) {
    $apiKey = GROQ_API_KEY;
    $apiUrl = GROQ_API_URL;
    
    // Check if API key is configured
    if ($apiKey === 'YOUR_GROQ_API_KEY_HERE' || empty($apiKey)) {
        return [
            'success' => false,
            'error' => 'Groq API key not configured. Please add your API key in config/groq.php'
        ];
    }
    
    $data = [
        'model' => GROQ_MODEL,
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 1024,
    ];
    
    $ch = curl_init($apiUrl);
    
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    curl_close($ch);
    
    if ($curlError) {
        error_log("Groq API cURL Error: " . $curlError);
        return [
            'success' => false,
            'error' => 'Failed to connect to Groq API'
        ];
    }
    
    if ($httpCode !== 200) {
        error_log("Groq API HTTP Error: " . $httpCode . " - " . $response);
        return [
            'success' => false,
            'error' => 'Groq API returned error: HTTP ' . $httpCode
        ];
    }
    
    $result = json_decode($response, true);
    
    if (isset($result['choices'][0]['message']['content'])) {
        return [
            'success' => true,
            'reply' => $result['choices'][0]['message']['content']
        ];
    }
    
    return [
        'success' => false,
        'error' => 'Unexpected API response format'
    ];
}

/**
 * Build AI Agent system prompt with context
 * @param int|null $userId
 * @param array $context
 * @return string
 */
function buildAgentSystemPrompt($userId = null, $context = []) {
    $username = $context['username'] ?? 'Guest';
    $viewedFaqs = $context['viewed_faqs'] ?? [];
    $currentFaq = $context['current_faq'] ?? null;
    $searchTerms = $context['search_terms'] ?? [];
    
    $systemPrompt = "You are a helpful AI assistant for a programming FAQ system. Your role is to help beginners learn programming concepts." . PHP_EOL;
    $systemPrompt .= "User: {$username}" . PHP_EOL;
    
    if (!empty($viewedFaqs)) {
        $systemPrompt .= PHP_EOL . "Recently viewed FAQs:" . PHP_EOL;
        foreach (array_slice($viewedFaqs, -5) as $faq) {
            $systemPrompt .= "- " . htmlspecialchars($faq['question']) . PHP_EOL;
        }
    }
    
    if ($currentFaq) {
        $systemPrompt .= PHP_EOL . "Currently viewing FAQ: " . htmlspecialchars($currentFaq['question']) . PHP_EOL;
        $systemPrompt .= "Answer: " . htmlspecialchars($currentFaq['answer']) . PHP_EOL;
    }
    
    if (!empty($searchTerms)) {
        $systemPrompt .= PHP_EOL . "Search terms used: " . implode(', ', array_slice($searchTerms, -3)) . PHP_EOL;
    }
    
    $systemPrompt .= PHP_EOL . "Guidelines:" . PHP_EOL;
    $systemPrompt .= "- Be friendly and encouraging for beginners" . PHP_EOL;
    $systemPrompt .= "- Use simple language, avoid jargon without explanation" . PHP_EOL;
    $systemPrompt .= "- When relevant, reference FAQs in the system" . PHP_EOL;
    $systemPrompt .= "- If you don't know something, be honest and suggest where to learn more" . PHP_EOL;
    $systemPrompt .= "- Keep responses concise but informative";
    
    return $systemPrompt;
}

**⚠️ IMPORTANT:** Copy this into your `config/groq.php` file, then **replace** `YOUR_GROQ_API_KEY_HERE` with your actual Groq API key from https://console.groq.com/keys