<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/browse_cat.tpl.php';
require_once __DIR__ . '/../templates/service_card.tpl.php';
require_once __DIR__ . '/../database/scripts/category.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';

$catId          = (int)($_GET['cat']      ?? 0);
$priceFilter    = $_GET['price']    ?? '';   // '' | 'low' | 'high'
$deliveryFilter = $_GET['delivery'] ?? '';   // '' | '1' | '7'

$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

$category   = Category::getById($catId);
$totalCount = Service::countByCategory($catId, $priceFilter, $deliveryFilter);
$services   = Service::getByCategory($catId, $limit, $offset, $priceFilter, $deliveryFilter);
$totalPages = (int)ceil($totalCount / $limit);

drawHeader();
drawBrowsePage($category, $services, $page, $totalPages, $catId, $priceFilter, $deliveryFilter);
drawFooter();