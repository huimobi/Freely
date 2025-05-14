<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../templates/common.tpl.php';
require_once __DIR__.'/../templates/edit_profile.tpl.php';

$session = Session::getInstance();
$user = $session->getUser();
if (!$user) {
  header('Location: /');
  exit;
}

$errors = $_SESSION['edit_errors'] ?? [];
unset($_SESSION['edit_errors']);

drawSimpleHeader();
drawEditProfileForm($user, $errors);
