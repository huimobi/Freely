<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/browse_services.tpl.php';
require_once __DIR__ . '/../database/scripts/category.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';

$catId = (int)($_GET['cat'] ?? 0);
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$category = Category::getById($catId);
$totalCount = Service::countByCategory($catId);

$sort = $_GET['sort'] ?? '';
$priceMin = (isset($_GET['price_min']) && $_GET['price_min'] !== '') ? (float)$_GET['price_min'] : null;
$priceMax = (isset($_GET['price_max']) && $_GET['price_max'] !== '') ? (float)$_GET['price_max'] : null;
$ratingMin = (isset($_GET['rating_min']) && $_GET['rating_min'] !== '') ? (float)$_GET['rating_min'] : null;
$ratingMax = (isset($_GET['rating_max']) && $_GET['rating_max'] !== '') ? (float)$_GET['rating_max'] : null;

$services = Service::getByCategory($catId, $limit, $offset, $sort, $priceMin, $priceMax, $ratingMin, $ratingMax);
$totalPages = (int)ceil($totalCount / $limit);


$queryParams = [
    'cat' => $catId,
    'sort' => $sort,
    'price_min' => $priceMin,
    'price_max' => $priceMax,
    'rating_min' => $ratingMin,
    'rating_max' => $ratingMax,
];
foreach ($queryParams as $key => $val) {
    if ($val === null || $val === '') {
        unset($queryParams[$key]);
    }
}
$baseUrl = '/pages/browse_cat.php?' . http_build_query($queryParams);

foreach ($services as $svc) {
    $svc->seller = User::getUser($svc->sellerId);
    $svc->rating = Comment::averageForService($svc->id);
    $svc->numRatings = Comment::countForService($svc->id);
}

drawHeader();
drawBrowseServicesPage($category->name, $services, $category->description, $page, $totalPages, $baseUrl);
drawFooter();