<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/service.class.php';

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) { header('Location: /'); exit;}

$serviceId = (int)$_POST['serviceId'];
$service = Service::getById($serviceId);

if ($service && $service->sellerId === $user->id) { Service::deleteById($serviceId);}

header('Location: ../pages/my_services.php');
exit;
