<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/create_comment.tpl.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';

$session = Session::getInstance();
$user = $session->getUser();

$jobOrderId= (int)$_POST['order_id'];
$serviceId= (int)$_POST['service_id'];

if(Comment::hasComment($jobOrderId)|| !$user ||!$serviceId) {
    header('Location: /'); 
    exit;
}

drawSimpleHeader();
drawCreateCommentForm($serviceId,$jobOrderId);
drawFooter();
