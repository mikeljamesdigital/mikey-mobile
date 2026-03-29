<?php
// --- Configuration ---
const API_KEY = "MGlRe313HjyVe0EMSpZcImWmysOIFx";
const EMAIL = "mikel@mikeljamesdigital.com";

// --- Do not edit below this line ---
const API_URL = "https://api.cloudways.com/api/v1";

function callCloudwaysAPI($method, $url, $accessToken, $post = [])
{
    $baseURL = API_URL;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_URL, $baseURL . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($accessToken) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $accessToken]);
    }

    if (!empty($post)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }

    $output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpcode !== 200) {
        error_log("Cloudways API Error: Code $httpcode - " . substr($output, 0, 1000));
        http_response_code(500);
        exit("An error occurred.");
    }
    curl_close($ch);
    return json_decode($output);
}

// 1. Get Access Token
$tokenResponse = callCloudwaysAPI("POST", "/oauth/access_token", null, [
    "email" => EMAIL,
    "api_key" => API_KEY
]);
$accessToken = $tokenResponse->access_token;

// 2. Trigger Git Pull
$gitPullResponse = callCloudwaysAPI("POST", "/git/pull", $accessToken, [
    "server_id"   => $_GET["server_id"],
    "app_id"      => $_GET["app_id"],
    "git_url"     => $_GET["git_url"],
    "branch_name" => $_GET["branch_name"],
    "deploy_path" => isset($_GET["deploy_path"]) ? $_GET["deploy_path"] : ""
]);

// 3. Respond to GitHub
http_response_code(200);
echo json_encode([
    "status" => "success",
    "message" => "Deployment started.",
    "operation_id" => $gitPullResponse->operation_id ?? null
]);
?>
