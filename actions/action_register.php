<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

$username  = trim($_POST['username'] ?? '');
$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';

if ($username && $firstName && $lastName && $email && $password) {
    
    $user = User::register( $username, $firstName, $lastName, $email, $password);
    if ($user !== null) Session::getInstance()->login($user);
}

header('Location: /');
exit;
