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

  public static function getServicesByPartialTag(
      string $tag, int $limit, int $offset,
      string $sort = '', ?float $priceMin = null, ?float $priceMax = null,
      ?float $ratingMin = null, ?float $ratingMax = null
  ): array {
      $db = Database::getInstance();

      switch ($sort) {
          case 'price_asc':  $orderBy = 's.BasePrice ASC'; break;
          case 'price_desc': $orderBy = 's.BasePrice DESC'; break;
          case 'rating_asc': $orderBy = 'avgData.avgRating ASC'; break;
          case 'rating_desc': $orderBy = 'avgData.avgRating DESC'; break;
          default: $orderBy = 's.CreatedAt DESC';
      }

      $where = 's.IsActive = 1 AND t.Name LIKE :tag';
      $params = [':tag' => '%' . $tag . '%'];

      if ($priceMin !== null) {
          $where .= ' AND s.BasePrice >= :priceMin';
          $params[':priceMin'] = $priceMin;
      }
      if ($priceMax !== null) {
          $where .= ' AND s.BasePrice <= :priceMax';
          $params[':priceMax'] = $priceMax;
      }

      $having = [];
      if ($ratingMin !== null) {
          $having[] = 'AVG(Rating) >= :ratingMin';
          $params[':ratingMin'] = $ratingMin;
      }
      if ($ratingMax !== null) {
          $having[] = 'AVG(Rating) <= :ratingMax';
          $params[':ratingMax'] = $ratingMax;
      }

      $avgSubquery = "
          SELECT ServiceId, AVG(Rating) as avgRating
          FROM Comment
          GROUP BY ServiceId
      ";
      if (count($having)) {
          $avgSubquery .= ' HAVING ' . implode(' AND ', $having);
      }

      $stmt = $db->prepare("
          SELECT s.*, COALESCE(avgData.avgRating, 0) as avgRating
          FROM Service s
          JOIN ServiceTag st ON s.ServiceId = st.ServiceId
          JOIN Tag t ON t.TagId = st.TagId
          LEFT JOIN (
              $avgSubquery
          ) avgData ON s.ServiceId = avgData.ServiceId
          WHERE $where
          ORDER BY $orderBy
          LIMIT :lim OFFSET :off
      ");

      $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
      $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
      foreach ($params as $key => $value) {
          $stmt->bindValue($key, $value, PDO::PARAM_STR);
      }

      $stmt->execute();

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