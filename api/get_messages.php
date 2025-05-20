<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/message.class.php';

$session = Session::getInstance();

if (!$session->isLoggedIn()) {echo json_encode(['status' => 'error', 'message' => 'User not logged in']); exit;}
if (!isset($_GET['with'])) {echo json_encode(['status' => 'error', 'message' => 'Missing conversation user id']); exit;}

$loggedUserId = $session->getUser()->id;
$otherUserId = intval($_GET['with']);

try {
  $conversation = Message::getConversation($loggedUserId, $otherUserId);

  $response = [];
  foreach ($conversation as $msg) {
    $response[] = [
      'senderId' => $msg->senderId,
      'receiverId' => $msg->receiverId,
      'content' => $msg->content,
      'timestamp' => $msg->timestamp
    ];
  }

  echo json_encode(['status' => 'success', 'messages' => $response]);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
