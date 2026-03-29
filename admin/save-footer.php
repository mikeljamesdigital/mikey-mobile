<?php
/**
 * Save Footer Configuration
 * Receives JSON data and saves it to footer-config.json
 */

header('Content-Type: application/json');

// Get the JSON data from the request
$jsonData = file_get_contents('php://input');

// Validate JSON
$config = json_decode($jsonData);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON: ' . json_last_error_msg()
    ]);
    exit;
}

// Validate required fields
if (!isset($config->company) || !isset($config->columns) || !isset($config->copyright)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields in configuration'
    ]);
    exit;
}

// Save to file
$filePath = '../footer-config.json';
$result = file_put_contents($filePath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

if ($result === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to write to file. Check permissions.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Footer configuration saved successfully',
    'bytes' => $result
]);
?>
