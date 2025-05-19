<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/message.class.php';

$session = Session::getInstance();

if (!$session->isLoggedIn()) { echo json_encode(['status' => 'error', 'message' => 'User not logged in']); exit;}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['selectedUserId'], $data['content'])) { echo json_encode(['status' => 'error', 'message' => 'Missing parameters']); exit;}

$senderId = $session->getUser()->id;
$receiverId = intval($data['selectedUserId']);
$content = trim($data['content']);

if ($content === '') { echo json_encode(['status' => 'error', 'message' => 'Message content cannot be empty']); exit;}

try {
  Message::sendMessage($senderId, $receiverId, $content);
  echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
