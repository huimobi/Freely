<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/photo.php';
require_once __DIR__ . '/../database/scripts/database.php';
require_once __DIR__. '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/tag.class.php';


$session = Session::getInstance();
$user = $session->getUser();
if (!$user) {
  header('Location: /');
  exit;
}

$catId = (int)($_POST['category_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$desc = trim($_POST['description'] ?? '');
$basePrice = $_POST['base_price'] ?? '';
$currency = trim($_POST['currency'] ?? '');
$deliveryDays = (int)($_POST['delivery_days'] ?? 0);
$revisions = (int)($_POST['revisions'] ?? 0);

$errors = [];


if ($catId <= 0) $errors[] = 'Please select a category.';
if ($title === '1') $errors[] = 'Title is required.';
if (strlen($title) > 150) $errors[] = 'Title is too long.';
if ($desc === '') $errors[] = 'Description is required.';
if (!is_numeric($basePrice) || $basePrice < 0) $errors[] = 'Enter a valid price.';
if ($currency === '' || strlen($currency) > 3) $errors[] = 'Enter a 3-letter currency code.';
if ($deliveryDays < 1) $errors[] = 'Delivery days must be at least 1.';
if ($revisions < 0) $errors[] = 'Revisions cannot be negative.';
if (strlen($desc) > 2000) $errors[] = 'Description is too long (max 2000 characters).';

if ($errors) {
  $_SESSION['create_service_errors'] = $errors;
  header('Location: /pages/create_service.php');
  exit;
}

$newServiceId = Service::create( $user->id,$catId, $title, $desc, (float)$basePrice, $currency, $deliveryDays, $revisions);

$rawTags = $_POST['tags'] ?? '';
Tag::processTagsForService($newServiceId, $rawTags);

Photo::setServicePhotos($_FILES,$newServiceId);

header('Location: /pages/service.php?id='. $newServiceId);
exit;
