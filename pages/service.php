<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/create_service.tpl.php';
require_once __DIR__ . '/../database/scripts/category.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../templates/service_header.tpl.php';
require_once __DIR__ . '/../templates/service_description.tpl.php';

$id=(int)$_GET['id'] ?? null;

if ($id === null) {
    header('Location: /');
    exit;
}

$SERVICE = Service::getService($id);
$USER= USER::getUser($SERVICE->sellerId);




drawSimpleHeader();
drawServiceHeader($SERVICE, $USER);
drawServiceDescription($SERVICE);
drawFooter();
