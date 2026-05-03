<?php
/**
 * GitHub Webhook Auto-Deploy Script
 * Place at: /public_html/deploy.php
 * GitHub Webhook URL: https://mikeymobile.com/deploy.php
 * Secret: set in GitHub webhook settings
 */

// --- Configuration ---
define('WEBHOOK_SECRET', 'mikeymobile_deploy_2026');
define('REPO_DIR', '/home/1584168.cloudwaysapps.com/njfxrssvcj/public_html');
define('BRANCH', 'main');
define('LOG_FILE', '/tmp/deploy_log.txt');

// --- Security: Verify GitHub signature ---
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (empty($signature)) {
    http_response_code(403);
    die('No signature provided.');
}

$expected = 'sha256=' . hash_hmac('sha256', $payload, WEBHOOK_SECRET);
if (!hash_equals($expected, $signature)) {
    http_response_code(403);
    die('Invalid signature.');
}

// --- Parse payload ---
$data = json_decode($payload, true);
$ref = $data['ref'] ?? '';
$pushedBranch = str_replace('refs/heads/', '', $ref);

// Only deploy on pushes to main branch
if ($pushedBranch !== BRANCH) {
    http_response_code(200);
    die("Push to '$pushedBranch' ignored. Only '${\BRANCH}' triggers deploy.");
}

// --- Run git pull ---
$timestamp = date('Y-m-d H:i:s');
$output = [];
$returnCode = 0;

exec("cd " . escapeshellarg(REPO_DIR) . " && git pull origin " . BRANCH . " 2>&1", $output, $returnCode);

$log = "[$timestamp] Deploy triggered by push to $pushedBranch\n";
$log .= "Return code: $returnCode\n";
$log .= implode("\n", $output) . "\n\n";
file_put_contents(LOG_FILE, $log, FILE_APPEND);

if ($returnCode === 0) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Deployed successfully', 'output' => $output]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Deploy failed', 'output' => $output]);
}
