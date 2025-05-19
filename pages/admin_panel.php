<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../database/scripts/category.class.php';
require_once __DIR__ . '/../templates/admin_panel.tpl.php';

$session = Session::getInstance();
$user = $session->getUser();

if (!$user || !$user->isAdmin($user->id)) {
    header("Location: ../index.php");
    exit();
}

$services = Service::everyService();
$users = User::everyUser();
$categories = Category::getAllWithStats();

drawHeader();
drawAdminPanel($services, $users, $categories);
drawFooter();
