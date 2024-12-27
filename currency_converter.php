<?php
session_start();

// Define the cache duration (10 minutes in seconds)
$cacheDuration = 600; 

// Define the API endpoint and key
$apiKey = "b86433b2ed93e064a73e37c2";
$baseCurrency = isset($_GET['base']) ? $_GET['base'] : 'eur';

// Check if rates are already cached in the session and still valid
if (isset($_SESSION['currency_rates'][$baseCurrency])) {
    $cachedData = $_SESSION['currency_rates'][$baseCurrency];
    if (time() - $cachedData['timestamp'] < $cacheDuration) {
        echo json_encode($cachedData['rates']);
        exit;
    }
}

// Fetch new rates from the API
$apiUrl = "https://v6.exchangerate-api.com/v6/$apiKey/latest/$baseCurrency";
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// Validate and cache the response
if ($data && isset($data['conversion_rates'])) {
    $_SESSION['currency_rates'][$baseCurrency] = [
        'rates' => $data['conversion_rates'],
        'timestamp' => time()
    ];
    echo json_encode($data['conversion_rates']);
} else {
    // Return an error if the API call fails
    http_response_code(500);
    echo json_encode(['error' => 'Unable to fetch conversion rates.']);
}
