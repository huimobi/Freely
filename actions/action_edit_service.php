<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/tag.class.php';

$session = Session::getInstance();

$submitted = $_POST['csrf_token'] ?? '';
if (!$session->validateCsrfToken($submitted)) {http_response_code(403); exit('Invalid CSRF token');}

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

if (isset($_FILES['photos']) && is_array($_FILES['photos']['tmp_name']) && count(array_filter($_FILES['photos']['tmp_name'])) > 0) {
    $uploadDir = __DIR__ . '/../images/services/' . $service->id . '/';

    // Delete old images if they exist
    if (is_dir($uploadDir)) {
        $oldFiles = glob($uploadDir . '*');
        foreach ($oldFiles as $file) {
            if (is_file($file)) unlink($file);
        }
    } else {
        mkdir($uploadDir, 0755, true);
    }

    foreach ($_FILES['photo']['tmp_name'] as $index => $tmpName) {
        if (is_uploaded_file($tmpName)) {
            $fileType = mime_content_type($tmpName);
            if (!in_array($fileType, ['image/jpeg', 'image/png'])) continue;

            $ext = $fileType === 'image/png' ? 'png' : 'jpg';
            $filename = $index . '.' . $ext;
            move_uploaded_file($tmpName, $uploadDir . $filename);
        }
    }
}

$service->save();
Tag::processTagsForService($service->id, $tags);

header("Location: /pages/my_services.php");
exit;
