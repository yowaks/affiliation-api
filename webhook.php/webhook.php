<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';
use Stripe\Stripe;
use Stripe\Webhook;

Stripe::setApiKey(STRIPE_SECRET_KEY);
$payload = @file_get_contents('php://input');
$sig = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
  $event = Webhook::constructEvent($payload, $sig, STRIPE_WEBHOOK_SECRET);
} catch(Exception $e) {
  http_response_code(400);
  exit('invalid signature');
}

function supabase_insert($table,$data){
  $url = rtrim(SUPABASE_URL, '/') . "/rest/v1/$table";
  $opts = [
    'http'=>[
      'method'=>'POST',
      'header'=>"Content-Type: application/json\r\napikey: ".SUPABASE_SERVICE_KEY."\r\nAuthorization: Bearer ".SUPABASE_SERVICE_KEY,
      'content'=>json_encode($data)
    ]
  ];
  file_get_contents($url,false,stream_context_create($opts));
}

if($event->type === 'checkout.session.completed'){
  $s = $event->data->object;
  $ref = $s->metadata->affiliate_ref ?? null;
  supabase_insert('orders',[
    'stripe_payment_intent'=>$s->payment_intent,
    'stripe_session_id'=>$s->id,
    'affiliate_code'=>$ref,
    'amount_total_cents'=>$s->amount_total,
    'currency'=>$s->currency,
    'customer_email'=>$s->customer_details->email ?? null,
    'status'=>'paid'
  ]);
}
http_response_code(200);
echo json_encode(['ok'=>true]);
