<?php
declare(strict_types = 1);

require_once __DIR__ . '/database.php';

class Message {
  public int $id;
  public int $senderId;
  public int $receiverId;
  public string $content;
  public string $timestamp;

  public function __construct(int $id, int $senderId, int $receiverId, string $content, string $timestamp) {
    $this->id = $id;
    $this->senderId = $senderId;
    $this->receiverId = $receiverId;
    $this->content = $content;
    $this->timestamp = $timestamp;
  }

  public static function getConversation(int $user1, int $user2): array {
    $db = Database::getInstance();
    $stmt = $db->prepare('SELECT * FROM Message  WHERE (SenderUserId = ? AND ReceiverUserId = ?) OR (SenderUserId = ? AND ReceiverUserId = ?) ORDER BY Timestamp ASC ');
    $stmt->execute([$user1, $user2, $user2, $user1]);

    $messages = [];
    while ($msg = $stmt->fetch()) {
      $messages[] = new Message(
        intval($msg['MessageId']),
        intval($msg['SenderUserId']),
        intval($msg['ReceiverUserId']),
        $msg['Content'],
        $msg['Timestamp']
      );
    }
    return $messages;
  }

  public static function sendMessage(int $senderId, int $receiverId, string $content): void {
    $db = Database::getInstance();
    $stmt = $db->prepare('INSERT INTO Message (SenderUserId, ReceiverUserId, Content) VALUES (?, ?, ?)');
    $stmt->execute([$senderId, $receiverId, $content]);
  }
}