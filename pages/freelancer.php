<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/photo.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/freelancer_page.tpl.php';
require_once __DIR__ . '/../templates/service_card.tpl.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';
require_once __DIR__ . '/../database/scripts/joborder.class.php';

$id = (int) ($_GET['id'] ?? 0);

$freelancer = User::getUser($id);

if (!$freelancer) {
    header('Location: /');
    exit;
}

$services = SERVICE::getAllByUserId($freelancer->id);
foreach ($services as $service) {
    $service->seller=USER::getUser($service->sellerId);
    $service->rating=COMMENT::averageForService($service->id);
    $service->numRatings=COMMENT::countForService($service->id);
}

$comments= COMMENT::getAllBySeller($freelancer->id);
foreach ($comments as $comment) {
    $comment->user = User::getUser($comment->buyerUserId) ?? null;
    $comment->userProfilePic = Photo::getUserProfilePic($comment->buyerUserId);
}

$freelancerInfo =[
    'freelancer' => $freelancer,
    'comments' => $comments,
    'rating' => COMMENT::averageForSeller($freelancer->id),
    'totalServices' => count(SERVICE::getAllByUserId($freelancer->id)),
    'totalOrders' => count(JobOrder::getAllBySellerId($freelancer->id)),
    'profilePic'  => PHOTO::getUserProfilePic($freelancer->id),
];

drawHeader();
drawFreelancerPage($freelancerInfo, $services);
drawFooter();

?>