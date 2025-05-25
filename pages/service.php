<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/create_service.tpl.php';
require_once __DIR__ . '/../database/scripts/category.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';
require_once __DIR__ . '/../templates/service_page.tpl.php';
require_once __DIR__ . '/../database/scripts/joborder.class.php';

$id = (int) $_GET['id'] ?? null;


$SERVICE = SERVICE::getService($id);

$SERVICE->rating = Comment::averageForService($SERVICE->id) ?? 0;
$SERVICE->numRatings = Comment::countForService(  $SERVICE->id) ?? 0;

$SELLER = User::getUser($SERVICE->sellerId) ?? null;

if (!$SERVICE || !$SELLER) {
    header('Location: /');
    exit;
}

//seller info
$sellerInfo=[
    'seller'=>$SELLER,
    'rating' => Comment::averageForSeller($SELLER->id),
    'totalServices' => count(SERVICE::getAllByUserId($SELLER->id)),
    'totalOrders' => count(JobOrder::getAllBySellerId($SELLER->id)),
    'profilePic' => (file_exists("../images/users/{$SERVICE->sellerId}.jpg") ? "../images/users/{$SERVICE->sellerId}.jpg" : "../images/users/default.jpg"),
];

//service info
$serviceComments=Comment::getByService($SERVICE->id);
foreach ($serviceComments as $comment) {
    $comment->user = User::getUser($comment->buyerUserId) ?? null;
    $comment->userProfilePic = file_exists("/images/users/" . $comment->buyerUserId . ".jpg") ? "/images/users/" . $comment->buyerUserId . ".jpg" : "/images/users/default.jpg";
}

$photos=getPhotos($SERVICE->id);
$serviceInfo=[
    'service'=> $SERVICE,
    'rating' => Comment::averageForService($SERVICE->id) ?? 0,
    'numRatings' => Comment::countForService($SERVICE->id) ?? 0,
    'category' => Category::getById($SERVICE->categoryId) ?? null,
    'comments' => $serviceComments,
    'totalComments' => Comment::countForService($SERVICE->id) ?? 0,
    'photos' => $photos,
    'totalPhotos' => count($photos) ??0,
];

drawHeader();
drawServicePage($serviceInfo,$sellerInfo);
drawFooter();


function getPhotos($id): array
{
    $photos = [];
    $dir = __DIR__ . '/../images/services/' . $id;
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $photos[] = '/images/services/' . $id . '/' . $file;
            }
        }
    }
    return $photos;
}