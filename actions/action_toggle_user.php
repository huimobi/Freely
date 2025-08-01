<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/user.class.php';

$session = Session::getInstance();

$submitted = $_POST['csrf_token'] ?? '';
if (!$session->validateCsrfToken($submitted)) {http_response_code(403); exit('Invalid CSRF token');}

$currentUser = $session->getUser();
$targetUserId = intval($_POST['id']);

if (!$session->isLoggedIn() || (!$currentUser->isAdmin($currentUser->id) && $currentUser->id !== $targetUserId)) {
    header('Location: ../index.php');
    exit();
}

User::toggleUser($targetUserId);

if ($currentUser->id === $targetUserId) {
    $session->logout();
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();