<?php
require __DIR__ . '/config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$affiliateCode = $input['affiliate_code'] ?? null;
$landingPath = $input['landing_path'] ?? null;
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

if (!$affiliateCode) {
  http_response_code(400);
  echo json_encode(['error' => 'missing affiliate code']);
  exit;
}

$url = rtrim(SUPABASE_URL, '/') . '/rest/v1/clicks';
$data = [
  'affiliate_code' => $affiliateCode,
  'ip' => $ip,
  'user_agent' => $userAgent,
  'landing_path' => $landingPath
];
$opts = [
  'http' => [
    'method' => 'POST',
    'header' => "Content-Type: application/json\r\n".
                "apikey: ".SUPABASE_SERVICE_KEY."\r\n".
                "Authorization: Bearer ".SUPABASE_SERVICE_KEY."\r\n",
    'content' => json_encode($data)
  ]
];
$ctx = stream_context_create($opts);
file_get_contents($url, false, $ctx);
echo json_encode(['ok'=>true]);
