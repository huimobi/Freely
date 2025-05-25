<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/offer.class.php';

class JobOrder
{
    public int $id;
    public int $buyerId;
    public int $sellerId;
    public int $serviceId;
    public string $status;
    public string $orderDate;

    public function __construct(int $id, int $buyerId, int $sellerId, int $serviceId, string $status, string $orderDate)
    {
        $this->id = $id;
        $this->buyerId = $buyerId;
        $this->sellerId = $sellerId;
        $this->serviceId = $serviceId;
        $this->status = $status;
        $this->orderDate = $orderDate;
    }

    public static function create(int $serviceId, int $buyerId, int $sellerId, float $agreedPrice, string $currency): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO JobOrder(ServiceId, BuyerUserId, SellerUserId, AgreedPrice, Currency) 
VALUES (?, ?, ?, ?, ?);"
        );
        $stmt->execute([$serviceId, $buyerId, $sellerId, $agreedPrice, $currency]);
        return (int) $db->lastInsertId();
    }
    public static function getAllByBuyerId(int $buyerId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM JobOrder WHERE BuyerUserId = ? ORDER BY OrderDate DESC');
        $stmt->execute([$buyerId]);

        $orders = [];
        while ($row = $stmt->fetch()) {
            $orders[] = new JobOrder(
                $row['JobOrderId'],
                $row['BuyerUserId'],
                $row['SellerUserId'],
                $row['ServiceId'],
                $row['Status'],
                $row['OrderDate']
            );
        }

        return $orders;
    }

    public static function getAllBySellerId(int $sellerId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM JobOrder WHERE SellerUserId = ? ORDER BY OrderDate DESC');
        $stmt->execute([$sellerId]);

        $orders = [];
        while ($row = $stmt->fetch()) {
            $orders[] = new JobOrder(
                $row['JobOrderId'],
                $row['BuyerUserId'],
                $row['SellerUserId'],
                $row['ServiceId'],
                $row['Status'],
                $row['OrderDate']
            );
        }

        return $orders;
    }

    public static function updateStatus(int $orderId, string $newStatus): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE JobOrder SET Status = ? WHERE JobOrderId = ?');
        $stmt->execute([$newStatus, $orderId]);
    }

    public static function createFromOffer(Offer $offer): void {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO JobOrder (ServiceId, BuyerUserId, SellerUserId, AgreedPrice, Currency, Requirements) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$offer->serviceId, $offer->buyerId, $offer->sellerId, $offer->price, $offer->currency, $offer->requirements]);
    }
}
