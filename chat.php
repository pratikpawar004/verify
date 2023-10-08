<?php
// Replace '6177837627:AAFtgdqBNx-4kXx-Ihy9Gbc_1EjmI6qc86A' with your actual Telegram bot token
$botToken = '6177837627:AAFtgdqBNx-4kXx-Ihy9Gbc_1EjmI6qc86A';

// Replace 'YOUR_OPENAI_API_KEY' with your actual OpenAI API key
$apiKey = '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';

// Define the base URL for the Telegram Bot API
$apiUrl = "https://api.telegram.org/bot$botToken/";

// Function to send a message to OpenAI GPT-3 and get a response
function sendToOpenAI($message) {
    global $apiKey;
    $data = [
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant.'
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ]
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseObj = json_decode($response);
    $botReply = $responseObj->choices[0]->message->content;

    return $botReply;
}

// Function to send a message to a user
function sendMessage($chatId, $message) {
    global $apiUrl;
    $data = array(
        'chat_id' => $chatId,
        'text' => $message,
    );
    $url = $apiUrl . 'sendMessage?' . http_build_query($data);
    file_get_contents($url);
}

// Retrieve incoming updates from Telegram
$update = json_decode(file_get_contents('php://input'), true);

if (!$update) {
    // Handle errors or empty updates
    exit;
}

$chatId = $update['message']['chat']['id'];
$userMessage = $update['message']['text'];

// Send the user's message to OpenAI and get a response
$botReply = sendToOpenAI($userMessage);

// Send the OpenAI response back to the user
sendMessage($chatId, $botReply);



// Function to handle user commands
function handleCommands($chatId, $command) {
    switch ($command) {
        case '/start':
            $responseMessage = "Welcome to your Telegram bot!";
            break;
        case '/help':
            $responseMessage = "This is a help message. You can use other commands too!";
            break;
        default:
            $responseMessage = "I'm not sure what you mean. Type /help for assistance.";
    }

    sendMessage($chatId, $responseMessage);
}


// Retrieve incoming updates from Telegram
$update = json_decode(file_get_contents('php://input'), true);

if (!$update) {
    // Handle errors or empty updates
    exit;
}

$chatId = $update['message']['chat']['id'];
$userMessage = $update['message']['text'];

// Check for user commands
if (strpos($userMessage, '/') === 0) {
    handleCommands($chatId, $userMessage);
} else {
    // You can implement additional logic for non-command messages here
    $responseMessage = "You said: $userMessage";
    sendMessage($chatId, $responseMessage);
}


?>
