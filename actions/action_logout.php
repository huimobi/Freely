<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

$session = Session::getInstance();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {http_response_code(405); exit('Method Not Allowed');}
$submitted = $_POST['csrf_token'] ?? '';
if (! $session->validateCsrfToken($submitted)) {http_response_code(403); exit('Invalid CSRF token');}

$session->logout();

header('Location: /');
exit;