<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/database.php';
require_once __DIR__.'/../database/scripts/comment.class.php';


$session = Session::getInstance();
$user = $session->getUser();
if (!$user) {
  header('Location: /');
  exit;
}

$jobOrderId = (int)($_POST['job_order_id'] ?? 0);
$buyerUserId = (int)($_POST['buyer_user_id'] ?? 0);
$serviceId = (int)($_POST['service_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$desc = trim($_POST['description'] ?? '');

$errors = [];

if ($jobOrderId <= 0) $errors[] = 'Invalid job order ID.';
if ($buyerUserId <= 0) $errors[] = 'Invalid buyer user ID.';
if ($serviceId <= 0) $errors[] = 'Invalid service ID.';
if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5.';
if ($desc === '') $errors[] = 'Description is required.';
if (strlen($desc) > 2000) $errors[] = 'Description is too long (max 2000 characters).';


if ($errors) {
  $_SESSION['create_comment_errors'] = $errors;
  header('Location: /');
  exit;
}
$newCommentId = Comment::create($jobOrderId, $buyerUserId, $serviceId, $rating, $desc);

header('Location: /service.php?id=' . $serviceId);
exit;
