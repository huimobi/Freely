<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../templates/common.tpl.php';

$session = Session::getInstance();
$allUsers = User::getMessagedUsers($session->getUser()->id);

drawHeader();

require_once __DIR__ . '/../templates/messages.tpl.php';

drawFooter();

