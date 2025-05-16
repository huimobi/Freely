<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../templates/service_table.tpl.php';

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) {header('Location: login.php'); exit;}

$services = Service::getAllByUserId($user->id);

drawHeader();
drawServiceTable($services, true);
drawFooter();
