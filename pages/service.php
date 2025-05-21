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

$id = (int) $_GET['id'] ?? null;

$SERVICE = Service::getService($id);

if (!$SERVICE) {
    header('Location: /');
    exit;
}

$SERVICE->seller = User::getUser($SERVICE->sellerId);
$SERVICE->rating = Comment::averageForService($SERVICE->id);
$SERVICE->numRatings = Comment::countForService($SERVICE->id);
$SERVICE->category = Category::getById($SERVICE->categoryId);
$SERVICE->comments = Comment::getByService($SERVICE->id);
$SERVICE->totalComments = Comment::countForService($SERVICE->id);
$SERVICE->commentsToShow = 10;
$SERVICE->photos = getPhotos($SERVICE->id);

drawHeader();
drawServicePage($SERVICE);
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