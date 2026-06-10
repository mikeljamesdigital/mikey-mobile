<?php
/**
 * Mikey Mobile — Booking Confirmation API
 * POST /instant-quote/api/book.php
 * Sends a full booking confirmation email to Mikey and the customer.
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

// Extract fields
$name       = htmlspecialchars(trim($data['name']       ?? ''));
$phone      = htmlspecialchars(trim($data['phone']      ?? ''));
$email      = htmlspecialchars(trim($data['email']      ?? ''));
$year       = htmlspecialchars(trim($data['year']       ?? ''));
$make       = htmlspecialchars(trim($data['make']       ?? ''));
$model      = htmlspecialchars(trim($data['model']      ?? ''));
$submodel   = htmlspecialchars(trim($data['submodel']   ?? ''));
$mileage    = htmlspecialchars(trim($data['mileage']    ?? ''));
$address    = htmlspecialchars(trim($data['address']    ?? ''));
$book_date  = htmlspecialchars(trim($data['book_date']  ?? ''));
$book_time  = htmlspecialchars(trim($data['book_time']  ?? ''));
$notes      = htmlspecialchars(trim($data['notes']      ?? ''));
$engine     = htmlspecialchars(trim($data['engine_type'] ?? ''));
$oil_type   = htmlspecialchars(trim($data['oil_type']   ?? ''));
$oil_cap    = htmlspecialchars(trim($data['oil_capacity'] ?? ''));
$base_price = floatval($data['base_price']  ?? 115);
$extra_qts  = intval($data['extra_quarts']  ?? 0);
$add_engine = !empty($data['add_engine_filter']);
$add_cabin  = !empty($data['add_cabin_filter']);
$eng_price  = floatval($data['engine_filter_price'] ?? 35);
$cab_price  = floatval($data['cabin_filter_price']  ?? 35);

$vehicle_str = trim("{$year} {$make} {$model} {$submodel}");

// Calculate total
$total = $base_price + ($extra_qts * 10);
if ($add_engine) $total += $eng_price;
if ($add_cabin)  $total += $cab_price;

// Format date
$formatted_date = $book_date;
if ($book_date) {
    $dt = DateTime::createFromFormat('Y-m-d', $book_date);
    if ($dt) $formatted_date = $dt->format('l, F j, Y');
}

// Build services list
$services = ['Full Synthetic Oil Change'];
if ($add_engine) $services[] = 'Engine Air Filter (+$' . number_format($eng_price, 0) . ')';
if ($add_cabin)  $services[] = 'Cabin Air Filter (+$'  . number_format($cab_price, 0) . ')';

// ── Email to Mikey ────────────────────────────────────────────────────────────
$to_mikey   = 'mikey@mikeymobile.com';
$subj_mikey = "🔧 New Booking — {$vehicle_str} on {$formatted_date}";

$body_mikey  = "NEW BOOKING CONFIRMED\n";
$body_mikey .= str_repeat('═', 44) . "\n\n";
$body_mikey .= "CUSTOMER\n";
$body_mikey .= "Name:    {$name}\n";
$body_mikey .= "Phone:   {$phone}\n";
$body_mikey .= "Email:   {$email}\n\n";
$body_mikey .= "VEHICLE\n";
$body_mikey .= "Vehicle: {$vehicle_str}\n";
if ($mileage) $body_mikey .= "Mileage: {$mileage} miles\n";
$body_mikey .= "Engine:  {$engine}\n";
$body_mikey .= "Oil:     {$oil_type} ({$oil_cap})\n\n";
$body_mikey .= "APPOINTMENT\n";
$body_mikey .= "Date:    {$formatted_date}\n";
$body_mikey .= "Time:    {$book_time}\n";
$body_mikey .= "Address: {$address}\n";
if ($notes) $body_mikey .= "Notes:   {$notes}\n";
$body_mikey .= "\nSERVICES\n";
foreach ($services as $svc) $body_mikey .= "  • {$svc}\n";
$body_mikey .= "\nESTIMATED TOTAL: \$" . number_format($total, 0) . "\n\n";
$body_mikey .= str_repeat('═', 44) . "\n";
$body_mikey .= "Submitted: " . date('Y-m-d H:i:s') . " PST\n";

$headers_mikey  = "From: bookings@mikeymobile.com\r\n";
$headers_mikey .= "Reply-To: {$email}\r\n";
$headers_mikey .= "X-Mailer: MikeyMobile-QuoteFunnel/1.0\r\n";

@mail($to_mikey, $subj_mikey, $body_mikey, $headers_mikey);

// ── Confirmation email to customer ────────────────────────────────────────────
if ($email) {
    $subj_cust = "Your Mikey Mobile Appointment is Confirmed!";

    $body_cust  = "Hi {$name},\n\n";
    $body_cust .= "You're all set! Here's a summary of your upcoming appointment:\n\n";
    $body_cust .= str_repeat('─', 40) . "\n";
    $body_cust .= "Vehicle:  {$vehicle_str}\n";
    $body_cust .= "Date:     {$formatted_date}\n";
    $body_cust .= "Time:     {$book_time}\n";
    $body_cust .= "Address:  {$address}\n";
    $body_cust .= "Services: " . implode(', ', $services) . "\n";
    $body_cust .= "Total:    \$" . number_format($total, 0) . " (estimated)\n";
    $body_cust .= str_repeat('─', 40) . "\n\n";
    $body_cust .= "We'll reach out to confirm closer to your appointment.\n";
    $body_cust .= "Questions? Call or text us at (559) 905-9494.\n\n";
    $body_cust .= "See you soon!\n";
    $body_cust .= "— Mikey Mobile Team\n\n";
    $body_cust .= "mikeymobile.com | Fresno, Clovis & Madera Ranchos\n";

    $headers_cust  = "From: bookings@mikeymobile.com\r\n";
    $headers_cust .= "Reply-To: mikey@mikeymobile.com\r\n";
    $headers_cust .= "X-Mailer: MikeyMobile-QuoteFunnel/1.0\r\n";

    @mail($email, $subj_cust, $body_cust, $headers_cust);
}

echo json_encode(['success' => true, 'total' => $total]);
exit;
