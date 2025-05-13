<?php
declare(strict_types=1);
require_once __DIR__ . '/database.php';

class Category {
  public int $id;
  public string $name;
  public float $avgRating;
  public int $serviceCount;

  public function __construct(int $id, string $name, float $avgRating, int $serviceCount) {
    $this->id = $id;
    $this->name = $name;
    $this->avgRating = $avgRating;
    $this->serviceCount = $serviceCount;
  }

  /** fetches all top-level categories + their avg rating and service count */
  public static function getAllWithStats(): array {
    $db = Database::getInstance();
    $sql = "SELECT c.CategoryId AS id, c.Name AS name, COALESCE(AVG(cm.Rating),0) AS avgRating, COUNT(s.ServiceId) AS serviceCount
      FROM Category c
      LEFT JOIN Service s
        ON s.CategoryId = c.CategoryId
      LEFT JOIN Comment cm
        ON cm.ServiceId = s.ServiceId
      WHERE c.ParentCategoryId IS NULL
      GROUP BY c.CategoryId, c.Name
      ORDER BY c.Name";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $out = [];
    while ($row = $stmt->fetch()) {
      $out[] = new Category(
        (int)$row['id'],
        (string)$row['name'],
        round((float)$row['avgRating'], 1),
        (int)$row['serviceCount']
      );
    }
    return $out;
  }
}
