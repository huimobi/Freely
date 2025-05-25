<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/service.class.php';

$session = Session::getInstance();

$submitted = $_POST['csrf_token'] ?? '';
if (!$session->validateCsrfToken($submitted)) {http_response_code(403); exit('Invalid CSRF token');}

$user = $session->getUser();

if (!$user) { header('Location: /'); exit;}

$serviceId = (int)$_POST['serviceId'];
$service = Service::getById($serviceId);

if ($service && $service->sellerId === $user->id) { $service->toggleService($serviceId);}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();