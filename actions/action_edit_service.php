<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/photo.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/tag.class.php';

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) { header('Location: /'); exit; }

$serviceId = (int)($_POST['serviceId'] ?? 0);
$catId = (int)($_POST['categoryId'] ?? 0);
$title = trim($_POST['title'] ?? '');
$desc = trim($_POST['description'] ?? '');
$basePrice = $_POST['basePrice'] ?? '';
$currency = trim($_POST['currency'] ?? '');
$deliveryDays = (int)($_POST['deliveryDays'] ?? 0);
$revisions = (int)($_POST['revisions'] ?? 0);
$tags = trim($_POST['tags'] ?? '');

$errors = [];

if ($catId <= 0) $errors[] = 'Please select a category.';
if ($title === '') $errors[] = 'Title is required.';
if (strlen($title) > 150) $errors[] = 'Title is too long.';
if ($desc === '') $errors[] = 'Description is required.';
if (!is_numeric($basePrice) || $basePrice < 0) $errors[] = 'Enter a valid price.';
if ($currency === '' || strlen($currency) > 3) $errors[] = 'Enter a valid currency.';
if ($deliveryDays < 1) $errors[] = 'Delivery days must be at least 1.';
if ($revisions < 0) $errors[] = 'Revisions cannot be negative.';
if (strlen($desc) > 2000) $errors[] = 'Description is too long.';

$service = Service::getById($serviceId);

if (!$service || $service->sellerId !== $user->id){
  header('Location: /');
  exit;
}

if ($errors) {
  $_SESSION['edit_service_errors'] = $errors;
  header("Location: /pages/edit_service.php?id=$serviceId");
  exit;
}

$service->categoryId = $catId;
$service->title = $title;
$service->description = $desc;
$service->basePrice = (float)$basePrice;
$service->currency = $currency;
$service->deliveryDays = $deliveryDays;
$service->revisions = $revisions;

Photo::setServicePhotos($_FILES,$serviceId);

$service->save();
Tag::processTagsForService($service->id, $tags);

header("Location: /pages/service.php?id=". $service->id);
exit;
