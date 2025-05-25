<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../templates/common.tpl.php';

require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/offer.class.php';

require_once __DIR__ . '/../templates/messages.tpl.php';

$session = Session::getInstance();
if (!$_SESSION['user_id']) {
    header("Location: /");
    exit;
}
$allUsers = User::getMessagedUsers($session->getUser()->id);
$myServices = Service::getAllByUserId($session->getUser()->id);
$preselectedUserId = isset($_GET['user']) ? intval($_GET['user']) : null;

if ($preselectedUserId !== null) {
    $alreadyIncluded = array_filter($allUsers, fn($u) => $u->id === $preselectedUserId);
    if (empty($alreadyIncluded)) {
        $userToAdd = User::getUser($preselectedUserId);
        if ($userToAdd !== null) {
            $allUsers[] = $userToAdd;
        }
    }
}

drawHeader();
drawMessagesPage($allUsers, $preselectedUserId, $myServices);
drawFooter();

