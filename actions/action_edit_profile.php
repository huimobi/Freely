<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/photo.php';
require_once __DIR__.'/../database/scripts/user.class.php';

$session = Session::getInstance();

$submitted = $_POST['csrf_token'] ?? '';
if (!$session->validateCsrfToken($submitted)) {http_response_code(403); exit('Invalid CSRF token');}

$current = $session->getUser();
if (!$current) { header('Location: /'); exit;}

$userId = (int)($_POST['user_id'] ?? 0);
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name']  ?? '');
$email = trim($_POST['email']      ?? '');
$headline = trim($_POST['headline']   ?? '');
$description = trim($_POST['description']?? '');
$username = trim($_POST['username'] ?? '');
$newPassword = $_POST['new_password'] ?? '';

$errors = [];
  
// Common validations
if ($userId !== $current->id) $errors[] = 'Invalid session.';
if (strlen($firstName) < 1 || strlen($firstName) > 30) $errors[] = 'First name must be 1–30 characters.';
if (strlen($lastName) < 1 || strlen($lastName) > 30) $errors[] = 'Last name must be 1–30 characters.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
if ($email !== $current->email && User::emailExists($email)) $errors[] = 'That email is already taken.';
if (strlen($headline) > 200) $errors[] = 'Headline cannot exceed 200 characters.';
if (strlen($description) > 1000) $errors[] = 'Description cannot exceed 1000 characters.';

$user = User::getUser($userId);

if ($user && $username !== $user->userName) {
  if (User::usernameExists($username)) {
    $errors[] = 'Username already taken.';
  } else {
    $user->userName = $username;
  }
}

if ($errors) {
  $_SESSION['edit_errors'] = $errors;
  header('Location: /pages/edit_profile.php');
  exit;
}

if ($user) {
  $user->firstName = $firstName;
  $user->lastName = $lastName;
  $user->email = $email;
  $user->headline = $headline;
  $user->description = $description;

  PHOTO::setUserProfilePic($_FILES, $user->id);

  if (!empty($newPassword)) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $user->updatePassword($hashedPassword);
  }
  $user->save();
}

header('Location: /pages/edit_profile.php?success=1');
exit;