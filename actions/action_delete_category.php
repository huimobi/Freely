<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/category.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';

$session = Session::getInstance();
if (!$session->isLoggedIn() || !$session->getUser()->isAdmin($session->getUser()->id)) {
    header("Location: ../index.php");
    exit();
}

$categoryId = intval($_POST['id']);

// Do not allow deletion of the fallback category
if ($categoryId === 1) {
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit();
}

Service::reassignCategory($categoryId, 1);
Category::delete($categoryId);

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
exit();