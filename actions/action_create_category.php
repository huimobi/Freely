<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/category.class.php';

$session = Session::getInstance();

$submitted = $_POST['csrf_token'] ?? '';
if (!$session->validateCsrfToken($submitted)) {http_response_code(403); exit('Invalid CSRF token');}

if (!$session->isLoggedIn() || !$session->getUser()->isAdmin($session->getUser()->id)) {
    header('Location: ../index.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($name !== '') {
    Category::add($name, $description);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();