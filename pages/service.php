<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/photo.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/create_service.tpl.php';
require_once __DIR__ . '/../database/scripts/category.class.php';
require_once __DIR__ . '/../database/scripts/SERVICE.class.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';
require_once __DIR__ . '/../templates/service_page.tpl.php';
require_once __DIR__ . '/../database/scripts/joborder.class.php';

$id = (int) $_GET['id'] ?? null;


$SERVICE = SERVICE::getService($id);
$SELLER = User::getUser($SERVICE->sellerId) ?? null;

if (!$SERVICE || !$SELLER) {
    header('Location: /');
    exit;
}

//seller info
$sellerInfo=[
    'seller'=>$SELLER,
    'rating' => Comment::averageForSeller($SELLER->id),
    'profilePic' => Photo::getUserProfilePic($SELLER->id),
];

//service info
$serviceComments=Comment::getByService($SERVICE->id);
foreach ($serviceComments as $comment) {
    $comment->user = User::getUser($comment->buyerUserId) ?? null;
    $comment->userProfilePic = Photo::getUserProfilePic( $comment->buyerUserId); 
}

$serviceInfo=[
    'service'=> $SERVICE,
    'rating' => Comment::averageForService($SERVICE->id),
    'category' => Category::getById($SERVICE->categoryId),
    'comments' => $serviceComments,
    'photos' => Photo::getServicePhotos($SERVICE->id),
];

drawHeader();
drawServicePage($serviceInfo,$sellerInfo);
drawFooter();

