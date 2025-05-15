<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

class Comment {
  public int $id;
  public int $jobOrderId;
  public int $buyerUserId;
  public int $serviceId;
  public int $rating;
  public string $description;
  public string $commentDate;

  public function __construct(
    int $id,
    int $jobOrderId,
    int $buyerUserId,
    int $serviceId,
    int $rating,
    string $description,
    string $commentDate
  ) {
    $this->id = $id;
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
    return (int)$db->lastInsertId();
  }
  
  public static function getByService(int $id): ?Comment {
    $db = Database::getInstance();
    $stmt = $db->prepare('SELECT * FROM Comment WHERE ServiceId = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
      return new Comment(
        (int)$row['CommentId'],
        (int)$row['JobOrderId'],
        (int)$row['BuyerUserId'],
        (int)$row['ServiceId'],
        (int)$row['Rating'],
        (string)$row['Description'],
        (string)$row['CommentDate']
      );
    }
    return null;
  }

  public static function averageForService(int $serviceId): float {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT AVG(Rating) FROM Comment WHERE ServiceId = ?");
    $stmt->execute([$serviceId]);
    return round((float)$stmt->fetchColumn(), 1);
  }

  public static function countForService(int $serviceId): int {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT COUNT(*) FROM Comment WHERE ServiceId = ?");
    $stmt->execute([$serviceId]);
    return (int)$stmt->fetchColumn();
  }
}