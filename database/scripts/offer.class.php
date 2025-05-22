<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

class Offer {
    public int    $id;
    public int    $sellerId;
    public int    $buyerId;
    public int    $serviceId;
    public string $requirements;
    public float  $price;
    public string $currency;
    public string $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        int $id, int $sellerId, int $buyerId, int $serviceId,
        string $requirements, float $price, string $currency,
        string $status, string $createdAt, string $updatedAt
    ) {
        $this->id           = $id;
        $this->sellerId     = $sellerId;
        $this->buyerId      = $buyerId;
        $this->serviceId    = $serviceId;
        $this->requirements = $requirements;
        $this->price        = $price;
        $this->currency     = $currency;
        $this->status       = $status;
        $this->createdAt    = $createdAt;
        $this->updatedAt    = $updatedAt;
    }

    public static function create(int $sellerId, int $buyerId, int $serviceId, string $req, float $price, string $currency): void {
        $db = Database::getInstance();
        $stmt = $db->prepare(
        'INSERT INTO Offer (SellerUserId, BuyerUserId, ServiceId, Requirements, Price, Currency)
        VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$sellerId, $buyerId, $serviceId, $req, $price, $currency]);
    }

    public static function getBetween(int $userA, int $userB): array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
        'SELECT * FROM Offer
        WHERE (SellerUserId = :a AND BuyerUserId = :b)
            OR (SellerUserId = :b AND BuyerUserId = :a)
        ORDER BY CreatedAt ASC'
        );
        $stmt->execute([':a'=>$userA,':b'=>$userB]);
        $out = [];
        while ($row = $stmt->fetch()) {
        $out[] = new Offer(
            (int)$row['OfferId'],
            (int)$row['SellerUserId'],
            (int)$row['BuyerUserId'],
            (int)$row['ServiceId'],
            $row['Requirements'],
            (float)$row['Price'],
            $row['Currency'],
            $row['Status'],
            $row['CreatedAt'],
            $row['UpdatedAt']
        );
        }
        return $out;
    }

    public static function respond(int $offerId, string $newStatus): void {
        $db = Database::getInstance();
        $stmt = $db->prepare(
        'UPDATE Offer SET Status = ?, UpdatedAt = CURRENT_TIMESTAMP WHERE OfferId = ?'
        );
        $stmt->execute([$newStatus, $offerId]);
    }

    public static function getById(int $id): Offer {
        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM Offer WHERE OfferId = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
        throw new Exception("Offer #{$id} not found");
        }
        return new Offer(
        (int)$row['OfferId'],
        (int)$row['SellerUserId'],
        (int)$row['BuyerUserId'],
        (int)$row['ServiceId'],
        $row['Requirements'],
        (float)$row['Price'],
        $row['Currency'],
        $row['Status'],
        $row['CreatedAt'],
        $row['UpdatedAt']
        );
    }
}
