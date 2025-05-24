<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
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
$USER= SESSION::getInstance()->getUser();

if (!$SERVICE) {
    header('Location: /');
    exit;
}

$SELLER = User::getUser($SERVICE->sellerId) ?? null;
if($SELLER) {
$SELLER->rating = Comment::averageForSeller($SELLER->id); 
$SELLER->reviewsCount= Comment::countForSeller($SELLER->id); 
$SELLER->totalServices=count(SERVICE::getAllByUserId($SELLER->id));
$SELLER->totalOrders=count(JobOrder::getAllBySellerId($SELLER->id));
$SELLER->profilePic=file_exists("../images/users/{$SERVICE->sellerId}.jpg") ? "../images/users/{$SERVICE->sellerId}.jpg" :"/images/users/default.jpg";
}

$SERVICE->rating = Comment::averageForService($SERVICE->id) ?? 0;
$SERVICE->numRatings = Comment::countForService($SERVICE->id) ?? 0;
$SERVICE->category = Category::getById($SERVICE->categoryId) ?? null;
$SERVICE->comments = Comment::getByService($SERVICE->id) ?? null;
if($SERVICE->comments){
    foreach($SERVICE->comments as $comment){
        $comment->user=User::getUser($comment->buyerUserId)??null;
    }
}
$SERVICE->totalComments = Comment::countForService($SERVICE->id) ?? null;
$SERVICE->commentsToShow = 10;

$photos=getPhotos($SERVICE->id);
$SERVICE->photos = $photos;
$SERVICE->totalPhotos= count($photos);

drawHeader();
drawServicePage($SERVICE,$SELLER);
drawFooter();


function getPhotos($id) :array
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