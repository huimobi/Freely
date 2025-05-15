<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../templates/freelancer_page.tpl.php';
require_once __DIR__ . '/../templates/service_card.tpl.php';
require_once __DIR__ . '/../database/scripts/user.class.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../database/scripts/comment.class.php';

// Obter o ID do freelancer da URL
$id = (int) ($_GET['id'] ?? 0);

// Buscar dados do freelancer
$freelancer = User::getUser($id);

if (!$freelancer) {
    header('Location: /');
    exit;
}

// Verificar se é realmente um freelancer
if (!User::isFreelancer($id)) {
    header('Location: /');
    exit;
}

// Buscar serviços oferecidos pelo freelancer
$services = getFreelancerServices($id);

// Buscar avaliações do freelancer
$freelancer->reviews = getFreelancerReviews($id);
$freelancer->rating = calculateAverageRating($freelancer->reviews);
$freelancer->numReviews = count($freelancer->reviews);

drawHeader();
drawFreelancerPage($freelancer, $services);
drawFooter();

// Funções auxiliares

function getFreelancerServices(int $userId): array {
    $db = Database::getInstance();
    $stmt = $db->prepare(
        "SELECT ServiceId, SellerUserId, CategoryId, Title, Description,
                BasePrice, Currency, DeliveryDays, Revisions, IsActive, CreatedAt
         FROM Service 
         WHERE SellerUserId = ? AND IsActive = 1
         ORDER BY CreatedAt DESC"
    );
    $stmt->execute([$userId]);
    
    $services = [];
    while ($row = $stmt->fetch()) {
        $service = new Service(
            (int)$row['ServiceId'],
            (int)$row['SellerUserId'],
            (int)$row['CategoryId'],
            (string)$row['Title'],
            (string)$row['Description'],
            (float)$row['BasePrice'],
            (string)$row['Currency'],
            (int)$row['DeliveryDays'],
            (int)$row['Revisions'],
            (bool)$row['IsActive'],
            (string)$row['CreatedAt']
        );
        
        // Adicionar informações do vendedor
        $service->seller = User::getUser($service->sellerId);
        
        // Adicionar avaliações
        $service->rating = Comment::averageForService($service->id);
        $service->numRatings = Comment::countForService($service->id);
        
        $services[] = $service;
    }
    
    return $services;
}

function getFreelancerReviews(int $userId): array {
    $db = Database::getInstance();
    $stmt = $db->prepare(
        "SELECT c.CommentId, c.JobOrderId, c.BuyerUserId, c.ServiceId, 
                c.Rating, c.Description, c.CommentDate,
                u.FirstName, u.LastName, u.UserName
         FROM Comment c
         JOIN Service s ON c.ServiceId = s.ServiceId
         JOIN User u ON c.BuyerUserId = u.UserId
         WHERE s.SellerUserId = ?
         ORDER BY c.CommentDate DESC"
    );
    $stmt->execute([$userId]);
    
    $reviews = [];
    while ($row = $stmt->fetch()) {
        $review = (object)[
            'id' => (int)$row['CommentId'],
            'buyerId' => (int)$row['BuyerUserId'],
            'buyerName' => $row['UserName'],
            'serviceId' => (int)$row['ServiceId'],
            'rating' => (int)$row['Rating'],
            'text' => $row['Description'],
            'date' => $row['CommentDate']
        ];
        
        $reviews[] = $review;
    }
    
    return $reviews;
}

function calculateAverageRating(array $reviews): float {
    if (empty($reviews)) return 0.0;
    
    $sum = 0;
    foreach ($reviews as $review) {
        $sum += $review->rating;
    }
    
    return round($sum / count($reviews), 1);
}