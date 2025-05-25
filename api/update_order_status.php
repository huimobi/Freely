<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/joborder.class.php';

$session = Session::getInstance();
$user = $session->getUser();

if (!$user || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

$submitted = $_POST['csrf_token'] ?? '';
if (! $session->validateCsrfToken($submitted)) {http_response_code(403); echo "Invalid CSRF token"; exit;}

$orderId = $_POST['order_id'] ?? null;
$newStatus = $_POST['new_status'] ?? null;

$validStatuses = ['InProgress', 'Revision', 'Completed', 'Cancelled'];

if (!is_numeric($orderId) || !in_array($newStatus, $validStatuses)) {
    http_response_code(400);
    echo "Invalid input";
    exit;
}

try {
    JobOrder::updateStatus((int)$orderId, $newStatus);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo "Failed to update order status.";
}
