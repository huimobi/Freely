<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

class Comment {
    
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