<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../templates/common.tpl.php';
require_once __DIR__.'/../templates/edit_service.tpl.php';
require_once __DIR__.'/../database/scripts/service.class.php';
require_once __DIR__.'/../database/scripts/category.class.php';

$session = Session::getInstance();
$user = $session->getUser();
if (!$user) { header('Location: /'); exit; }

$serviceId = (int)($_GET['id'] ?? 0);
$service = Service::getById($serviceId);
if (!$service || $service->sellerId !== $user->id) { header('Location: /'); exit; }

$errors = $_SESSION['edit_service_errors'] ?? [];
unset($_SESSION['edit_service_errors']);

$cats = Category::getAllWithStats();

drawSimpleHeader();
drawEditServiceForm($service, $cats, $errors);