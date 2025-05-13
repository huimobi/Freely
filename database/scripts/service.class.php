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

    public static function countByCategory(int $catId, string $priceFilter, string $deliveryFilter): int {
    $db = Database::getInstance();

    // build WHERE
    $where = ['CategoryId = :cat'];
    $params = [':cat' => $catId];

    // delivery filter: “1–3 days” or “7+”
    if ($deliveryFilter === '1') {
      $where[] = 'DeliveryDays BETWEEN 1 AND 3';
    } elseif ($deliveryFilter === '7') {
      $where[] = 'DeliveryDays >= 7';
    }

    $sql = 'SELECT COUNT(*) FROM Service
            WHERE ' . implode(' AND ', $where) . ' AND IsActive = 1';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
  }

  public static function getByCategory(
    int $catId, int $limit, int $offset,
    string $priceFilter, string $deliveryFilter
  ): array {
    $db = Database::getInstance();

    $where = ['CategoryId = :cat'];
    $params = [':cat' => $catId];

    if ($deliveryFilter === '1') {
      $where[] = 'DeliveryDays BETWEEN 1 AND 3';
    } elseif ($deliveryFilter === '7') {
      $where[] = 'DeliveryDays >= 7';
    }

    // sort clause
    $order = 'CreatedAt DESC';
    if ($priceFilter === 'low') {
      $order = 'BasePrice ASC';
    } elseif ($priceFilter === 'high') {
      $order = 'BasePrice DESC';
    }

    $sql = "
      SELECT ServiceId, SellerUserId, CategoryId, Title, Description,
             BasePrice, Currency, DeliveryDays, Revisions, IsActive, CreatedAt
      FROM Service
      WHERE " . implode(' AND ', $where) . " AND IsActive = 1
      ORDER BY $order
      LIMIT :lim OFFSET :off
    ";

    $stmt = $db->prepare($sql);
    foreach ($params as $k => $v) {
      $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':lim', $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $out = [];
    while ($row = $stmt->fetch()) {
      $out[] = new self(
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
    return $out;
  }
}
