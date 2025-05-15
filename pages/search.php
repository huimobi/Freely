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

$services = [];
$totalPages = 1;

if (!empty($searchTerm)) {
  $totalCount = Tag::countServicesByPartialTag($searchTerm);


  $priceSort = $_GET['price'] ?? '';
  $ratingSort = $_GET['rating'] ?? '';
  $services = Tag::getServicesByPartialTag($searchTerm, $limit, $offset, $priceSort, $ratingSort);

  $totalPages = (int)ceil($totalCount / $limit);
  
  foreach ($services as $svc) {
    $svc->seller = User::getUser($svc->sellerId);
    $svc->rating = Comment::averageForService($svc->id);
    $svc->numRatings = Comment::countForService($svc->id);
  }
}

drawHeader("Search Results");
$des = !empty($searchTerm) ? "Results for '$searchTerm'" : "Search Results";
$title = "Services";
drawBrowseServicesPage($title, $services, $des);
drawFooter();
