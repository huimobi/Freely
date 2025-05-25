<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

class Comment
{
  public int $id;
  public int $jobOrderId;
  public int $buyerUserId;
  public int $serviceId;
  public int $rating;
  public string $description;
  public string $commentDate;

  public function __construct(
    int $jobOrderId,
    int $buyerUserId,
    int $serviceId,
    int $rating,
    string $description,
    string $commentDate
  ) {
    $this->jobOrderId = $jobOrderId;
    $this->buyerUserId = $buyerUserId;
    $this->serviceId = $serviceId;
    $this->rating = $rating;
    $this->description = $description;
    $this->commentDate = $commentDate;
  }


  public static function create(
    int $jobOrderId,
    int $buyerUserId,
    int $serviceId,
    int $rating,
    string $description
  ): int {
    $db = Database::getInstance();
    $stmt = $db->prepare("INSERT INTO Comment (JobOrderId, BuyerUserId, ServiceId, Rating, Description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$jobOrderId, $buyerUserId, $serviceId, $rating, $description]);
    return (int) $db->lastInsertId();
  }

  public static function getByService(int $id): array
  {
    $db = Database::getInstance();
    $stmt = $db->prepare('SELECT * FROM Comment WHERE ServiceId = ?');
    $stmt->execute([$id]);

    $services = [];
    while ($row = $stmt->fetch()) {
      $services[] = new Comment(
        (int) $row['JobOrderId'],
        (int) $row['BuyerUserId'],
        (int) $row['ServiceId'],
        (int) $row['Rating'],
        (string) $row['Description'],
        (string) $row['CommentDate']
      );
    }
    return $services;
  }

  public static function getAllBySeller(int $sellerId): array
  {
    $db = Database::getInstance();
    $stmt = $db->prepare('SELECT * from Comment Join Service on Comment.ServiceId=Service.ServiceId where Service.SellerUserId=?');
    $stmt->execute([$sellerId]);
    $comments = [];
    while ($row = $stmt->fetch()) {
      $comments[] = new Comment(
        (int) $row['JobOrderId'],
        (int) $row['BuyerUserId'],
        (int) $row['ServiceId'],
        (int) $row['Rating'],
        (string) $row['Description'],
        (string) $row['CommentDate']
      );
    }
    return $comments;
  }

  public static function averageForService(int $serviceId): float
  {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT AVG(Rating) FROM Comment WHERE ServiceId = ?");
    $stmt->execute([$serviceId]);
    return round((float) $stmt->fetchColumn(), 1);
  }

  public static function countForService(int $serviceId): int
  {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT COUNT(*) FROM Comment WHERE ServiceId = ?");
    $stmt->execute([$serviceId]);
    return (int) $stmt->fetchColumn();
  }

  public static function averageForSeller(int $sellerId): float
  {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT AVG(Rating) FROM Comment JOIN Service on Comment.ServiceId=Service.ServiceId WHERE Service.SellerUserId = ?");
    $stmt->execute([$sellerId]);
    return round((float) $stmt->fetchColumn(), 1);
  }

  public static function countForSeller(int $sellerId): int
  {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT Count(*) FROM Comment JOIN Service on Comment.ServiceId=Service.ServiceId WHERE Service.SellerUserId = ?");
    $stmt->execute([$sellerId]);
    return (int) $stmt->fetchColumn();
  }

  public static function hasComment($jobOrderId): bool
  {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM Comment WHERE Comment.jobOrderId=?");
    $stmt->execute([$jobOrderId]);
    return (bool) $stmt->fetchColumn();
  }
}