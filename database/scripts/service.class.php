<?php
    declare(strict_types = 1);
    require_once __DIR__ . '/database.php';
class Service {
    public int $id;
    public int $sellerId;
    public int $categoryId;
    public string $title;
    public string $description;
    public float $basePrice;
    public string $currency;
    public int $deliveryDays;
    public int $revisions;
    public bool $isActive;
    public string $createdAt;

  public function __construct(
    int  $id,
    int  $sellerId,
    int  $categoryId,
    string $title,
    string $description,
    float $basePrice,
    string $currency,
    int $deliveryDays,
    int $revisions,
    bool $isActive,
    string $createdAt
    ) {
        $this->id = $id;
        $this->sellerId = $sellerId;
        $this->categoryId = $categoryId;
        $this->title = $title;
        $this->description = $description;
        $this->basePrice = $basePrice;
        $this->currency = $currency;
        $this->deliveryDays = $deliveryDays;
        $this->revisions = $revisions;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt;
  }

  public static function create(
          int $sellerId,
          int $categoryId,
          string $title,
          string $description,
          float $basePrice,
          string $currency,
          int $deliveryDays,
          int $revisions
      ): int {
          $db = Database::getInstance();
          $stmt = $db->prepare("INSERT INTO Service ( SellerUserId, CategoryId, Title, Description, BasePrice, Currency, DeliveryDays, Revisions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
          $stmt->execute([ $sellerId, $categoryId, $title, $description, $basePrice, $currency, $deliveryDays, $revisions]);
          return (int)$db->lastInsertId();
  }

  public static function countByCategory(int $catId): int {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT COUNT(*) FROM Service WHERE CategoryId = :cat AND IsActive = 1");
    $stmt->execute([':cat' => $catId]);
    return (int)$stmt->fetchColumn();
  }

  public static function countAll(): int {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT COUNT(*) FROM Service WHERE IsActive = 1");
    $stmt->execute();
    return (int)$stmt->fetchColumn();
  }

  public static function getAll(int $limit, int $offset, string $priceSort = '', string $ratingSort = ''): array {
    $db = Database::getInstance();

    error_log("SORT DEBUG: price=$priceSort, rating=$ratingSort, order=$orderBy");

    if (!empty($ratingSort)) {
      $orderBy = "avgRating " . ($ratingSort === 'asc' ? 'ASC' : 'DESC');
    } elseif (!empty($priceSort)) {
      $orderBy = "BasePrice " . ($priceSort === 'asc' ? 'ASC' : 'DESC');
    } else {
      $orderBy = "s.CreatedAt DESC";
    }

    $stmt = $db->prepare("
      SELECT s.*, COALESCE(avgData.avgRating, 0) as avgRating
      FROM Service s LEFT JOIN (
        SELECT ServiceId, AVG(Rating) as avgRating
        FROM Comment
        GROUP BY ServiceId
      ) avgData ON s.ServiceId = avgData.ServiceId
      WHERE s.IsActive = 1
      ORDER BY $orderBy
      LIMIT :lim OFFSET :off ");

    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $services = [];
    while ($row = $stmt->fetch()) {
      $services[] = new self(
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

  public static function getByCategory(
      int $catId, int $limit, int $offset,
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

      $where = 's.CategoryId = :cat AND s.IsActive = 1';
      $params = [':cat' => $catId];

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

      $avgDataSubquery = "
          SELECT ServiceId, AVG(Rating) as avgRating
          FROM Comment
          GROUP BY ServiceId
      ";

      if (count($having)) {
          $avgDataSubquery .= " HAVING " . implode(' AND ', $having);
      }

      $stmt = $db->prepare("
          SELECT s.*, COALESCE(avgData.avgRating, 0) as avgRating
          FROM Service s
          LEFT JOIN (
              $avgDataSubquery
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
          $services[] = new self(
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


  public static function getTopRated(int $limit = 10): array {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT s.*, COALESCE(avgData.avgRating, 0) as avgRating FROM Service s LEFT JOIN ( SELECT ServiceId, AVG(Rating) as avgRating FROM Comment GROUP BY ServiceId ) avgData ON s.ServiceId = avgData.ServiceId ORDER BY avgRating DESC LIMIT ?");
    $stmt->execute([$limit]);

    $services = [];
    while ($row = $stmt->fetch()) {
        $services[] = new Service(
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
    }
    return $services;
  }

}
