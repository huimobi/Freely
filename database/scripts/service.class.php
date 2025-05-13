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
}
