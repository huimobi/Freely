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
}
?>