<?php
declare(strict_types=1);

class Tag {
    
  public static function getOrCreateId(string $name): int {
    $db = Database::getInstance();
    $name = strtolower(trim($name));

    $stmt = $db->prepare("INSERT OR IGNORE INTO Tag (Name) VALUES (?)");
    $stmt->execute([$name]);

    $stmt = $db->prepare("SELECT TagId FROM Tag WHERE Name = ?");
    $stmt->execute([$name]);

    return (int)$stmt->fetchColumn();
  }

  public static function processTagsForService(int $serviceId, string $rawTags): void {
    $db = Database::getInstance();
    $tags = array_filter(array_map(fn($t) => strtolower(trim($t)), explode(',', $rawTags)));

    foreach ($tags as $tag) {
      $tagId = self::getOrCreateId($tag);

      $stmt = $db->prepare("INSERT OR IGNORE INTO ServiceTag (ServiceId, TagId) VALUES (?, ?)");
      $stmt->execute([$serviceId, $tagId]);
    }
  }

  public static function getServicesByPartialTag(string $tag): array {
    $db = Database::getInstance();
    $tag = strtolower(trim($tag));

    $stmt = $db->prepare("
        SELECT Service.* FROM Service
        JOIN ServiceTag ON Service.ServiceId = ServiceTag.ServiceId
        JOIN Tag ON Tag.TagId = ServiceTag.TagId
        WHERE Tag.Name LIKE ?
        GROUP BY Service.ServiceId
    ");
    $stmt->execute(['%' . $tag . '%']);

    $services = [];
    while ($row = $stmt->fetch()) {
        $service = new Service(
            $row['ServiceId'],
            $row['SellerUserId'],
            $row['CategoryId'],
            $row['Title'],
            $row['Description'],
            $row['BasePrice'],
            $row['Currency'],
            $row['DeliveryDays'],
            $row['Revisions'],
            (bool)$row['IsActive'],
            $row['CreatedAt']
        );
        $service->seller = User::getUser($row['SellerUserId']);

        $services[] = $service;
    }

    return $services;
}


}
?>