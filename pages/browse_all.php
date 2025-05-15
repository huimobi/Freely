<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/browse_services.tpl.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$totalCount = Service::countAll();
$totalPages = (int)ceil($totalCount / $limit);

$priceSort = $_GET['price'] ?? '';
$ratingSort = $_GET['rating'] ?? '';
$services = Service::getAll($limit, $offset, $priceSort, $ratingSort);

foreach ($services as $svc) {
    $svc->seller = User::getUser($svc->sellerId);
    $svc->rating = Comment::averageForService($svc->id);
    $svc->numRatings = Comment::countForService($svc->id);
}

drawHeader("All Services");
drawBrowseServicesPage("All Services", $services, null, $page, $totalPages, "/pages/browse_all.php");
drawFooter();