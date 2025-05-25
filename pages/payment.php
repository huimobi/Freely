<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/create_service.tpl.php';
require_once __DIR__ . '/../database/scripts/category.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';
require_once __DIR__ . '/../templates/service_page.tpl.php';
require_once __DIR__ . '/../templates/payment_page.tpl.php';

$service_id = (int) $_POST ['service_id'];

$Service = SERVICE::getById($service_id) ?? null;
$Service->seller = USER::getUser($Service->sellerId) ?? null;

$session = SESSION::getInstance();
$Buyer = $session->getUser() ?? null;

if (!$Service || !$Buyer) {
    header('Location: /');
    exit;
}

drawSimpleHeader();
drawPaymentPage($Service, $Buyer);

?>