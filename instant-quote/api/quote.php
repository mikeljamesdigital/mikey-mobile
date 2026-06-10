<?php
/**
 * Mikey Mobile — AI Quote API
 * POST /instant-quote/api/quote.php
 * Calls OpenAI to research vehicle specs and returns structured quote data.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://mikeymobile.com');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$year     = htmlspecialchars(trim($data['year']     ?? ''));
$make     = htmlspecialchars(trim($data['make']     ?? ''));
$model    = htmlspecialchars(trim($data['model']    ?? ''));
$submodel = htmlspecialchars(trim($data['submodel'] ?? ''));
$mileage  = htmlspecialchars(trim($data['mileage']  ?? ''));
$name     = htmlspecialchars(trim($data['name']     ?? ''));
$phone    = htmlspecialchars(trim($data['phone']    ?? ''));
$email    = htmlspecialchars(trim($data['email']    ?? ''));

if (!$year || !$make || !$model) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing vehicle info']);
    exit;
}

// ── OpenAI API Key ────────────────────────────────────────────────────────────
// Replace with your actual OpenAI key or set as environment variable
$openai_key = getenv('OPENAI_API_KEY') ?: 'YOUR_OPENAI_API_KEY_HERE';

// ── Build the prompt ──────────────────────────────────────────────────────────
$vehicle_str = "{$year} {$make} {$model}" . ($submodel ? " {$submodel}" : '');
$mileage_str = $mileage ? " with {$mileage} miles" : '';

$system_prompt = <<<PROMPT
You are an expert automotive service advisor for Mikey Mobile, a mobile oil change service in Fresno, CA.
Your job is to research a customer's vehicle and return accurate oil change specifications and pricing.

Mikey Mobile pricing:
- 4-cylinder engines: \$115 flat (includes 4–5 qts full synthetic + filter + labor)
- 6-cylinder engines: \$135 flat (includes 6–7 qts full synthetic + filter + labor)
- 8-cylinder engines: \$150 flat (includes 7–8 qts full synthetic + filter + labor)
- Diesel engines: \$249 flat
- Additional quarts beyond the base: +\$10/qt each

Air filter add-on pricing (estimate based on typical OEM part cost + labor):
- Engine air filter: typically \$35–\$55 depending on vehicle
- Cabin air filter: typically \$35–\$55 depending on vehicle

You MUST respond with ONLY a valid JSON object — no markdown, no explanation, just raw JSON.
PROMPT;

$user_prompt = <<<PROMPT
Research the {$vehicle_str}{$mileage_str} and return a JSON object with these exact fields:

{
  "engine_type": "4-Cylinder" | "6-Cylinder" | "8-Cylinder" | "Diesel",
  "engine_description": "e.g. 2.5L 4-Cylinder DOHC",
  "oil_type": "e.g. 0W-20 Full Synthetic",
  "oil_capacity": "e.g. 4.8 qts",
  "oil_capacity_quarts": 4.8,
  "filter_note": "e.g. Standard spin-on filter, OEM spec",
  "base_price": 115,
  "extra_quarts": 0,
  "engine_filter_price": 39,
  "engine_filter_note": "e.g. Fits most 2020 Camry 2.5L engines",
  "cabin_filter_price": 35,
  "cabin_filter_note": "e.g. Standard cabin filter for 2020 Camry",
  "service_notes": "Any important notes about this vehicle's oil change (1–2 sentences max)"
}

Rules:
- base_price must match the Mikey Mobile flat rate for the engine type
- extra_quarts = max(0, ceil(oil_capacity_quarts) - base_included_quarts) where base_included_quarts is 5 for 4-cyl, 7 for 6-cyl, 8 for 8-cyl, 0 for diesel
- For diesel, base_price = 249 and extra_quarts = 0
- engine_filter_price and cabin_filter_price should be realistic estimates (typically \$35–\$55)
- Return ONLY the JSON object, nothing else
PROMPT;

// ── Call OpenAI ───────────────────────────────────────────────────────────────
$payload = json_encode([
    'model'       => 'gpt-4o-mini',
    'messages'    => [
        ['role' => 'system', 'content' => $system_prompt],
        ['role' => 'user',   'content' => $user_prompt],
    ],
    'temperature' => 0.2,
    'max_tokens'  => 600,
    'response_format' => ['type' => 'json_object'],
]);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        "Authorization: Bearer {$openai_key}",
    ],
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$response || $http_code !== 200) {
    // Fallback: return a basic quote based on vehicle info
    $fallback = build_fallback_quote($make, $model, $year);
    echo json_encode($fallback);
    exit;
}

$ai_response = json_decode($response, true);
$content     = $ai_response['choices'][0]['message']['content'] ?? null;

if (!$content) {
    $fallback = build_fallback_quote($make, $model, $year);
    echo json_encode($fallback);
    exit;
}

$quote = json_decode($content, true);

if (!$quote || !isset($quote['base_price'])) {
    $fallback = build_fallback_quote($make, $model, $year);
    echo json_encode($fallback);
    exit;
}

// ── Send notification email to Mikey ─────────────────────────────────────────
send_quote_email($name, $phone, $email, $vehicle_str, $mileage, $quote);

// Return quote to browser
echo json_encode($quote);
exit;

// ── Helper: fallback quote ────────────────────────────────────────────────────
function build_fallback_quote($make, $model, $year) {
    return [
        'engine_type'        => '4-Cylinder',
        'engine_description' => "{$year} {$make} {$model}",
        'oil_type'           => '0W-20 Full Synthetic',
        'oil_capacity'       => '~5 qts',
        'oil_capacity_quarts'=> 5,
        'filter_note'        => 'Standard OEM-spec filter',
        'base_price'         => 115,
        'extra_quarts'       => 0,
        'engine_filter_price'=> 39,
        'engine_filter_note' => 'Standard engine air filter',
        'cabin_filter_price' => 35,
        'cabin_filter_note'  => 'Standard cabin air filter',
        'service_notes'      => 'Quote based on typical 4-cylinder configuration. Final price confirmed at service.',
    ];
}

// ── Helper: send email ────────────────────────────────────────────────────────
function send_quote_email($name, $phone, $email, $vehicle, $mileage, $quote) {
    $to      = 'mikey@mikeymobile.com';
    $subject = "New Quick Quote Request — {$vehicle}";

    $engine   = $quote['engine_type']        ?? 'Unknown';
    $oil_type = $quote['oil_type']           ?? 'Unknown';
    $oil_cap  = $quote['oil_capacity']       ?? 'Unknown';
    $price    = '$' . ($quote['base_price']  ?? '115');
    $notes    = $quote['service_notes']      ?? '';

    $body = "NEW QUICK QUOTE REQUEST\n";
    $body .= str_repeat('─', 40) . "\n\n";
    $body .= "CUSTOMER\n";
    $body .= "Name:    {$name}\n";
    $body .= "Phone:   {$phone}\n";
    $body .= "Email:   {$email}\n\n";
    $body .= "VEHICLE\n";
    $body .= "Vehicle: {$vehicle}\n";
    if ($mileage) $body .= "Mileage: {$mileage} miles\n";
    $body .= "\nQUOTE DETAILS\n";
    $body .= "Engine:       {$engine}\n";
    $body .= "Oil Type:     {$oil_type}\n";
    $body .= "Oil Capacity: {$oil_cap}\n";
    $body .= "Base Price:   {$price}\n";
    if ($notes) $body .= "Notes:        {$notes}\n";
    $body .= "\n" . str_repeat('─', 40) . "\n";
    $body .= "This customer has NOT yet booked. They are viewing their quote now.\n";
    $body .= "Sent: " . date('Y-m-d H:i:s') . " PST\n";

    $headers  = "From: quotes@mikeymobile.com\r\n";
    $headers .= "Reply-To: {$email}\r\n";
    $headers .= "X-Mailer: MikeyMobile-QuoteFunnel/1.0\r\n";

    @mail($to, $subject, $body, $headers);
}
