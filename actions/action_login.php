<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email && $password ) {
    $user = User::getUserWithPassword($email, $password);
    if ($user !== null) Session::getInstance()->login($user);
}

header('Location: /');
exit;