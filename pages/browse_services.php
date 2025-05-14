<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/browse_all.tpl.php';
require_once __DIR__ . '/../templates/service_card.tpl.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';

$services = Service::getAllActive();

foreach ($services as $svc) {
  $svc->seller = User::getUser($svc->sellerId);
  $svc->rating = Comment::averageForService($svc->id);
  $svc->numRatings = Comment::countForService($svc->id);
}

drawHeader("All Services");
drawBrowseAllPage($services);
drawFooter();