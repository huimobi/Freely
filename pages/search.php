<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/browse_services.tpl.php';
require_once __DIR__ . '/../database/scripts/tag.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';

$searchTerm = $_GET['q'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$sort = $_GET['sort'] ?? '';
$priceMin = (isset($_GET['price_min']) && $_GET['price_min'] !== '') ? (float)$_GET['price_min'] : null;
$priceMax = (isset($_GET['price_max']) && $_GET['price_max'] !== '') ? (float)$_GET['price_max'] : null;
$ratingMin = (isset($_GET['rating_min']) && $_GET['rating_min'] !== '') ? (float)$_GET['rating_min'] : null;
$ratingMax = (isset($_GET['rating_max']) && $_GET['rating_max'] !== '') ? (float)$_GET['rating_max'] : null;

$services = [];
$totalPages = 1;

if (!empty($searchTerm)) {
  $totalCount = Tag::countServicesByPartialTag($searchTerm);
  $totalPages = (int)ceil($totalCount / $limit);
  $services = Tag::getServicesByPartialTag($searchTerm, $limit, $offset, $sort, $priceMin, $priceMax, $ratingMin, $ratingMax);

  foreach ($services as $svc) {
    $svc->seller = User::getUser($svc->sellerId);
    $svc->rating = Comment::averageForService($svc->id);
    $svc->numRatings = Comment::countForService($svc->id);
  }
}

drawHeader("Search Results");
$des = !empty($searchTerm) ? "Results for '$searchTerm'" : "Search Results";
$title = "Services";
drawBrowseServicesPage($title, $services, $des, $page, $totalPages, "/pages/search.php?q=" . urlencode($searchTerm));
drawFooter();
