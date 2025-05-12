<?php
    declare(strict_types = 1);
    require_once __DIR__ . '/database.php';
    
    class FreeLancer {
    public int $userId;
    public string $headline;
    public string $description;
    

    public static function getByUserId(int $id): ?self {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT Headline, Description FROM FreeLancer WHERE UserId = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new self($id, (string)$row['Headline'], (string)$row['Description']);
    }

    public function __construct(int $userId, string $headline, string $description) {
        $this->userId     = $userId;
        $this->headline   = $headline;
        $this->description = $description;
    }
}