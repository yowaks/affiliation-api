<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';
use Stripe\Stripe;
use Stripe\Checkout\Session;

header('Content-Type: application/json');
Stripe::setApiKey(STRIPE_SECRET_KEY);

$input = json_decode(file_get_contents('php://input'), true);
$affiliateRef = $input['affiliate_code'] ?? null;

try {
  $session = Session::create([
    'mode' => 'payment',
    'line_items' => [[ 'price' => STRIPE_PRICE_ID, 'quantity' => 1 ]],
    'success_url' => SUCCESS_URL,
    'cancel_url' => CANCEL_URL,
    'metadata' => ['affiliate_ref' => $affiliateRef]
  ]);
  echo json_encode(['url' => $session->url]);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['error' => $e->getMessage()]);
}
