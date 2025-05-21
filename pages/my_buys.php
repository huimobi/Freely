<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../database/scripts/joborder.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../templates/my_buys.tpl.php';

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) {header('Location: /'); exit;}

$orders = JobOrder::getAllByBuyerId($user->id);

drawHeader();
drawMyBuysTable($orders);
drawFooter();
