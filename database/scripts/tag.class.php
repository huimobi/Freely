<?php
declare(strict_types=1);
require_once __DIR__ . '/database.php';

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

  public static function getServicesByPartialTag(string $tag, int $limit, int $offset, string $priceSort = '', string $ratingSort = ''): array {
    $db = Database::getInstance();

    if (!empty($ratingSort)) {
      $orderBy = "avgRating " . ($ratingSort === 'asc' ? 'ASC' : 'DESC');
    } elseif (!empty($priceSort)) {
      $orderBy = "BasePrice " . ($priceSort === 'asc' ? 'ASC' : 'DESC');
    } else {
      $orderBy = "s.CreatedAt DESC";
    }

    $stmt = $db->prepare("
      SELECT s.*, COALESCE(avgData.avgRating, 0) as avgRating
      FROM Service s
      JOIN ServiceTag st ON s.ServiceId = st.ServiceId
      JOIN Tag t ON t.TagId = st.TagId LEFT JOIN (
        SELECT ServiceId, AVG(Rating) as avgRating
        FROM Comment
        GROUP BY ServiceId
      ) avgData ON s.ServiceId = avgData.ServiceId
      WHERE t.Name LIKE ?
        AND s.IsActive = 1
      ORDER BY $orderBy
      LIMIT ? OFFSET ? ");
      
    $stmt->execute(['%' . $tag . '%', $limit, $offset]);

    $services = [];
    while ($row = $stmt->fetch()) {
      $services[] = new Service(
        (int)$row['ServiceId'],
        (int)$row['SellerUserId'],
        (int)$row['CategoryId'],
        $row['Title'],
        $row['Description'],
        (float)$row['BasePrice'],
        $row['Currency'],
        (int)$row['DeliveryDays'],
        (int)$row['Revisions'],
        (bool)$row['IsActive'],
        $row['CreatedAt']
      );
    }

    return $services;
  }

  public static function countServicesByPartialTag(string $term): int {
    $db = Database::getInstance();
    $stmt = $db->prepare("
      SELECT COUNT(DISTINCT s.ServiceId)
      FROM Service s
      JOIN ServiceTag st ON s.ServiceId = st.ServiceId
      JOIN Tag t ON t.TagId = st.TagId
      WHERE t.Name LIKE ?
        AND s.IsActive = 1
    ");
    $stmt->execute(['%' . $term . '%']);
    return (int)$stmt->fetchColumn();
  }

  public static function getTagsByPartial(string $query, int $limit = 10): array {
    $db = Database::getInstance();
    $stmt = $db->prepare('SELECT DISTINCT name FROM Tag WHERE name LIKE ? LIMIT ?');
    $stmt->execute(["%$query%", $limit]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }
}
?>