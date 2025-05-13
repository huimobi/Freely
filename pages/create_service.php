<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/create_service.tpl.php';
require_once __DIR__ . '/../database/scripts/category.class.php';

$session = Session::getInstance();
$user    = $session->getUser();
if (!$user) {
  header('Location: /');
  exit;
}
$errors = $_SESSION['create_service_errors'] ?? [];
unset($_SESSION['create_service_errors']);

$cats = Category::getAllWithStats();

drawSimpleHeader();
drawCreateServiceForm($cats, $errors);
drawFooter();
