<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

$session = Session::getInstance();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {http_response_code(405); exit('Method Not Allowed');}
$submitted = $_POST['csrf_token'] ?? '';
if (! $session->validateCsrfToken($submitted)) {http_response_code(403); exit('Invalid CSRF token');}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if ($email === '') $errors[] = 'Please enter your email.';
if ($password === '') $errors[] = 'Please enter your password.';
if (empty($errors)) {
    $result = User::authenticate($email, $password);
    if ($result['status'] === 'success') {Session::getInstance()->login($result['user']);
    } else {
        $errors[] = $result['status'] === 'email_not_found' ? 'No account found with that email.' : 'Incorrect password.';
    }
}

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header('Location: /');
    exit;
}

header('Location: /');
exit;