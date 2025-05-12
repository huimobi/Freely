<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/user.class.php';

$session = Session::getInstance();
$current = $session->getUser();
if (!$current) {
  header('Location: /');
  exit;
}

$userId = (int)($_POST['user_id'] ?? 0);
if ($userId !== $current->id) {

  header('Location: /pages/edit_profile.php');
  exit;
}

$db = Database::getInstance();
$stmt = $db->prepare('DELETE FROM User WHERE UserId = ?');
$stmt->execute([$userId]);

$session->logout();

header('Location: /index.php');
exit;
