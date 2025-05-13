<?php
declare(strict_types=1);
require_once __DIR__ . '/database.php';

class Category {
  public int $id;
  public string $name;
  public string $description;
  public float $avgRating;
  public int $serviceCount;

  public function __construct(int $id, string $name, float $avgRating, int $serviceCount, string $description = '') {
    $this->id = $id;
    $this->name = $name;
    $this->avgRating = $avgRating;
    $this->serviceCount = $serviceCount;
    $this->description  = $description;
  }

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

  public static function getById(int $id): ?self {
    $db = Database::getInstance();
    $stmt = $db->prepare('
      SELECT c.CategoryId AS id,
            c.Name       AS name,
            c.Description,
            COALESCE(AVG(cm.Rating),0) AS avgRating,
            COUNT(s.ServiceId)       AS serviceCount
        FROM Category c
        LEFT JOIN Service s  ON s.CategoryId = c.CategoryId
        LEFT JOIN Comment cm ON cm.ServiceId    = s.ServiceId
      WHERE c.CategoryId = ?
      GROUP BY c.CategoryId, c.Name, c.Description
    ');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) return null;
    return new self(
      (int)   $row['id'],
      (string)$row['name'],
      (float) $row['avgRating'],
      (int)   $row['serviceCount']
    );
  }
}
