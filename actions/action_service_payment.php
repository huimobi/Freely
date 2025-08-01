<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/database.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/joborder.class.php';


$session = Session::getInstance();

$submitted = $_POST['csrf_token'] ?? '';
if (!$session->validateCsrfToken($submitted)) {http_response_code(403); exit('Invalid CSRF token');}

$user = $session->getUser();

if (!$user) {
  header('Location: /');
  exit;
}

$serviceId =  (int)$_POST['service_id'] ?? 0;

$service = SERVICE::getById($serviceId) ?? 0;
$buyerId = $user->id ?? 0;
$sellerId = $service->sellerId ?? 0;
$basePrice = $service->basePrice ?? 0;
$currency = $service->currency ?? "";

$errors = [];

if ($serviceId <= 0) {
  $errors[] = 'Invalid service ID.';
}

if ($buyerId <= 0) {
  $errors[] = 'Invalid buyer.';
}
if ($sellerId <= 0) {
  $errors[] = 'Invalid seller.';
}
if ($basePrice <= 0) {
  $errors[] = 'Invalid base price.';
}
if (empty($currency)) {
  $errors[] = 'Currency not specified.';
}


if ($errors) {
  $_SESSION['create_payment_errors'] = $errors;
  header('Location: /pages/service.php?id=' . $serviceId);
  exit;
}

$JobOrderId=JobOrder::create($serviceId, $buyerId, $sellerId, $basePrice,$currency );

header('Location: /pages/my_buys.php');
exit;
